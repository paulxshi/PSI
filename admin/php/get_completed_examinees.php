<?php
require "../../config/db.php";

header('Content-Type: application/json');

try {

    $sql = "SELECT 
                e.test_permit,
                CONCAT(u.first_name, ' ', 
                       IFNULL(u.middle_name, ''), ' ', 
                       u.last_name) AS full_name,
                u.email,
                s.scheduled_date
            FROM examinees e
            INNER JOIN users u ON e.user_id = u.user_id
            LEFT JOIN schedules s ON e.schedule_id = s.schedule_id
            WHERE e.examinee_status = :status
            ORDER BY s.scheduled_date ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['status' => 'Completed']);

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);

} catch (PDOException $e) {

    echo json_encode([
        "error" => $e->getMessage()
    ]);
}