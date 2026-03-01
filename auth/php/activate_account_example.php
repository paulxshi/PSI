<?php
require_once __DIR__ . '/../config/db.php';

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
        
        $updateUserStmt = $pdo->prepare("
            UPDATE users 
            SET status = 'active' 
            WHERE user_id = ?
        ");
        $updateUserStmt->execute([$user_id]);
        
        if ($schedule_id) {
            $updateExamineeStmt = $pdo->prepare("
                UPDATE examinees 
                SET status = 'Scheduled', schedule_id = ?
                WHERE user_id = ?
            ");
            $updateExamineeStmt->execute([$schedule_id, $user_id]);
        } else {
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

?>
