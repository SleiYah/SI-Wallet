<?php
include(__DIR__ . "/../../models/tickets.php");
include(__DIR__ . "/../../models/Users.php");
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

$ticket_id = $data['ticket_id'] ?? null;
$subject = $data['subject'] ?? '';
$message = $data['message'] ?? '';
$status = $data['status'] ?? '';

$userData = authenticate();
$user_id = $userData->user_id;

$ticket = new Ticket();
$user = new User();



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
                'message' => 'Invalid status. Must be "open" or "resolved".'
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
    if ( empty($subject) || empty($message)) {
        echo json_encode([
            'success' => false,
            'message' => 'Subject and message are required.'
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