<?php
require "../../config/db.php"; // this should return $conn as PDO

header('Content-Type: application/json');

try {

    $sql = "SELECT 
                s.schedule_id,
                s.scheduled_date,
                s.num_registered,
                s.num_completed,
                v.venue_name,
                v.region
            FROM schedules s
            INNER JOIN venue v ON s.venue_id = v.venue_id
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
