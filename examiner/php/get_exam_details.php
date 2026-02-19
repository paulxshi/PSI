<?php
require_once "../../config/db.php"; // your PDO connection

if (!isset($_GET['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Missing user ID"]);
    exit;
}

$user_id = $_GET['user_id'];

try {
    // Get the latest successful schedule of the user
$stmt = $pdo->prepare("
    SELECT 
        e.date_of_registration,
        s.scheduled_date AS date_of_test,
        v.venue_name,
        v.region
    FROM examinees e
    LEFT JOIN schedules s ON e.schedule_id = s.schedule_id
    LEFT JOIN venue v ON s.venue_id = v.venue_id
    WHERE e.user_id = :user_id
    ORDER BY e.date_of_registration DESC
    LIMIT 1
");


    $stmt->execute(['user_id' => $user_id]);
    $schedule = $stmt->fetch(PDO::FETCH_ASSOC);

if ($schedule) {
    echo json_encode([
        "status" => "success",
        "date_of_test" => $schedule['date_of_test'],
        "date_of_registration" => $schedule['date_of_registration'],
        "venue_name" => $schedule['venue_name'],
        "region" => $schedule['region']
    ]);
}
 else {
        echo json_encode([
            "status" => "error",
            "message" => "No exam details found"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
