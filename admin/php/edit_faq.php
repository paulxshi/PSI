<?php
header('Content-Type: application/json');

try {
    require_once '../../config/db.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    $faq_id = intval($_POST['faq_id'] ?? 0);
    $question = trim($_POST['question'] ?? '');
    $answer = trim($_POST['answer'] ?? '');

    if ($faq_id <= 0 || empty($question) || empty($answer)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE faqs SET question = ?, answer = ?, updated_at = CURRENT_TIMESTAMP WHERE faq_id = ?");
    $stmt->execute([$question, $answer, $faq_id]);
    
    echo json_encode(['success' => true, 'message' => 'FAQ updated successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>