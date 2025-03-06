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

$query = "SELECT v.*, u.first_name, u.last_name, u.email, u.username 
          FROM verifications v
          JOIN users u ON v.user_id = u.user_id
          WHERE v.verified = 0";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$verifications = [];
while ($row = $result->fetch_assoc()) {
    $verifications[] = $row;
}

echo json_encode([
    'success' => true,
    'verifications' => $verifications
]);
?>