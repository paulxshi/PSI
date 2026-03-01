<?php
/**
 * Payment System Requirements Checker
 * Run this on each XAMPP installation to diagnose issues
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment System Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .result { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .pass { background: #d4edda; border-left: 4px solid #28a745; }
        .fail { background: #f8d7da; border-left: 4px solid #dc3545; }
        .warn { background: #fff3cd; border-left: 4px solid #ffc107; }
        h1 { color: #333; }
        h2 { color: #555; margin-top: 30px; }
        code { background: #e9ecef; padding: 2px 5px; border-radius: 3px; }
        .info { color: #666; font-size: 0.9em; }
    </style>
</head>
<body>
    <h1>🔍 Payment System Diagnostic Tool</h1>
    <p class="info">This tool checks if your XAMPP setup has all requirements for the payment system.</p>

<?php

$allPassed = true;

// Check 1: PHP Version
echo "<h2>1. PHP Version</h2>";
$phpVersion = phpversion();
$phpOk = version_compare($phpVersion, '7.4', '>=');
if ($phpOk) {
    echo "<div class='result pass'>✅ PHP Version: $phpVersion (OK)</div>";
} else {
    echo "<div class='result fail'>❌ PHP Version: $phpVersion (Requires PHP 7.4+)</div>";
    $allPassed = false;
}

// Check 2: Required Extensions
echo "<h2>2. Required PHP Extensions</h2>";
$requiredExtensions = ['curl', 'json', 'pdo', 'pdo_mysql', 'mbstring', 'openssl'];

foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<div class='result pass'>✅ <code>$ext</code> extension is enabled</div>";
    } else {
        echo "<div class='result fail'>❌ <code>$ext</code> extension is NOT enabled</div>";
        echo "<div class='result warn'>⚠️ Enable <code>extension=$ext</code> in php.ini</div>";
        $allPassed = false;
    }
}

// Check 3: File Paths
echo "<h2>3. Required Files</h2>";
$currentDir = dirname(_FILE_);
$requiredFiles = [
    '../../config/db.php' => 'Database Configuration',
    '../../config/payment_config.php' => 'Payment Configuration',
    'create_payment.php' => 'Payment Creation Script'
];

foreach ($requiredFiles as $file => $desc) {
    $fullPath = $currentDir . '/' . $file;
    if (file_exists($fullPath)) {
        echo "<div class='result pass'>✅ $desc: <code>$file</code></div>";
    } else {
        echo "<div class='result fail'>❌ $desc NOT FOUND: <code>$fullPath</code></div>";
        $allPassed = false;
    }
}

// Check 4: Database Connection
echo "<h2>4. Database Connection</h2>";
try {
    require_once('../../config/db.php');
    if (isset($pdo) && $pdo instanceof PDO) {
        echo "<div class='result pass'>✅ Database connection successful</div>";
        
        // Check if payments table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'payments'");
        if ($stmt->rowCount() > 0) {
            echo "<div class='result pass'>✅ <code>payments</code> table exists</div>";
        } else {
            echo "<div class='result fail'>❌ <code>payments</code> table NOT FOUND</div>";
            echo "<div class='result warn'>⚠️ Run the SQL migration to create the table</div>";
            $allPassed = false;
        }
    } else {
        echo "<div class='result fail'>❌ Database connection failed (PDO not initialized)</div>";
        $allPassed = false;
    }
} catch (Exception $e) {
    echo "<div class='result fail'>❌ Database Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    $allPassed = false;
}

// Check 5: Payment Configuration
echo "<h2>5. Payment Configuration</h2>";
try {
    require_once('../../config/payment_config.php');
    
    if (defined('PAYMENT_MODE')) {
        echo "<div class='result pass'>✅ Payment mode: <strong>" . PAYMENT_MODE . "</strong></div>";
    } else {
        echo "<div class='result fail'>❌ PAYMENT_MODE not defined</div>";
        $allPassed = false;
    }
    
    if (defined('XENDIT_API_KEY')) {
        $keyPreview = substr(XENDIT_API_KEY, 0, 20) . '...';
        echo "<div class='result pass'>✅ Xendit API Key configured: <code>$keyPreview</code></div>";
    } else {
        echo "<div class='result fail'>❌ XENDIT_API_KEY not configured</div>";
        $allPassed = false;
    }
    
    if (defined('SUCCESS_REDIRECT_URL')) {
        echo "<div class='result pass'>✅ Success URL: <code>" . SUCCESS_REDIRECT_URL . "</code></div>";
    }
    
    if (defined('FAILURE_REDIRECT_URL')) {
        echo "<div class='result pass'>✅ Failure URL: <code>" . FAILURE_REDIRECT_URL . "</code></div>";
    }
    
} catch (Exception $e) {
    echo "<div class='result fail'>❌ Payment Config Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    $allPassed = false;
}

// Check 6: cURL Functionality
echo "<h2>6. cURL Test (Internet Connection)</h2>";
if (function_exists('curl_version')) {
    $curlVersion = curl_version();
    echo "<div class='result pass'>✅ cURL Version: {$curlVersion['version']}</div>";
    
    // Test connection to Xendit API
    $ch = curl_init('https://api.xendit.co/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($httpCode > 0) {
        echo "<div class='result pass'>✅ Successfully connected to Xendit API (HTTP $httpCode)</div>";
    } else {
        echo "<div class='result fail'>❌ Cannot connect to Xendit API</div>";
        if ($curlError) {
            echo "<div class='result warn'>⚠️ Error: $curlError</div>";
        }
        echo "<div class='result warn'>⚠️ Check internet connection or firewall settings</div>";
        $allPassed = false;
    }
} else {
    echo "<div class='result fail'>❌ cURL functions not available</div>";
    $allPassed = false;
}

// Check 7: PHP Error Logging
echo "<h2>7. PHP Error Logging</h2>";
$errorLog = ini_get('error_log');
$displayErrors = ini_get('display_errors');
$logErrors = ini_get('log_errors');

echo "<div class='result " . ($logErrors ? 'pass' : 'warn') . "'>";
echo ($logErrors ? '✅' : '⚠️') . " Error logging: " . ($logErrors ? 'Enabled' : 'Disabled') . "</div>";

echo "<div class='result " . (!$displayErrors ? 'pass' : 'warn') . "'>";
echo (!$displayErrors ? '✅' : '⚠️') . " Display errors: " . (!$displayErrors ? 'Off (Good)' : 'On (Should be Off)') . "</div>";

if ($errorLog) {
    echo "<div class='result pass'>✅ Error log file: <code>$errorLog</code></div>";
} else {
    echo "<div class='result warn'>⚠️ No error log file configured</div>";
}

// Check 8: Test create_payment.php directly
echo "<h2>8. Test Payment Script Execution</h2>";
echo "<div class='result warn'>⚠️ To test payment creation, you must be logged in</div>";
echo "<div class='result info'>📝 Check error logs if payment fails: <code>$errorLog</code></div>";

// Final Result
echo "<h2>Summary</h2>";
if ($allPassed) {
    echo "<div class='result pass'>";
    echo "<h3>✅ All checks passed!</h3>";
    echo "<p>Your XAMPP setup should support the payment system.</p>";
    echo "<p>If payment still fails, check:<br>";
    echo "1. Session is active (user logged in)<br>";
    echo "2. User status is 'Awaiting Payment'<br>";
    echo "3. PHP error logs for detailed errors</p>";
    echo "</div>";
} else {
    echo "<div class='result fail'>";
    echo "<h3>❌ Some checks failed</h3>";
    echo "<p>Fix the issues above and refresh this page.</p>";
    echo "</div>";
}

?>

<h2>📚 How to Fix Common Issues</h2>

<div class="result info">
    <h3>Enable PHP Extensions in XAMPP:</h3>
    <ol>
        <li>Open <code>C:\xampp\php\php.ini</code> in a text editor</li>
        <li>Find these lines and remove the <code>;</code> at the start:
            <ul>
                <li><code>;extension=curl</code> → <code>extension=curl</code></li>
                <li><code>;extension=openssl</code> → <code>extension=openssl</code></li>
                <li><code>;extension=pdo_mysql</code> → <code>extension=pdo_mysql</code></li>
            </ul>
        </li>
        <li>Save the file</li>
        <li>Restart Apache from XAMPP Control Panel</li>
        <li>Refresh this page to verify</li>
    </ol>
    
    <h3>View PHP Error Logs:</h3>
    <p><strong>Windows:</strong> <code>C:\xampp\php\logs\php_error_log</code></p>
    <p><strong>Apache Error Log:</strong> <code>C:\xampp\apache\logs\error.log</code></p>
    
    <h3>Enable Error Logging:</h3>
    <p>In <code>php.ini</code>, ensure:</p>
    <code>
        log_errors = On<br>
        error_log = "C:\xampp\php\logs\php_error_log"<br>
        display_errors = Off
    </code>
</div>

<p style="margin-top: 30px; color: #999; font-size: 0.85em;">
    Run this diagnostic on each XAMPP installation that has payment issues.
</p>

</body>
</html>