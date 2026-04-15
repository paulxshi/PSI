<?php
require "../../config/db.php";
header('Content-Type: application/json');

$sql = "SELECT 
            v.region,
            v.venue_id,
            v.venue_name,
            s.schedule_id,
            s.scheduled_date,
            COUNT(CASE WHEN p.status = 'PAID' THEN 1 END) AS num_paid,
            s.num_of_examinees
        FROM venue v
        LEFT JOIN schedules s ON s.venue_id = v.venue_id
        LEFT JOIN examinees e ON e.schedule_id = s.schedule_id
        LEFT JOIN payments p ON p.examinee_id = e.examinee_id
        WHERE s.scheduled_date >= CURDATE()
        GROUP BY v.region, v.venue_id, v.venue_name, s.schedule_id, s.scheduled_date, s.num_of_examinees
        ORDER BY v.region, v.venue_name, s.scheduled_date";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $region = $row['region'];
    $venue  = $row['venue_name'];

    if (!isset($data[$region])) $data[$region] = [];
    if (!isset($data[$region][$venue])) $data[$region][$venue] = [];

    $data[$region][$venue][] = [
        "schedule_id" => $row['schedule_id'],
        "date"        => $row['scheduled_date'],
        "registered"  => $row['num_paid'],
        "limit"       => $row['num_of_examinees']
    ];
}

echo json_encode($data);