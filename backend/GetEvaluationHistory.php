<?php
header('Content-Type: application/json');
require_once 'Database.php';

try {
    $db = new Database();
    $conn = $db->connect();
    
    session_start();
    if (!isset($_SESSION['user_id'])) die(json_encode([]));
    $history = $db->getEvaluationHistory($_SESSION['user_id']); 

    echo json_encode($history);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
