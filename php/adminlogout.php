<?php
session_start();

require_once "../config/db.php";
require_once "log_activity.php";

if (isset($_SESSION['user_id'])) {
    $username = isset($_SESSION['first_name'], $_SESSION['last_name']) 
        ? $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] 
        : null;
    $email = $_SESSION['email'] ?? null;
    $role = $_SESSION['role'] ?? 'admin';
    
    logActivity('logout', 'Admin logged out', $_SESSION['user_id'], $username, $email, $role, 'info');
}

if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    unset($_SESSION['user_id']);
    unset($_SESSION['email']);
    unset($_SESSION['first_name']);
    unset($_SESSION['last_name']);
    unset($_SESSION['is_admin']);
    unset($_SESSION['role']);
}

header("Location: ../adminlogin.html");
exit;
?>
