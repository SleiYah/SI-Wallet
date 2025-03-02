<?php
include(__DIR__ . "/../../models/wallets.php");
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

$wallet_id = $data['wallet_id'] ?? null;
$user_id = $data['user_id'] ?? null;
$card_number = $data['card_number'] ?? '';
$cvv = $data['cvv'] ?? '';
$expiry_date = $data['expiry_date'] ?? '';
$balance = $data['balance'] ?? 0.00;

$wallet = new Wallet();

if ($wallet_id) {
    $existingWallet = $wallet->read($wallet_id);
    if (!$existingWallet) {
        echo json_encode([
            'success' => false,
            'message' => 'Wallet not found'
        ]);
        exit;
    }
    
    if (!is_numeric($balance)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid balance value'
        ]);
        exit;
    }
    
    $result = $wallet->updateBalance($wallet_id, $balance);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Wallet balance updated successfully',
            'wallet_id' => $wallet_id
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update wallet balance'
        ]);
    }
} 
else {
    if (empty($user_id) || empty($card_number) || empty($cvv) || empty($expiry_date)) {
        echo json_encode([
            'success' => false,
            'message' => 'User ID, card number, CVV, and expiry date are required.'
        ]);
        exit;
    }
    
   
    include(__DIR__ . "/../../models/users.php");
    $user = new User();
    $existingUser = $user->read($user_id);
    if (!$existingUser) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        exit;
    }
    
    $walletData = [
        'user_id' => $user_id,
        'card_number' => $clean_card_number,
        'cvv' => $cvv,
        'expiry_date' => $expiry_date,
    ];
    
    $walletId = $wallet->create($walletData);
    
    if ($walletId) {
        echo json_encode([
            'success' => true,
            'message' => 'Wallet created successfully',
            'wallet_id' => $walletId
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create wallet'
        ]);
    }
}