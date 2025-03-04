<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


$jwt_secret_key = 'b1ack-$un-3mp1re';
$jwt_algorithm = 'HS256';


function generateToken($userData) {
    global $jwt_secret_key, $jwt_algorithm;
    
    $issuedAt = time();
    $expirationTime = $issuedAt + 3600; 
    
    $payload = [
        'iat'  => $issuedAt,
        'exp'  => $expirationTime,
        'data' => [
            'user_id'  => $userData['user_id'],
            'email'    => $userData['email'],
            'username' => $userData['username'],
        ]
    ];
    
    return JWT::encode($payload, $jwt_secret_key, $jwt_algorithm);
}


function validateToken($token) {
    global $jwt_secret_key, $jwt_algorithm;
    
    try {
        $decoded = JWT::decode($token, new Key($jwt_secret_key, $jwt_algorithm));
        return $decoded->data;
    } catch (Exception $e) {
        return false;
    }
}


function authenticate() {
    $headers = getallheaders();
    
    if (!isset($headers['Authorization'])) {
        echo json_encode([
            'success' => false,
            'message' => 'No token provided'
        ]);
        exit;
    }
    
    $token = str_replace('Bearer ', '', $headers['Authorization']);
    
    $userData = validateToken($token);
    if (!$userData) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid or expired token'
        ]);
        exit;
    }
    
    return $userData;
}


