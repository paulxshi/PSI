<?php
require_once "../../config/db.php";

header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("SELECT faq_id, question, answer FROM faqs ORDER BY faq_id ASC");
    $stmt->execute();

    $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($faqs);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}