<?php
header('Content-Type: application/json');

try {
    require_once "../../config/db.php";

    $stmt = $pdo->prepare("SELECT faq_id, question, answer, category FROM faqs ORDER BY faq_id DESC");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $faqs = [
        "registered" => [],
        "unregistered" => []
    ];

    foreach ($results as $row) {
        if ($row['category'] === 'registered') {
            $faqs['registered'][] = $row;
        } elseif ($row['category'] === 'unregistered') {
            $faqs['unregistered'][] = $row;
        }
    }

    echo json_encode($faqs);

} catch (Exception $e) {
    echo json_encode([
        "registered" => [],
        "unregistered" => []
    ]);
}

