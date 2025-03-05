<?php
include(__DIR__ . "/../../models/wallets.php");
include(__DIR__ . "/../../models/users.php");
include(__DIR__ . "/../../connection/conn.php");
include(__DIR__ . "/../../utils/jwt-auth.php");

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
$card_number = $data['card_number'] ?? '';
$card_type = $data['card_type'] ?? null;
$cvv = $data['cvv'] ?? '';
$expiry_date = $data['expiry_date'] ?? '';
$balance = $data['balance'] ?? 0.00;

$userData = authenticate();
$user_id = $userData->user_id;

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
    if (empty($card_number) || empty($card_type) || empty($cvv) || empty($expiry_date)) {
        echo json_encode([
            'success' => false,
            'message' => 'Card number, card type, CVV, and expiry date are required.'
        ]);
        exit;
    }
    
    $allowed_card_types = ['Visa', 'Mastercard', 'Amex', 'Discover'];
    if (!in_array($card_type, $allowed_card_types)) {
        echo json_encode([
            'success' => false,
            'message' => 'Card type must be one of: Visa, Mastercard, Amex, or Discover.'
        ]);
        exit;
    }
    
    $user = new User();
    $userInfo = $user->read($user_id);
    
    if (!$userInfo) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        exit;
    }
    
    $user_tier = $userInfo['tier'];
    
    if ($user_tier < 3) {
        $wallet_count_query = "SELECT COUNT(*) as wallet_count FROM wallets WHERE user_id = ?";
        $wallet_count_stmt = $conn->prepare($wallet_count_query);
        $wallet_count_stmt->bind_param("i", $user_id);
        $wallet_count_stmt->execute();
        $wallet_count_result = $wallet_count_stmt->get_result();
        $wallet_count = $wallet_count_result->fetch_assoc()['wallet_count'];
        
        if ($wallet_count > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Users with tier ' . $user_tier . ' are allowed one wallet. Upgrade to tier 3 for multiple wallets.'
            ]);
            exit;
        }
    }
    
    $checkQuery = "SELECT wallet_id FROM wallets WHERE card_number = ? AND card_type = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ss", $card_number, $card_type);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'This card is already registered with the same brand'
        ]);
        exit;
    }
    
    $walletData = [
        'user_id' => $user_id,
        'card_number' => $card_number,
        'card_type' => $card_type,
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