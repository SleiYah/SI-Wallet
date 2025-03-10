<?php
include(__DIR__ . "/../../connection/conn.php");
include(__DIR__ . "/../../utils/jwt-auth.php");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Username and password are required'
        ]);
        exit;
    }
    
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid credentials'
        ]);
        exit;
    }
    
    $user = $result->fetch_assoc();
    $user_id = $user['user_id'];
    if ($user['tier'] != 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Access denied. You need administrator privileges.'
        ]);
        exit;
    }
    
    if (password_verify($password, $user['password_hash'])) {
        $token = generateToken($user);
        
        unset($user['password_hash']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Admin login successful',
            'token' => $token,
            'user' => $user
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid credentials'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Only POST requests are allowed.'
    ]);
}
?>