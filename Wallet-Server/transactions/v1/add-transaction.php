<?php
include(__DIR__ . "/../../models/transactions.php");
include(__DIR__ . "/../../models/p2p_transactions.php");
include(__DIR__ . "/../../models/wallets.php");
include(__DIR__ . "/../../models/scheduled_transactions.php");
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
$note = $data['note'] ?? '';
$amount = $data['amount'] ?? null;
$transaction_type = $data['transaction_type'] ?? '';
$to_wallet_id = $data['to_wallet_id'] ?? null;
$recipient_username = $data['recipient_username'] ?? '';
$schedule_date = $data['schedule_date'] ?? null;
$is_scheduled = !empty($schedule_date);

if (empty($wallet_id) || $amount === null || empty($transaction_type)) {
    echo json_encode([
        'success' => false,
        'message' => 'Wallet ID, amount, and transaction type are required.'
    ]);
    exit;
}

if (!is_numeric($amount) || $amount <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Amount must be a positive number.'
    ]);
    exit;
}


$valid_types = ['deposit', 'withdraw', 'p2p'];
if (!in_array($transaction_type, $valid_types)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid transaction type. Must be deposit, withdraw, or p2p.'
    ]);
    exit;
}


if ($is_scheduled && $transaction_type !== 'p2p') {
    echo json_encode([
        'success' => false,
        'message' => 'Only P2P transactions can be scheduled.'
    ]);
    exit;
}
if ($transaction_type === 'p2p' && empty($to_wallet_id)) {
    echo json_encode([
        'success' => false,
        'message' => 'Recipient wallet ID is required for P2P transactions.'
    ]);
    exit;
}

if ($is_scheduled) {
    $current_date = new DateTime();
    $scheduled_date = new DateTime($schedule_date);

    if ($scheduled_date < $current_date) {
        echo json_encode([
            'success' => false,
            'message' => 'Schedule date cannot be in the past.'
        ]);
        exit;
    }

    $formatted_schedule_date = $scheduled_date->format('Y-m-d H:i:s');
}

$source_user_query = "SELECT u.tier, u.max_transaction_amount 
                       FROM wallets w 
                       JOIN users u ON w.user_id = u.user_id 
                       WHERE w.wallet_id = ?";
$source_user_stmt = $conn->prepare($source_user_query);
$source_user_stmt->bind_param("i", $wallet_id);
$source_user_stmt->execute();
$source_user_result = $source_user_stmt->get_result();

if ($source_user_result->num_rows == 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to retrieve user information'
    ]);
    exit;
}

$source_user = $source_user_result->fetch_assoc();
$source_max_amount = $source_user['max_transaction_amount'];

if ($amount > $source_max_amount) {
    echo json_encode([
        'success' => false,
        'message' => "Transaction amount exceeds your limit of $" . $source_max_amount
    ]);
    exit;
}

if ($transaction_type === 'p2p' && !empty($data['recipient_username'])) {
    $recipient_username = $data['recipient_username'];

    $user_query = "SELECT user_id FROM users WHERE username = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("s", $recipient_username);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();

    if ($user_result->num_rows == 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Recipient username not found'
        ]);
        exit;
    }

    $recipient_user_id = $user_result->fetch_assoc()['user_id'];

    $wallet_query = "SELECT * FROM wallets WHERE wallet_id = ? AND user_id = ?";
    $wallet_stmt = $conn->prepare($wallet_query);
    $wallet_stmt->bind_param("ii", $to_wallet_id, $recipient_user_id);
    $wallet_stmt->execute();
    $wallet_result = $wallet_stmt->get_result();

    if ($wallet_result->num_rows == 0) {
        echo json_encode([
            'success' => false,
            'message' => 'The specified wallet ID does not belong to this recipient'
        ]);
        exit;
    }

    $recipient_user_query = "SELECT u.tier, u.max_transaction_amount ,u.username
                             FROM users u 
                             WHERE u.user_id = ?";
    $recipient_user_stmt = $conn->prepare($recipient_user_query);
    $recipient_user_stmt->bind_param("i", $recipient_user_id);
    $recipient_user_stmt->execute();
    $recipient_user_result = $recipient_user_stmt->get_result();

    if ($recipient_user_result->num_rows > 0) {
        $recipient_user = $recipient_user_result->fetch_assoc();
        $recipient_max_amount = $recipient_user['max_transaction_amount'];
        $recipient_username = $recipient_user['username'];


        if ($amount > $recipient_max_amount) {
            echo json_encode([
                'success' => false,
                'message' => $recipient_username . " can't receive more than $" . $recipient_max_amount . " per transaction"
            ]);
            exit;
        }
    }
} else if ($transaction_type === 'p2p' && empty($data['recipient_username'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Recipient can not be empty in p2p transactions'
    ]);
    exit;
}

