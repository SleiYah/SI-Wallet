<?php



include("../../models/users.php");


header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Only POST requests are allowed.'
    ]);
    return;
}


$json_string = file_get_contents('php://input');
$data = json_decode($json_string, true);



$user_id = $data['user_id'] ?? null;
$firstName = $data['first_name'] ?? '';
$lastName = $data['last_name'] ?? '';
$email = $data['email'] ?? '';
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';


if (empty($firstName) || empty($lastName) || empty($email) ||empty($username)) {
    echo json_encode([
        'success' => false,
        'message' => 'First name, last name, email and username are required.'
    ]);
    return;
}


$user = new User();


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

    $existingUser = $user->search($user_id);
    if (!$existingUser) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        return;
    }
    
    
    if ($email != $existingUser['email']) {
        $emailCheck = $user->searchByEmail($email);
        if ($emailCheck) {
            echo json_encode([
                'success' => false,
                'message' => 'Email already in use by another account'
            ]);
            return;
        }
    }
    if ($username != $existingUser['username']) {
        $usernameCheck = $user->searchByUsername($username);
        if ($usernameCheck) {
            echo json_encode([
                'success' => false,
                'message' => 'Username already in use by another account'
            ]);
            return;
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
} else {
    
    
    
    if (empty($password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Password is required for new users'
        ]);
        return;
    }
    
    
    if ($user->searchByEmail($email)) {
        echo json_encode([
            'success' => false,
            'message' => 'Email already exists'
        ]);
        return;
    }
    if ($user->searchByUsername($username)) {
        echo json_encode([
            'success' => false,
            'message' => 'Username already exists'
        ]);
        return;
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