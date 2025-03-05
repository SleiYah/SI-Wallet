<?php
include(__DIR__ . "/../../models/Users.php");
include(__DIR__ . "/../../connection/conn.php");
include(__DIR__ . "/../../utils/jwt-auth.php");


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Only POST requests are allowed.'
    ]);
    exit;
}

$userData = authenticate();
$userId = $userData->user_id;
if (!$userData) {
    echo json_encode([
        'success' => false,
        'message' => 'Authentication failed'
    ]);
    exit;
}



$user = new User();
$userProfile = $user->read($userId);

if ($userProfile) {
    unset($userProfile['password_hash']);
    unset($userProfile['user_id']);
    echo json_encode([
        'success' => true,
        'data' => $userProfile
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'User profile not found'
    ]);
}