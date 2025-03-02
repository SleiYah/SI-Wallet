<?php
include("../../models/users.php");
include(__DIR__ . "/../../connection/conn.php");

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

$user_id = $data['user_id'] ?? null;
$firstName = $data['first_name'] ?? '';
$lastName = $data['last_name'] ?? '';
$email = $data['email'] ?? '';
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

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
    }}

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


if ($user_id) {
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