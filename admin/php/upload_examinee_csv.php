<?php
// Handle CSV Upload to examinee_masterlist

// Buffer output so PHP warnings/notices don't corrupt the JSON response
ob_start();

// Catch fatal errors and return proper JSON instead of empty body
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        ob_end_clean();
        if (!headers_sent()) {
            header('Content-Type: application/json');
            http_response_code(500);
        }
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $error['message']]);
    }
});

header('Content-Type: application/json');
session_start();

// Performance: allow up to 5 minutes and 256 MB for large CSV uploads (≥5 000 rows)
set_time_limit(300);
ini_set('memory_limit', '256M');

// Helper: ensure a string is valid UTF-8 (handles Windows-1252/ANSI from Excel CSV exports)
function toUtf8($value) {
    if ($value === null || $value === '') return $value;
    if (mb_check_encoding($value, 'UTF-8')) return $value;
    $converted = @mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
    return ($converted !== false) ? $converted : mb_convert_encoding($value, 'UTF-8', 'UTF-8');
}

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    ob_end_clean();
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] !== UPLOAD_ERR_OK) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error']);
    exit;
}

$file = $_FILES['csvFile'];

// Validate file type by extension (finfo can be unreliable on WAMP / Windows)
$originalName = strtolower($file['name']);
$ext = pathinfo($originalName, PATHINFO_EXTENSION);

if ($ext !== 'csv') {
    // Also accept .txt since some Excel exports use it
    if ($ext !== 'txt') {
        ob_end_clean();
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid file type. CSV file required']);
        exit;
    }
}

// Double-check MIME type when finfo is available, but only as a secondary guard
if (function_exists('finfo_open')) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    if ($finfo !== false) {
        $detectedMime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $allowedMimes = [
            'text/csv', 'text/plain', 'text/x-csv', 'application/csv',
            'application/vnd.ms-excel', 'application/octet-stream'
        ];
        if ($detectedMime !== false && !in_array($detectedMime, $allowedMimes)) {
            ob_end_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid file type. CSV file required']);
            exit;
        }
    }
}

// Read CSV file — load entire content so we can normalize line endings before parsing.
// fgets/fgetcsv alone cannot handle \r-only (old Mac/Excel) line endings; reading the
// whole file first and splitting on a regex covers \r\n, \n, and \r uniformly.
$csvData = [];
$csvHeaders = []; // Track detected headers for error reporting
$fileOpenFailed = false;

$rawContent = @file_get_contents($file['tmp_name']);
if ($rawContent === false) {
    $fileOpenFailed = true;
} else {
    // Strip UTF-8 BOM (\xEF\xBB\xBF) that Excel prepends to CSV files
    $rawContent = ltrim($rawContent, "\xEF\xBB\xBF");

    // Normalize all line endings (\r\n or \r) to \n so we can split reliably
    $rawContent = str_replace("\r\n", "\n", $rawContent);
    $rawContent = str_replace("\r",   "\n", $rawContent);

    // Split into non-empty lines
    $lines = array_filter(explode("\n", $rawContent), function($l) {
        return trim($l) !== '';
    });
    $lines = array_values($lines);

    if (empty($lines)) {
        $fileOpenFailed = true; // treat empty file same as open failure
    } else {
        // Auto-detect delimiter from the header line
        $headerLine = $lines[0];
        $commaCount     = substr_count($headerLine, ',');
        $tabCount       = substr_count($headerLine, "\t");
        $semicolonCount = substr_count($headerLine, ';');

        $max = max($commaCount, $tabCount, $semicolonCount);
        if ($max === 0 || $commaCount === $max) {
            $delimiter = ',';
        } elseif ($tabCount === $max) {
            $delimiter = "\t";
        } else {
            $delimiter = ';';
        }

        // Parse each line with str_getcsv (handles quoted fields with embedded commas/newlines)
        $header = null;
        foreach ($lines as $lineIndex => $line) {
            $row = str_getcsv($line, $delimiter);

            if ($lineIndex === 0) {
                // Strip any remaining BOM fragment from the first cell
                $row[0] = ltrim($row[0], "\xEF\xBB\xBF");
                $header     = $row;
                $csvHeaders = $row;
                continue;
            }

            // Skip rows that are entirely empty
            if (empty(array_filter($row, fn($v) => trim($v) !== ''))) {
                continue;
            }

            // Normalize header names: lowercase + spaces/hyphens → underscores
            // so "Test Permit", "Last Name", "First Name" all map to snake_case keys
            $record = [];
            foreach ($header as $colIdx => $columnName) {
                $keyName = strtolower(trim($columnName));
                $keyName = preg_replace('/[\s\-]+/', '_', $keyName);
                $record[$keyName] = isset($row[$colIdx]) ? trim($row[$colIdx]) : '';
            }

            $csvData[] = $record;
        }
    }
}

