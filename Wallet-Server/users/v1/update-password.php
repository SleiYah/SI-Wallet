<?php
include(__DIR__ . "/../../models/users.php");
include(__DIR__ . "/../../connection/conn.php");
include(__DIR__ . "/../../utils/jwt-auth.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Only POST requests are allowed.'
    ]);
    exit;
}

$userData = authenticate();
if (!$userData) {
    echo json_encode([
        'success' => false, 
        'message' => 'Authentication failed'
    ]);
    exit;
}

$user_id = $userData->user_id;

$json_string = file_get_contents('php://input');
$data = json_decode($json_string, true);

$current_password = $data['current_password'] ?? '';
$new_password = $data['new_password'] ?? '';

if (empty($current_password) || empty($new_password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Current password and new password are required.'
    ]);
    exit;
}

$query = "SELECT password_hash FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    echo json_encode([
        'success' => false,
        'message' => 'User not found'
    ]);
    exit;
}

$user = $result->fetch_assoc();
$stored_hash = $user['password_hash'];

if (!password_verify($current_password, $stored_hash)) {
    echo json_encode([
        'success' => false,
        'message' => 'Current password is incorrect'
    ]);
    exit;
}

$new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
$update_query = "UPDATE users SET password_hash = ? WHERE user_id = ?";
$update_stmt = $conn->prepare($update_query);
$update_stmt->bind_param("si", $new_password_hash, $user_id);

if ($update_stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Password updated successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to update password: ' . $conn->error
    ]);
}