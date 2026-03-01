<?php
require "../../config/db.php"; 

header('Content-Type: application/json');

try {

    $sql = "SELECT 
    s.schedule_id,
    s.scheduled_date,
    v.venue_name,
    v.region,

    COUNT(DISTINCT CASE 
        WHEN e.examinee_status = 'Registered'
             AND e.schedule_id = s.schedule_id
        THEN e.examinee_id 
    END) AS num_registered,

    COUNT(DISTINCT CASE 
        WHEN e.examinee_status = 'Completed'
             AND e.attended_schedule_id = s.schedule_id
        THEN e.examinee_id 
    END) AS num_completed

FROM schedules s
INNER JOIN venue v ON s.venue_id = v.venue_id
LEFT JOIN examinees e 
    ON (e.schedule_id = s.schedule_id 
        OR e.attended_schedule_id = s.schedule_id)

GROUP BY s.schedule_id, s.scheduled_date, v.venue_name, v.region
ORDER BY s.scheduled_date ASC;";

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
