<?php
require_once "../../config/db.php";

$schedule_id = isset($_GET['schedule_id']) ? intval($_GET['schedule_id']) : 0;

if (!$schedule_id) {
    http_response_code(400);
    echo "Invalid schedule ID.";
    exit;
}

$sql = "
SELECT
    u.test_permit,
    CONCAT(u.last_name, ', ', u.first_name, ' ', COALESCE(u.middle_name, '')) AS full_name,
    u.email,
    u.contact_number,
    u.school,
    m.name        AS meal_name,
    m.price       AS meal_price,
    p.amount      AS amount_paid,
    p.paid_at,
    p.channel     AS payment_channel,
    v.venue_name,
    s.scheduled_date
FROM payments p
INNER JOIN users u           ON u.user_id        = p.user_id
INNER JOIN examinees e       ON e.examinee_id    = p.examinee_id
INNER JOIN schedules s       ON s.schedule_id    = e.schedule_id
INNER JOIN venue v           ON v.venue_id       = s.venue_id
INNER JOIN examinee_meals em ON em.user_id       = u.user_id
INNER JOIN meals m           ON m.meal_id        = em.meal_id
WHERE p.status = 'PAID'
  AND e.schedule_id = :schedule_id
ORDER BY u.last_name ASC
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':schedule_id' => $schedule_id]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $filename = "paid_meals_schedule_{$schedule_id}_" . date('Y-m-d') . ".csv";

    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=\"{$filename}\"");

    $output = fopen('php://output', 'w');

    fputcsv($output, [
        'Test Permit',
        'Full Name',
        'Email',
        'Contact Number',
        'School',
        'Meal',
        'Meal Price',
        'Amount Paid',
        'Paid At',
        'Payment Channel',
        'Venue',
        'Exam Date'
    ]);

    foreach ($rows as $row) {
        fputcsv($output, [
            $row['test_permit'],
            $row['full_name'],
            $row['email'],
            $row['contact_number'],
            $row['school'],
            $row['meal_name'],
            $row['meal_price'],
            $row['amount_paid'],
            $row['paid_at'],
            $row['payment_channel'],
            $row['venue_name'],
            $row['scheduled_date']
        ]);
    }

    fclose($output);

} catch (PDOException $e) {
    http_response_code(500);
    echo "Database error: " . $e->getMessage();
}
exit;