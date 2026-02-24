<?php
// Separate session for accountants
session_name('PSI_ACCOUNTANT');
session_start();

require_once "../config/db.php";
require_once "log_activity.php";

// Log logout activity before clearing session
if (isset($_SESSION['user_id'])) {
    $username = isset($_SESSION['first_name'], $_SESSION['last_name']) 
        ? $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] 
        : null;
    $email = $_SESSION['email'] ?? null;
    $role = $_SESSION['role'] ?? 'accountant';
    
    logActivity('logout', 'Accountant logged out', $_SESSION['user_id'], $username, $email, $role, 'info');
}

// Only clear session if an accountant is logged in
if (isset($_SESSION['role']) && $_SESSION['role'] === 'accountant') {
    // Safe to clear accountant session variables
    unset($_SESSION['user_id']);
    unset($_SESSION['email']);
    unset($_SESSION['first_name']);
    unset($_SESSION['last_name']);
    unset($_SESSION['role']);
}

// Redirect to accountant login page
header("Location: ../accountant/login.html");
exit;
?>
