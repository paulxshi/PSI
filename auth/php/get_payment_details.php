<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Session expired. Please log in again.'
    ]);
    exit;
}

require_once('../../config/db.php');

$user_id = $_SESSION['user_id'];

try {

    $query = "
        SELECT
            u.test_permit,
            CONCAT_WS(' ', u.first_name, NULLIF(u.middle_name, ''), u.last_name) AS full_name,
            e.status AS examinee_status,
            s.schedule_id,
            s.scheduled_date,
            s.price AS exam_price,
            v.venue_name,
            COALESCE(SUM(m.price), 0) AS meal_total

        FROM users u

        INNER JOIN examinees e
            ON e.user_id = u.user_id

        INNER JOIN schedules s
            ON s.schedule_id = e.schedule_id

        INNER JOIN venue v
            ON v.venue_id = s.venue_id

        LEFT JOIN examinee_meals em
            ON em.user_id = u.user_id

        LEFT JOIN meals m
            ON m.meal_id = em.meal_id
            AND m.schedule_id = s.schedule_id

        WHERE u.user_id = :user_id

        GROUP BY
            u.test_permit,
            u.first_name,
            u.middle_name,
            u.last_name,
            e.status,
            s.schedule_id,
            s.scheduled_date,
            s.price,
            v.venue_name
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo json_encode([
            'success' => false,
            'message' => 'No exam schedule found. Please select a schedule first.'
        ]);
        exit;
    }

    if ($result['examinee_status'] === 'Scheduled') {
        echo json_encode([
            'success' => false,
            'message' => 'Payment already completed. You can now login.'
        ]);
        exit;
    }

    if ($result['examinee_status'] !== 'Awaiting Payment') {
        echo json_encode([
            'success' => false,
            'message' => 'Please complete the previous steps first.'
        ]);
        exit;
    }

    $examPrice = (float)$result['exam_price'];
    $mealTotal = (float)$result['meal_total'];
    $totalPrice = $examPrice + $mealTotal;

    echo json_encode([
        'success' => true,
        'data' => [
            'test_permit' => $result['test_permit'],
            'full_name' => $result['full_name'],
            'scheduled_date' => $result['scheduled_date'],
            'venue_name' => $result['venue_name'],
            'exam_price' => $examPrice,
            'meal_total' => $mealTotal,
            'price' => $totalPrice
        ]
    ]);

} catch (PDOException $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>