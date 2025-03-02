<?php
include(__DIR__ . "/../../models/wallets.php");
include(__DIR__ . "/../../connection/conn.php");

header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Only GET requests are allowed.'
    ]);
    exit;
}


if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User ID is required'
    ]);
    exit;
}

$userId = $_GET['user_id'];


if (!is_numeric($userId)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid user ID format'
    ]);
    exit;
}


$query = "SELECT * FROM wallets WHERE user_id = ?";
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