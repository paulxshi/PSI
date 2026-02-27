<?php
session_name('PSI_ACCOUNTANT');
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Not authenticated',
        'redirect' => '../auth/login.html'
    ]);
    exit;
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['accountant', 'admin'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access - Accountant access required',
        'redirect' => '../landing.html'
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'user' => [
        'username' => $_SESSION['username'] ?? 'User',
        'email' => $_SESSION['email'] ?? '',
        'role' => $_SESSION['role']
    ]
]);
