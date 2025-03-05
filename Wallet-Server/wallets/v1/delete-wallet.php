<?php
include(__DIR__ . "/../../models/wallets.php");
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

$userData = authenticate();
$user_id = $userData->user_id;
$data = json_decode(file_get_contents('php://input'), true);
$walletId = $data['wallet_id'] ?? null;

if (!$walletId) {
    echo json_encode([
        'success' => false,
        'message' => 'Wallet ID is required'
    ]);
    exit;
}

$query = "SELECT * FROM wallets WHERE wallet_id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $walletId, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'You don\'t have permission to delete this wallet'
    ]);
    exit;
}

$wallet = $result->fetch_assoc();

if (floatval($wallet['balance']) > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Cannot delete wallet with non-zero balance. Please transfer or withdraw all funds first.'
    ]);
    exit;
}
$walletModel = new Wallet();

$success = $walletModel->delete($walletId);

if ($success) {
    echo json_encode([
        'success' => true,
        'message' => 'Wallet deleted successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete wallet'
    ]);
}