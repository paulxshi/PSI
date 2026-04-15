<?php
require '../../config/db.php';
header('Content-Type: application/json');

$scheduleId = isset($_GET['schedule_id']) ? (int) $_GET['schedule_id'] : 0;

if ($scheduleId <= 0) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "SELECT meal_id, name, price
         FROM meals
         WHERE schedule_id = :schedule_id
         ORDER BY name ASC"
    );

    $stmt->bindValue(':schedule_id', $scheduleId, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Failed to load meals.'
    ]);
}