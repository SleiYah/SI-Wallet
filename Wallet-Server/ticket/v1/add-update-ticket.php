<?php
include(__DIR__ . "/../../models/Tickets.php");
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

$ticket_id = $data['ticket_id'] ?? null;
$user_id = $data['user_id'] ?? null;
$subject = $data['subject'] ?? '';
$message = $data['message'] ?? '';
$status = $data['status'] ?? '';

$ticket = new Ticket();

function checkUserExists($conn, $user_id) {
    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}

function validateStatus($status) {
    $valid_statuses = ['open', 'in_progress', 'resolved'];
    return in_array($status, $valid_statuses);
}

$ticketData = [
    'user_id' => $user_id,
    'subject' => $subject,
    'message' => $message,
];

if ($ticket_id) {
    $existingTicket = $ticket->read($ticket_id);
    if (!$existingTicket) {
        echo json_encode([
            'success' => false,
            'message' => 'Ticket not found'
        ]);
        exit;
    }

    if (!empty($status)) {
        if (!validateStatus($status)) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid status. Must be "open", "in_progress", or "resolved".'
            ]);
            exit;
        }
        $ticketData['status'] = $status;
    }

    if ($user_id && $user_id != $existingTicket['user_id']) {
        if (!checkUserExists($conn, $user_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'User not found'
            ]);
            exit;
        }
    }

    $result = $ticket->update($ticket_id, $ticketData);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Ticket updated successfully',
            'ticket_id' => $ticket_id
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update ticket'
        ]);
    }
} else {
    if (empty($user_id) || empty($subject) || empty($message)) {
        echo json_encode([
            'success' => false,
            'message' => 'User ID, subject, and message are required.'
        ]);
        exit;
    }

    if (!checkUserExists($conn, $user_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        exit;
    }

    $newTicketId = $ticket->create($ticketData);
    
    if ($newTicketId) {
        echo json_encode([
            'success' => true,
            'message' => 'Ticket created successfully',
            'ticket_id' => $newTicketId
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create ticket'
        ]);
    }
}