// Guard: fail fast with a clear message if the file couldn't be read or had no data rows
if ($fileOpenFailed) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Could not open the uploaded file. Please try uploading again.']);
    exit;
}

if (empty($csvData)) {
    $headerInfo = empty($csvHeaders)
        ? 'No header row detected.'
        : 'Detected columns: ' . implode(' | ', $csvHeaders);
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'message' => 'No data rows found in the CSV file. ' . $headerInfo .
            ' — Required columns: test_permit (or "Test Permit"), last_name (or "Last Name"), first_name (or "First Name").'
    ]);
    exit;
}

// -------------------------------------------------------------------------
// Phase 1: Validate every row BEFORE touching the database.
// Errors and warnings are collected cheaply; valid rows go into $validRows
// for bulk INSERT in Phase 2.
// -------------------------------------------------------------------------
$successCount    = 0;
$errorCount      = 0;
$warningCount    = 0;
$errors          = [];
$detailedResults = []; // errors & warnings only — NOT every success row
$validRows       = []; // rows that passed validation, ready for bulk INSERT
$emailQueue      = []; // rows with an email that need a welcome e-mail

foreach ($csvData as $index => $record) {
    $rowNumber = $index + 2; // +2: 0-based index + 1 header row

    // Extract and sanitize fields
    $testPermit = preg_replace('/[\x00-\x1F\x7F]/u', '', toUtf8($record['test_permit'] ?? $record['testpermit'] ?? $record['permit'] ?? ''));
    $lastName   = trim(toUtf8($record['last_name']   ?? $record['lastname']   ?? ''));
    $firstName  = trim(toUtf8($record['first_name']  ?? $record['firstname']  ?? ''));
    $middleName = trim(toUtf8($record['middle_name'] ?? $record['middlename'] ?? ''));
    $email      = trim(toUtf8($record['email'] ?? ''));

    // Excel sometimes evaluates "2025-02-00001" as arithmetic, producing garbage.
    $permitSuspicious = !preg_match('/^\d{4}-\d{2,3}-\d{3,6}$/', $testPermit);

    $rowResult = [
        'row'         => $rowNumber,
        'test_permit' => $testPermit,
        'first_name'  => $firstName,
        'last_name'   => $lastName,
        'middle_name' => $middleName,
        'email'       => $email,
        'status'      => 'success',
        'error'       => null,
        'warning'     => $permitSuspicious
            ? 'Test permit format looks wrong — likely corrupted by Excel (expected YYYY-NN-NNNNN, got: ' . $testPermit . ')'
            : null,
    ];

    // --- required-field validation ---
    if (empty($testPermit)) {
        $errorCount++;
        $rowResult['status'] = 'error';
        $rowResult['error']  = 'Missing test permit number';
        $errors[]            = "Row $rowNumber: Missing test permit number";
        $detailedResults[]   = $rowResult;
        continue;
    }

    if (empty($lastName)) {
        $errorCount++;
        $rowResult['status'] = 'error';
        $rowResult['error']  = 'Missing last name';
        $errors[]            = "Row $rowNumber: Missing last name";
        $detailedResults[]   = $rowResult;
        continue;
    }

    if (empty($firstName)) {
        $errorCount++;
        $rowResult['status'] = 'error';
        $rowResult['error']  = 'Missing first name';
        $errors[]            = "Row $rowNumber: Missing first name";
        $detailedResults[]   = $rowResult;
        continue;
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorCount++;
        $rowResult['status'] = 'error';
        $rowResult['error']  = 'Invalid email format';
        $errors[]            = "Row $rowNumber: Invalid email format";
        $detailedResults[]   = $rowResult;
        continue;
    }

    // Keep warning rows visible in the results panel; still insert them.
    if ($permitSuspicious) {
        $warningCount++;
        $detailedResults[] = $rowResult;
    }

    // Use empty string, not null — the email column is NOT NULL in production.
    $validRows[] = [
        'test_permit' => $testPermit,
        'last_name'   => $lastName,
        'first_name'  => $firstName,
        'middle_name' => $middleName, // empty string is fine; avoids NOT NULL violation
        'email'       => $email,      // empty string is fine; avoids NOT NULL violation
        'row'         => $rowNumber,
    ];
}

// -------------------------------------------------------------------------
// Phase 2: Bulk INSERT valid rows in batches of 200.
// Reduces ~5 000 round-trips to ~25 queries.
// ON DUPLICATE KEY UPDATE preserves 'used' status and keeps the existing
// email when the new row has no email (IFNULL guard prevents blank-overwrite).
// -------------------------------------------------------------------------
$BATCH_SIZE = 200;

