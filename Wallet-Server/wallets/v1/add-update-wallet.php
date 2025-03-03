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
    
    $walletData = [
        'balance' => $balance
    ];
    
    $result = $wallet->update($wallet_id, $walletData);
    
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
    $user_query = "SELECT * FROM users WHERE user_id = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    
    if ($user_result->num_rows == 0) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        exit;
    }
    
    $walletData = [
        'user_id' => $user_id,
        'card_number' => $card_number,
        'cvv' => $cvv,
        'expiry_date' => $expiry_date,
        'balance' => $balance
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