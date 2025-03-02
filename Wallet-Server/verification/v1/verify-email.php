<?php
include(__DIR__ . "/../../connection/conn.php");
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];
    
    $query = "SELECT * FROM users WHERE email_token = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid token'
        ]);
        exit;
    }
    
    $user = $result->fetch_assoc();
    $userId = $user['user_id'];
        
    $updateQuery = "UPDATE users SET tier = 2, email_token = NULL WHERE user_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("i", $userId);
    $updateStmt->execute();
    
    if ($updateStmt->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Email verified successfully! Your account has been upgraded.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to verify email.'
        ]);
    }
}