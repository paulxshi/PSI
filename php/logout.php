<?php
session_start();

require_once "../config/db.php";
require_once "log_activity.php";

// Log logout activity before destroying session
if (isset($_SESSION['user_id'])) {
    $username = isset($_SESSION['first_name'], $_SESSION['last_name']) 
        ? $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] 
        : null;
    $email = $_SESSION['email'] ?? null;
    $role = $_SESSION['role'] ?? 'examinee';
    
    logActivity('logout', 'User logged out', $_SESSION['user_id'], $username, $email, $role, 'info');
}

// Destroy all session data
$_SESSION = [];
session_unset();
session_destroy();

// Optional: destroy session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Redirect to login page
header("Location: ../login.html");
exit;
?>
