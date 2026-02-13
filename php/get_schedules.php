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
            SELECT schedule_id, schedule_datetime
            FROM schedules
            WHERE venue_id = ? AND status = 'Incoming'
            ORDER BY schedule_datetime ASC
        ");
        $stmt->execute([$_GET['venue_id']]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}