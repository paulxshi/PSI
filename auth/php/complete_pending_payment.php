<?php
/**
 * Complete Pending Payment (Server-Side)
 * ======================================
 * This endpoint handles payment completion securely on the server side,
 * verifying the invoice status with Xendit API before updating the database.
 * This avoids exposing webhook tokens to the client.
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

require_once('../../config/db.php');
require_once('../../config/payment_config.php');

$user_id = $_SESSION['user_id'];

try {
    // 1. Get the pending payment for this user
    $query = "SELECT payment_id, xendit_invoice_id, external_id, examinee_id, amount 
              FROM payments 
              WHERE user_id = :user_id 
              AND status = 'PENDING'
              ORDER BY created_at DESC 
              LIMIT 1";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $payment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$payment) {
        echo json_encode(['success' => false, 'message' => 'No pending payment found']);
        exit;
    }

    $invoiceId  = $payment['xendit_invoice_id'];
    $examineeId = $payment['examinee_id'];

    // 2. Verify invoice status with Xendit API
    $invoiceUrl = XENDIT_INVOICE_URL . '/' . $invoiceId;

    $ch = curl_init($invoiceUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Basic ' . base64_encode(XENDIT_API_KEY . ':')
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $invoiceResponse = curl_exec($ch);
    $invoiceHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError       = curl_error($ch);
    curl_close($ch);

    if ($invoiceHttpCode !== 200) {
        error_log("[COMPLETE PAYMENT] Xendit API error (HTTP $invoiceHttpCode): $invoiceResponse | cURL: $curlError");
        echo json_encode([
            'success' => false,
            'message' => 'Could not verify payment with Xendit (HTTP ' . $invoiceHttpCode . ')'
        ]);
        exit;
    }

    $invoiceData = json_decode($invoiceResponse, true);

    if (!$invoiceData || !isset($invoiceData['status'])) {
        error_log("[COMPLETE PAYMENT] Invalid Xendit response: $invoiceResponse");
        echo json_encode(['success' => false, 'message' => 'Invalid response from Xendit']);
        exit;
    }

    $xenditStatus = strtoupper($invoiceData['status']);

    // If the invoice is not PAID on Xendit's side, reject
    if ($xenditStatus !== 'PAID') {
        // Update local status to match Xendit (e.g. EXPIRED)
        if (in_array($xenditStatus, ['EXPIRED', 'CANCELLED'])) {
            $updateStmt = $pdo->prepare("UPDATE payments SET status = :status, updated_at = NOW() WHERE payment_id = :pid");
            $updateStmt->execute([':status' => $xenditStatus, ':pid' => $payment['payment_id']]);
        }

        echo json_encode([
            'success' => false,
            'message' => 'Payment is not yet paid. Current status: ' . $xenditStatus
        ]);
        exit;
    }

    // 3. Determine payment method from Xendit invoice data
    $paymentMethod = 'UNKNOWN';

    $xenditPayment = $invoiceData['payments'][0] ?? null;
    if ($xenditPayment) {
        $method      = $xenditPayment['payment_method'] ?? null;
        $methodId    = $xenditPayment['payment_method_id'] ?? null;
        $ewalletType = $xenditPayment['ewallet_type'] ?? null;
        $channelCode = $xenditPayment['channel_code'] ?? null;
        $cardBrand   = $xenditPayment['card_brand'] ?? null;

        if (!empty($methodId)) {
            $paymentMethod = strtoupper($methodId);
        } elseif (!empty($ewalletType)) {
            $paymentMethod = strtoupper($method) . ' - ' . strtoupper($ewalletType);
        } elseif (!empty($channelCode)) {
            $paymentMethod = strtoupper($method) . ' - ' . strtoupper($channelCode);
        } elseif (!empty($cardBrand)) {
            $paymentMethod = strtoupper($method) . ' - ' . strtoupper($cardBrand);
        } elseif (!empty($method)) {
            $paymentMethod = strtoupper($method);
        }
    }

    if ($paymentMethod === 'UNKNOWN' && !empty($invoiceData['payment_method'])) {
        $paymentMethod = strtoupper($invoiceData['payment_method']);
    }

    error_log("[COMPLETE PAYMENT] Verified PAID for user_id: $user_id, invoice: $invoiceId, method: $paymentMethod");

    // 4. Begin transaction
    $pdo->beginTransaction();

    // 4a. Update payments table
    $updatePaymentQuery = "UPDATE payments 
                           SET status = 'PAID', 
                               paid_at = NOW(), 
                               updated_at = NOW(),
                               xendit_response = :xendit_response,
                               channel = :payment_method
                           WHERE payment_id = :payment_id 
                           AND status = 'PENDING'";

    $stmt = $pdo->prepare($updatePaymentQuery);
    $stmt->bindParam(':xendit_response', $invoiceResponse, PDO::PARAM_STR);
    $stmt->bindParam(':payment_method', $paymentMethod, PDO::PARAM_STR);
    $stmt->bindParam(':payment_id', $payment['payment_id'], PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Payment already processed or not found']);
        exit;
    }

    // 4b. Update examinees table
    $updateExamineeQuery = "UPDATE examinees 
                            SET status = 'Scheduled', 
                                examinee_status = 'Registered',
                                updated_at = NOW() 
                            WHERE examinee_id = :examinee_id 
                            AND status = 'Awaiting Payment'";

    $stmt = $pdo->prepare($updateExamineeQuery);
    $stmt->bindParam(':examinee_id', $examineeId, PDO::PARAM_INT);
    $stmt->execute();

    // 4c. Update users table
    $updateUserQuery = "UPDATE users 
                        SET status = 'active', 
                            updated_at = NOW() 
                        WHERE user_id = :user_id 
                        AND status = 'incomplete'";

    $stmt = $pdo->prepare($updateUserQuery);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // 4d. Update examinee_meals payment status to 'paid'
    $updateMealsQuery = "
        UPDATE examinee_meals 
        SET payment_status = 'paid'
        WHERE user_id = :user_id
        AND payment_status = 'unpaid'
    ";

    $stmt = $pdo->prepare($updateMealsQuery);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Commit transaction
    $pdo->commit();

    error_log("[COMPLETE PAYMENT] Payment completed successfully for user_id: $user_id, invoice: $invoiceId");

    // 5. Send payment confirmation email (non-blocking — don't fail if email fails)
    try {
        $emailQuery = "
            SELECT 
                u.email, u.first_name, u.last_name, u.test_permit,
                s.scheduled_date,
                v.venue_name, v.region
            FROM users u
            INNER JOIN examinees e ON u.user_id = e.user_id
            INNER JOIN schedules s ON e.schedule_id = s.schedule_id
            INNER JOIN venue v ON s.venue_id = v.venue_id
            WHERE u.user_id = :user_id
        ";

        $emailStmt = $pdo->prepare($emailQuery);
        $emailStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $emailStmt->execute();
        $userDetails = $emailStmt->fetch(PDO::FETCH_ASSOC);

        if ($userDetails && !empty($userDetails['email'])) {
            $dateObj       = new DateTime($userDetails['scheduled_date']);
            $formattedDate = $dateObj->format('F j, Y');

            $emailData = [
                'email'          => $userDetails['email'],
                'first_name'     => $userDetails['first_name'],
                'last_name'      => $userDetails['last_name'],
                'test_permit'    => $userDetails['test_permit'],
                'scheduled_date' => $formattedDate,
                'venue_name'     => $userDetails['venue_name'],
                'region'         => $userDetails['region']
            ];

            $n8nWebhookUrl = 'https://n8n.srv1069938.hstgr.cloud/webhook/payment-confirmation';

            $ch = curl_init($n8nWebhookUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $emailResponse = curl_exec($ch);
            $emailHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($emailHttpCode === 200) {
                error_log("[COMPLETE PAYMENT] Confirmation email sent to: {$userDetails['email']}");
            } else {
                error_log("[COMPLETE PAYMENT] Failed to send email to: {$userDetails['email']} (HTTP: $emailHttpCode)");
            }
        }
    } catch (Exception $emailError) {
        error_log("[COMPLETE PAYMENT] Email error: " . $emailError->getMessage());
    }

    // 6. Return success
    echo json_encode([
        'success'    => true,
        'message'    => 'Payment completed successfully',
        'user_id'    => $user_id,
        'invoice_id' => $invoiceId
    ]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("[COMPLETE PAYMENT] DB Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("[COMPLETE PAYMENT] Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
