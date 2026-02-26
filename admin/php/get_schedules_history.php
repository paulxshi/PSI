<?php
require "../../config/db.php"; // this should return $conn as PDO

header('Content-Type: application/json');

try {

    // IMPROVED: Only count examinees with status 'Registered' or 'Completed'
    $sql = "SELECT 
    s.schedule_id,
    s.scheduled_date,
    v.venue_name,
    v.region,
    COUNT(DISTINCT CASE 
        WHEN e.examinee_status = 'Registered' 
        THEN e.examinee_id 
    END) AS num_registered,
    COUNT(DISTINCT CASE 
        WHEN e.examinee_status = 'Completed' 
        THEN e.examinee_id 
    END) AS num_completed
FROM schedules s
INNER JOIN venue v ON s.venue_id = v.venue_id
LEFT JOIN examinees e ON s.schedule_id = e.attended_schedule_id  -- <-- changed here
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
