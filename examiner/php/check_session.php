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

// Block access for users with incomplete registration
// They should complete payment/schedule selection before accessing dashboard
if (isset($_SESSION['incomplete_registration']) && $_SESSION['incomplete_registration'] === true) {
    echo json_encode([
        'authenticated' => false,
        'message' => 'Please complete your registration and payment first.',
        'redirect' => '../auth/login.html'
    ]);
    exit;
}

// User is authenticated and fully registered
echo json_encode([
    'authenticated' => true,
    'user_id' => $_SESSION['user_id'],
    'email' => $_SESSION['email'] ?? null
]);
