<?php
require "../../config/db.php";

header('Content-Type: application/json');

$sql = "SELECT
            v.region,
            v.venue_id,
            v.venue_name,
            s.scheduled_date,
            s.num_registered,
            s.num_of_examinees
        FROM venue v
        LEFT JOIN schedules s ON s.venue_id = v.venue_id
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
        "date" => $row['scheduled_date'],
        "registered" => $row['num_registered'],
        "limit" => $row['num_of_examinees']
    ];
}

echo json_encode($data);
