<?php
include(__DIR__ . "/../../models/tickets.php");
include(__DIR__ . "/../../models/Users.php");
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
$user = new User();

function checkUserExists($user, $user_id) {
    $userData = $user->read($user_id);
    return $userData ? true : false;
}

function validateStatus($status) {
    $valid_statuses = ['open', 'in_progress', 'resolved'];
    return in_array($status, $valid_statuses);
}

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
        
        $result = $ticket->update($ticket_id, $status);
        
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
        echo json_encode([
            'success' => false,
            'message' => 'Status is required for ticket updates'
        ]);
        exit;
    }
} else {
    if (empty($user_id) || empty($subject) || empty($message)) {
        echo json_encode([
            'success' => false,
            'message' => 'User ID, subject, and message are required.'
        ]);
        exit;
    }

    if (!checkUserExists($user, $user_id)) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        exit;
    }

    $ticketData = [
        'user_id' => $user_id,
        'subject' => $subject,
        'message' => $message
    ];

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
?>