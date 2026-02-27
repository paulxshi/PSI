<?php
session_start();

require_once "../config/db.php";
require_once "log_activity.php";

if (isset($_SESSION['user_id'])) {
    $username = isset($_SESSION['first_name'], $_SESSION['last_name']) 
        ? $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] 
        : null;
    $email = $_SESSION['email'] ?? null;
    $role = $_SESSION['role'] ?? 'examinee';
    
    logActivity('logout', 'User logged out', $_SESSION['user_id'], $username, $email, $role, 'info');
}

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    // Safe to clear examinee session variables
    unset($_SESSION['user_id']);
    unset($_SESSION['email']);
    unset($_SESSION['first_name']);
    unset($_SESSION['last_name']);
    unset($_SESSION['role']);
}

// Redirect to login page
header("Location: ../login.html");
exit;
?>
