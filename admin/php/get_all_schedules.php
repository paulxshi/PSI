<?php
session_start();
require_once "../../config/db.php";

header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit();
}

try {
    // Get all schedules with venue information
    $query = "
        SELECT 
            s.schedule_id,
            s.scheduled_date,
            s.num_of_examinees as capacity,
            s.num_registered,
            s.price,
            s.status,
            v.venue_name,
            v.region
        FROM schedules s
        INNER JOIN venue v ON s.venue_id = v.venue_id
        ORDER BY s.scheduled_date ASC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all meals grouped by schedule_id
    $stmtMeals = $pdo->prepare("SELECT meal_id, schedule_id, name, price FROM meals WHERE schedule_id IS NOT NULL");
    $stmtMeals->execute();
    $allMeals = $stmtMeals->fetchAll(PDO::FETCH_ASSOC);

    $mealsBySchedule = [];
    foreach ($allMeals as $meal) {
        $sid = (int)$meal['schedule_id'];
        $mealsBySchedule[$sid][] = [
            'meal_id' => (int)$meal['meal_id'],
            'name'    => $meal['name'],
            'price'   => (float)$meal['price']
        ];
    }

    // Format the data for frontend
    $formattedSchedules = [];
    foreach ($schedules as $schedule) {
        $datetime = new DateTime($schedule['scheduled_date']);
        $sid = (int)$schedule['schedule_id'];

        $formattedSchedules[] = [
            'schedule_id'    => $sid,
            'venue_name'     => $schedule['venue_name'],
            'region'         => $schedule['region'],
            'scheduled_date' => $schedule['scheduled_date'],
            'date'           => $datetime->format('F j, Y'),
            'day'            => $datetime->format('l'),
            'capacity'       => (int)$schedule['capacity'],
            'num_registered' => (int)$schedule['num_registered'],
            'price'          => (float)$schedule['price'],
            'status'         => $schedule['status'],
            'is_full'        => (int)$schedule['num_registered'] >= (int)$schedule['capacity'],
            'meals'          => $mealsBySchedule[$sid] ?? []
        ];
    }
    
    echo json_encode([
        'success' => true,
        'schedules' => $formattedSchedules,
        'total' => count($formattedSchedules)
    ]);
    
} catch (PDOException $e) {
    error_log('Error fetching schedules: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching schedules: ' . $e->getMessage()
    ]);
}
