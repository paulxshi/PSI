<?php
/**
 * Check if user has an active registration session
 * Used by frontend pages to verify user authentication
 */

session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'authenticated' => true,
        'user_id' => $_SESSION['user_id'],
        'registration_flow' => isset($_SESSION['registration_flow']) ? $_SESSION['registration_flow'] : false,
        'test_permit' => isset($_SESSION['test_permit']) ? $_SESSION['test_permit'] : null
    ]);
} else {
    echo json_encode([
        'authenticated' => false,
        'message' => 'No active session. Please log in or complete registration.'
    ]);
}
