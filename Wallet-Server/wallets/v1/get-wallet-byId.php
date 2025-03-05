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


$userId = $userData->user_id;


if ($userData->user_id != $userId) {
    echo json_encode([
        'success' => false,
        'message' => 'You do not have permission to view wallets for this user'
    ]);
    exit;
}


$query = "SELECT w.*, u.first_name, u.last_name 
          FROM wallets w
          JOIN users u ON w.user_id = u.user_id
          WHERE w.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$wallets = [];
while ($row = $result->fetch_assoc()) {
    $wallets[] = $row;
}

echo json_encode([
    'success' => true,
    'data' => $wallets
]);
