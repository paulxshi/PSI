<?php
require_once "../config/db.php";


if (isset($_GET['type'])) {

    if ($_GET['type'] == "regions") {

        $stmt = $pdo->query("SELECT DISTINCT region FROM venue ORDER BY region ASC");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    if ($_GET['type'] == "venues" && isset($_GET['region'])) {

        $stmt = $pdo->prepare("
            SELECT DISTINCT v.venue_id, v.venue_name 
            FROM venue v
            INNER JOIN schedules s ON v.venue_id = s.venue_id
            WHERE v.region = ? 
                AND s.status = 'Incoming'
                AND s.scheduled_date >= CURDATE()
                AND COALESCE(s.num_registered, 0) < s.num_of_examinees
            ORDER BY v.venue_name ASC
        ");
        $stmt->execute([$_GET['region']]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    if ($_GET['type'] == "schedules" && isset($_GET['venue_id'])) {

        $stmt = $pdo->prepare("
            SELECT 
                schedule_id, 
                scheduled_date, 
                price,
                num_of_examinees,
                COALESCE(num_registered, 0) as num_registered,
                (num_of_examinees - COALESCE(num_registered, 0)) as available_slots
            FROM schedules
            WHERE venue_id = ? 
                AND status = 'Incoming'
                AND scheduled_date >= CURDATE()
                AND COALESCE(num_registered, 0) < num_of_examinees
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