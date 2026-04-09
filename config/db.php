<?php

require_once __DIR__ . '/env_loader.php';

date_default_timezone_set('Asia/Manila');

$host = EnvLoader::get('DB_HOST', '127.0.0.1');
$db   = EnvLoader::get('DB_NAME', 'pmma_database');
$user = EnvLoader::get('DB_USER', 'root');
$pass = EnvLoader::get('DB_PASSWORD', '');

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec("SET time_zone = '+08:00'");

} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());

    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed.'
        ]);
        exit;
    }

    http_response_code(500);
    exit('Database connection failed.');
}