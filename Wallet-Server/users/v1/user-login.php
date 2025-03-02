<?php
include(__DIR__ . "/../../connection/conn.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $email = $data['email'] ?? '';
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    
    if ((empty($email) && empty($username)) || empty($password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Email/username and password are required'
        ]);
        exit;
    }
    
    if (!empty($email)) {
        $query = "SELECT * FROM users WHERE email = ?";
        $param = $email;
    } else {
        $query = "SELECT * FROM users WHERE username = ?";
        $param = $username;
    }
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $param);
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
    
    if (password_verify($password, $user['password_hash'])) {
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid credentials'
        ]);
    }
}
else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Only POST requests are allowed.'
    ]);
}
?>