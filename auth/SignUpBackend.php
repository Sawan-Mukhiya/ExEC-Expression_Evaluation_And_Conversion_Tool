<?php
header('Content-Type: application/json');
require_once '../backend/Database.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        throw new Exception('All fields are required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    $db = new Database();
    $conn = $db-> connect();

    // Check existing username/email
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->rowCount() > 0) {
        throw new Exception('Username or email already exists');
    }

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user
    $stmt = $conn->prepare("
        INSERT INTO users (username, email, password_hash) 
        VALUES (?, ?, ?)
    ");

    if ($stmt->execute([$username, $email, $passwordHash])) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Registration failed');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
