<?php
require_once "../config/db.php";


date_default_timezone_set('Asia/Manila');


$pdo->exec("SET time_zone = '+08:00'");

$sql = "UPDATE schedules
SET status =
CASE
    WHEN DATE(scheduled_date) = CURDATE() THEN 'Incoming'
    WHEN DATE(scheduled_date) > CURDATE() THEN 'Incoming'
    WHEN DATE(scheduled_date) < CURDATE() THEN 'Completed'
END";

$stmt = $pdo->prepare($sql);
$stmt->execute();