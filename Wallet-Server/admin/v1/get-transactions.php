<?php
include(__DIR__ . "/../../connection/conn.php");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Only GET requests are allowed.'
    ]);
    exit;
}

$query = "SELECT t.*, w.card_number, w.card_type, u.user_id, u.username
          FROM transactions t
          JOIN wallets w ON t.wallet_id = w.wallet_id
          JOIN users u ON w.user_id = u.user_id
          ORDER BY t.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

echo json_encode([
    'success' => true,
    'transactions' => $transactions
]);
?>