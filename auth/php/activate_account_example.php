<?php
/**
 * EXAMPLE: How to activate user account after payment confirmation
 * 
 * This should be called from your payment webhook (webhook.php)
 * after verifying that payment was successful
 */

require_once __DIR__ . '/../../config/db.php';

/**
 * Activate user account and update examinee status after payment
 * 
 * @param int $user_id - The user ID to activate
 * @param int $schedule_id - The schedule ID they selected (optional, can be set later)
 * @return bool - Success status
 */
function activateUserAccount($user_id, $schedule_id = null) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Update user status to 'active' - allows login
        $updateUserStmt = $pdo->prepare("
            UPDATE users 
            SET status = 'active' 
            WHERE user_id = ?
        ");
        $updateUserStmt->execute([$user_id]);
        
        // Update examinee status based on whether schedule is selected
        if ($schedule_id) {
            // If schedule is selected, set status to 'Scheduled'
            $updateExamineeStmt = $pdo->prepare("
                UPDATE examinees 
                SET status = 'Scheduled', schedule_id = ?
                WHERE user_id = ?
            ");
            $updateExamineeStmt->execute([$schedule_id, $user_id]);
        } else {
            // If schedule not yet selected, set to 'Awaiting Payment' -> 'Scheduled' after schedule selection
            $updateExamineeStmt = $pdo->prepare("
                UPDATE examinees 
                SET status = 'Awaiting Payment'
                WHERE user_id = ?
            ");
            $updateExamineeStmt->execute([$user_id]);
        }
        
        $pdo->commit();
        return true;
        
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log('Error activating account: ' . $e->getMessage());
        return false;
    }
}

/**
 * USAGE IN WEBHOOK:
 * 
 * After verifying payment is successful in webhook.php:
 * 
 * $user_id = $payment_data['user_id'];
 * $schedule_id = $payment_data['schedule_id'] ?? null;
 * 
 * if (activateUserAccount($user_id, $schedule_id)) {
 *     // Account activated successfully
 *     // User can now login
 * } else {
 *     // Error activating account
 *     error_log('Failed to activate account for user_id: ' . $user_id);
 * }
 */
?>
