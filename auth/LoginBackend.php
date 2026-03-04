<?php
session_start();
header('Content-Type: application/json');
require_once '../backend/Database.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $identifier = $data['identifier'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($identifier) || empty($password)) {
        throw new Exception('All fields are required');
    }

    $db = new Database();
    $conn = $db->connect();

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        throw new Exception('Invalid credentials');
    }

    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['username'];
    
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
