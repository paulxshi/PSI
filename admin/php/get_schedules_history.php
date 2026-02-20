<?php
require "../../config/db.php"; // this should return $conn as PDO

header('Content-Type: application/json');

try {

    $sql = "SELECT 
                s.schedule_id,
                s.scheduled_date,
                v.venue_name,
                v.region,
                s.num_registered,
                COUNT(CASE WHEN e.examinee_status = 'Completed' THEN 1 END) as num_completed
            FROM schedules s
            INNER JOIN venue v ON s.venue_id = v.venue_id
            LEFT JOIN examinees e ON s.schedule_id = e.schedule_id
            GROUP BY s.schedule_id, s.scheduled_date, v.venue_name, v.region, s.num_registered
            ORDER BY s.scheduled_date ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // PDO way of fetching rows
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);

} catch (PDOException $e) {

    echo json_encode([
        "error" => $e->getMessage()
    ]);
}
?>
