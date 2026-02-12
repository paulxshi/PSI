<?php
require_once "../../config/db.php";


// Check if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form values and sanitize
    $region = trim($_POST['exam_region']);
    $venue_name = trim($_POST['exam_area']);
    $month = $_POST['exam_month'];
    $day = (int)$_POST['exam_day'];
    $year = (int)$_POST['exam_year'];
    $exam_limit = (int)$_POST['exam_limit'];

    // Convert month name to number
    $month_number = date("m", strtotime($month));

    // Build datetime string
    $schedule_datetime = sprintf("%04d-%02d-%02d 00:00:00", $year, $month_number, $day);

    try {
        // Start transaction
        $pdo->beginTransaction();

        // 1️⃣ Check if venue already exists
        $stmtCheck = $pdo->prepare("SELECT venue_id FROM venue WHERE venue_name = ? AND region = ?");
        $stmtCheck->execute([$venue_name, $region]);
        $existingVenue = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($existingVenue) {
            $venue_id = $existingVenue['venue_id'];
        } else {
            // 2️⃣ Insert new venue
            $stmtVenue = $pdo->prepare("INSERT INTO venue (venue_name, region) VALUES (?, ?)");
            $stmtVenue->execute([$venue_name, $region]);
            $venue_id = $pdo->lastInsertId();
        }

        // 3️⃣ Insert schedule
        $stmtSchedule = $pdo->prepare("INSERT INTO schedules (venue_id, schedule_datetime, num_of_examinees) VALUES (?, ?, ?)");
        $stmtSchedule->execute([$venue_id, $schedule_datetime, $exam_limit]);

        // Commit transaction
        $pdo->commit();

        echo '<div class="alert alert-success">Schedule successfully created!</div>';

    } catch (Exception $e) {
        $pdo->rollBack();
        echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }

    // Redirect to dashboard
    header("Location: ../dashboard.html");
    exit();
}