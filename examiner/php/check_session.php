<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'authenticated' => false,
        'message' => 'No active session. Please log in.'
    ]);
    exit;
}

// User is authenticated
echo json_encode([
    'authenticated' => true,
    'user_id' => $_SESSION['user_id'],
    'email' => $_SESSION['email'] ?? null
]);