$wallet = new Wallet();
$transaction = new Transaction();
$p2p_transaction = new P2P_Transaction();


$sourceWallet = $wallet->read($wallet_id);
if (!$sourceWallet) {
    echo json_encode([
        'success' => false,
        'message' => 'Source wallet not found'
    ]);
    exit;
}

if (($transaction_type === 'withdraw' || $transaction_type === 'p2p') && $sourceWallet['balance'] < $amount) {
    echo json_encode([
        'success' => false,
        'message' => 'Insufficient balance'
    ]);
    exit;
}


if ($transaction_type === 'p2p') {
    $recipientWallet = $wallet->read($to_wallet_id);
    if (!$recipientWallet) {
        echo json_encode([
            'success' => false,
            'message' => 'Recipient wallet not found'
        ]);
        exit;
    }


    if ($wallet_id == $to_wallet_id) {
        echo json_encode([
            'success' => false,
            'message' => 'Cannot transfer to the same wallet'
        ]);
        exit;
    }
}


$transactionData = [
    'wallet_id' => $wallet_id,
    'note' => $note,
    'amount' => $amount,
    'transaction_type' => $transaction_type,
    'status' => 'pending'
];

$transaction_id = $transaction->create($transactionData);

if (!$transaction_id) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to create transaction'
    ]);
    exit;
}

if ($transaction_type === 'p2p') {
    if (!$p2p_transaction->create($transaction_id, $to_wallet_id)) {
        $transaction->delete($transaction_id);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create P2P transaction record'
        ]);
        exit;
    }
}

if ($is_scheduled) {
    $scheduled_transaction = new Scheduled_Transaction();
    $schedule_id = $scheduled_transaction->create($transaction_id, $formatted_schedule_date);



    echo json_encode([
        'success' => true,
        'message' => 'Transaction scheduled successfully',
        'transaction_id' => $transaction_id,
        'schedule_id' => $schedule_id,
        'schedule_date' => $formatted_schedule_date
    ]);
    exit;
}

$newBalance = 0;


if ($transaction_type === 'deposit') {
    $newBalance = $sourceWallet['balance'] + $amount;
    $walletData = ['balance' => $newBalance];
    if (!$wallet->update($wallet_id, $walletData)) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update source wallet balance'
        ]);
        exit;
    }
} else if ($transaction_type === 'withdraw') {
    $newBalance = $sourceWallet['balance'] - $amount;
    $walletData = ['balance' => $newBalance];
    if (!$wallet->update($wallet_id, $walletData)) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update source wallet balance'
        ]);
        exit;
    }
} else if ($transaction_type === 'p2p') {


    $newSourceBalance = $sourceWallet['balance'] - $amount;
    $sourceWalletData = ['balance' => $newSourceBalance];
    if (!$wallet->update($wallet_id, $sourceWalletData)) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update source wallet balance'
        ]);
        exit;
    }


    $newRecipientBalance = $recipientWallet['balance'] + $amount;
    $recipientWalletData = ['balance' => $newRecipientBalance];
    if (!$wallet->update($to_wallet_id, $recipientWalletData)) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update recipient wallet balance'
        ]);
        exit;
    }

    $newBalance = $newSourceBalance;
}


$transaction->update($transaction_id, 'completed');

echo json_encode([
    'success' => true,
    'message' => 'Transaction completed successfully',
    'transaction_id' => $transaction_id,
    'new_balance' => $newBalance
]);
