<?php
include(__DIR__ . "/../../models/users.php");
include(__DIR__ . "/../../connection/conn.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Only GET requests are allowed.'
    ]);
    exit;
}

$user = new User();

if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $userId = $_GET['user_id'];
    
    if (!is_numeric($userId)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid user ID format'
        ]);
        exit;
    }
    
    $userData = $user->read($userId);
    
    if ($userData) {
        unset($userData['password_hash']);
        
        echo json_encode([
            'success' => true,
            'data' => $userData
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
    }
} 
else {
    $query = "SELECT * FROM users ORDER BY created_at DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        // Remove sensitive information
        $users[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $users
    ]);
}