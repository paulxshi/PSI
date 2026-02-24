<?php
header('Content-Type: application/json');

try {
    require_once '../../config/db.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    $faq_id = intval($_POST['faq_id'] ?? 0);

    if ($faq_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid FAQ ID']);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM faqs WHERE faq_id = ?");
    $stmt->execute([$faq_id]);
    
    echo json_encode(['success' => true, 'message' => 'FAQ deleted successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>