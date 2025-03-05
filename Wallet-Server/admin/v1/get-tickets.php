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

$query = "SELECT t.*, u.user_id, u.username, u.first_name, u.last_name, u.email
          FROM tickets t
          JOIN users u ON t.user_id = u.user_id
          WHERE t.status = 'open'
          ORDER BY t.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$tickets = [];
while ($row = $result->fetch_assoc()) {
    $tickets[] = $row;
}

echo json_encode([
    'success' => true,
    'tickets' => $tickets
]);
?>