try {
    $pdo->beginTransaction();

    foreach (array_chunk($validRows, $BATCH_SIZE) as $batch) {
        $placeholders = implode(',', array_fill(0, count($batch), '(?,?,?,?,?,0)'));
        $sql = "
            INSERT INTO examinee_masterlist
                (test_permit, last_name, first_name, middle_name, email, used)
            VALUES $placeholders
            ON DUPLICATE KEY UPDATE
                last_name   = VALUES(last_name),
                first_name  = VALUES(first_name),
                middle_name = VALUES(middle_name),
                email       = IFNULL(NULLIF(VALUES(email), ''), email)
        ";
        // NOTE: 'used' is intentionally excluded from the UPDATE so an already-
        // registered examinee's admission status is never reset by a re-upload.

        $params = [];
        foreach ($batch as $row) {
            $params[] = $row['test_permit'];
            $params[] = $row['last_name'];
            $params[] = $row['first_name'];
            $params[] = $row['middle_name'];
            $params[] = $row['email'];
        }

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $successCount += count($batch);
            foreach ($batch as $row) {
                if ($row['email'] !== '') {
                    $emailQueue[] = $row;
                }
            }
        } catch (PDOException $batchEx) {
            // One bad value broke the batch — fall back to row-by-row so a
            // single problem row does not discard the other 199.
            $single = $pdo->prepare("
                INSERT INTO examinee_masterlist
                    (test_permit, last_name, first_name, middle_name, email, used)
                VALUES (?,?,?,?,?,0)
                ON DUPLICATE KEY UPDATE
                    last_name   = VALUES(last_name),
                    first_name  = VALUES(first_name),
                    middle_name = VALUES(middle_name),
                    email       = IFNULL(NULLIF(VALUES(email), ''), email)
            ");
            foreach ($batch as $row) {
                try {
                    $single->execute([
                        $row['test_permit'],
                        $row['last_name'],
                        $row['first_name'],
                        $row['middle_name'],
                        $row['email'],
                    ]);
                    $successCount++;
                    if ($row['email'] !== '') {
                        $emailQueue[] = $row;
                    }
                } catch (PDOException $rowEx) {
                    $errorCount++;
                    $errors[] = "Row {$row['row']} (permit {$row['test_permit']}): " . $rowEx->getMessage();
                    $detailedResults[] = [
                        'row'         => $row['row'],
                        'test_permit' => $row['test_permit'],
                        'first_name'  => $row['first_name'],
                        'last_name'   => $row['last_name'],
                        'middle_name' => $row['middle_name'],
                        'email'       => $row['email'],
                        'status'      => 'error',
                        'error'       => $rowEx->getMessage(),
                        'warning'     => null,
                    ];
                }
            }
        }
    }

    $pdo->commit();

    // -------------------------------------------------------------------------
    // Phase 3: Send welcome emails via n8n webhook.
    // Each recipient gets an individual POST (matching the existing n8n workflow
    // contract). cURL multi-handle fires all requests in parallel so the total
    // wait time is bounded by the slowest single response, not 5 000× RTT.
    // -------------------------------------------------------------------------
    $emailsSent   = 0;
    $emailsFailed = 0;

    if (!empty($emailQueue)) {
        $n8nWebhookUrl = 'https://n8n.srv1069938.hstgr.cloud/webhook/csv-welcome-email';

        $mh      = curl_multi_init();
        $handles = [];

        foreach ($emailQueue as $row) {
            $payload = [
                'email'       => $row['email'],
                'first_name'  => $row['first_name'],
                'last_name'   => $row['last_name'],
                'middle_name' => $row['middle_name'],
                'test_permit' => $row['test_permit'],
            ];
            $ch = curl_init($n8nWebhookUrl);
            if ($ch === false) {
                $emailsFailed++;
                continue;
            }
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_multi_add_handle($mh, $ch);
            $handles[] = $ch;
        }

        // Execute all requests concurrently
        $running = null;
        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
        } while ($running > 0);

        foreach ($handles as $ch) {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode === 200) {
                $emailsSent++;
            } else {
                $emailsFailed++;
                error_log("CSV Upload: welcome-email webhook failed (HTTP $httpCode)");
            }
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }

        curl_multi_close($mh);
        error_log("CSV Upload: welcome emails sent=$emailsSent failed=$emailsFailed");
    }

    $msg = "Imported $successCount record(s) successfully";
    if ($errorCount   > 0) $msg .= ", $errorCount error(s)";
    if ($warningCount > 0) $msg .= ", $warningCount permit(s) with suspicious format";

    ob_end_clean();
    echo json_encode([
        'success'         => true,
        'message'         => $msg,
        'successCount'    => $successCount,
        'errorCount'      => $errorCount,
        'warningCount'    => $warningCount,
        'errors'          => $errors,
        'detailedResults' => $detailedResults, // errors & warnings only
        'totalRows'       => count($csvData),
        'emailsSent'      => $emailsSent,
        'emailsFailed'    => $emailsFailed,
    ], JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_UNESCAPED_UNICODE);

} catch (\Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage(),
    ]);
}
?>
