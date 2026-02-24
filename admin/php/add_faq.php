<?php
header('Content-Type: application/json');

try {
    require_once '../../config/db.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        exit;
    }

    $question = trim($_POST['question'] ?? '');
    $answer = trim($_POST['answer'] ?? '');
    $category = trim($_POST['category'] ?? '');

    if (empty($question) || empty($answer) || empty($category)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    if (!in_array($category, ['registered', 'unregistered'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid category']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO faqs (question, answer, category) VALUES (?, ?, ?)");
    $stmt->execute([$question, $answer, $category]);
    
    echo json_encode(['success' => true, 'message' => 'FAQ added successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>