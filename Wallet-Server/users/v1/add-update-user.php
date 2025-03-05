<?php
include("../../models/users.php");
include(__DIR__ . "/../../connection/conn.php");
include(__DIR__ . "/../../utils/jwt-auth.php"); // Added JWT auth

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Only POST requests are allowed.'
    ]);
    exit;
}

$json_string = file_get_contents('php://input');
$data = json_decode($json_string, true);

$operation = $data['operation'] ?? '';

if (!in_array($operation, ['add', 'update'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid operation. Must be either "add" or "update".'
    ]);
    exit;
}

$firstName = $data['first_name'] ?? '';
$lastName = $data['last_name'] ?? '';
$email = $data['email'] ?? '';
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

$user_id = null;
if ($operation === 'update') {
    $jwt_data = authenticate();
    if (!$jwt_data) {
        echo json_encode([
            'success' => false,
            'message' => 'Authentication failed. Please login again.'
        ]);
        exit;
    }
    $user_id = $jwt_data->user_id;
}

if (empty($firstName) || empty($lastName) || empty($email) || empty($username)) {
    echo json_encode([
        'success' => false,
        'message' => 'First name, last name, email and username are required.'
    ]);
    exit;
}

$user = new User();

function checkEmailExists($conn, $email) {
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}

function checkUsernameExists($conn, $username) {
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}

$userData = [
    'first_name' => $firstName,
    'last_name' => $lastName,
    'email' => $email,
    'username' => $username,
];

if (!empty($password)) {
    $userData['password'] = $password;
}


if ($operation === 'update') {
    $existingUser = $user->read($user_id);
    if (!$existingUser) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        exit;
    }
    
    if ($email != $existingUser['email']) {
        $emailCheck = checkEmailExists($conn, $email);
        if ($emailCheck) {
            echo json_encode([
                'success' => false,
                'message' => 'Email already in use by another account'
            ]);
            exit;
        }
    }
    
    if ($username != $existingUser['username']) {
        $usernameCheck = checkUsernameExists($conn, $username);
        if ($usernameCheck) {
            echo json_encode([
                'success' => false,
                'message' => 'Username already in use by another account'
            ]);
            exit;
        }
    }

    $result = $user->update($user_id, $userData);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'User updated successfully',
            'user_id' => $user_id
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update user'
        ]);
    }
} 
else { 
    if (empty($password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Password is required for new users'
        ]);
        exit;
    }
    
    if (checkEmailExists($conn, $email)) {
        echo json_encode([
            'success' => false,
            'message' => 'Email already exists'
        ]);
        exit;
    }
    
    if (checkUsernameExists($conn, $username)) {
        echo json_encode([
            'success' => false,
            'message' => 'Username already exists'
        ]);
        exit;
    }

    $userId = $user->create($userData);
    
    if ($userId) {
        echo json_encode([
            'success' => true,
            'message' => 'User created successfully',
            'user_id' => $userId
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create user'
        ]);
    }
}