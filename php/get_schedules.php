<?php
require_once "../config/db.php";


if (isset($_GET['type'])) {

    if ($_GET['type'] == "regions") {

        $stmt = $pdo->query("SELECT DISTINCT region FROM venue ORDER BY region ASC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    if ($_GET['type'] == "venues" && isset($_GET['region'])) {

        $stmt = $pdo->prepare("SELECT venue_id, venue_name FROM venue WHERE region = ?");
        $stmt->execute([$_GET['region']]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    if ($_GET['type'] == "schedules" && isset($_GET['venue_id'])) {

        $stmt = $pdo->prepare("
            SELECT schedule_id, scheduled_date, price
            FROM schedules
            WHERE venue_id = ? AND status = 'Incoming'
            ORDER BY scheduled_date ASC
        ");
        $stmt->execute([$_GET['venue_id']]);
        
        $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Add exam_price field for backward compatibility
        foreach ($schedules as &$schedule) {
            $schedule['exam_price'] = $schedule['price'];
        }
        
        echo json_encode($schedules);
    }
}