<?php
require_once "../../config/db.php";

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT venue_id, venue_name, region FROM venue");

    $venues = $stmt->fetchAll(); // already FETCH_ASSOC by default

    echo json_encode($venues);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
