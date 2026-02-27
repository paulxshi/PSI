<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode([
        'authenticated' => false,
        'message' => 'Unauthorized access. Please log in as admin.'
    ]);
    exit;
}

echo json_encode([
    'authenticated' => true,
    'user_id' => $_SESSION['user_id'] ?? null,
    'role' => $_SESSION['role'] ?? 'admin'
]);
