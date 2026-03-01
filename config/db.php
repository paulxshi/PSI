<?php
$host = '127.0.0.1';
$db   = 'u679207742_pmma';
$user = 'u679207742_User_pmma';
$pass = 'gZ3*FuQ@2DZ#';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // If this is called from an API endpoint expecting JSON
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
        exit;
    }
    // Otherwise output plain text
    http_response_code(500);
    exit('Database connection failed.');
}