<?php

class Ticket
{
    private $conn;

    public function __construct()
    {
        include(__DIR__ . "/../connection/conn.php");
        $this->conn = $conn;
    }

    public function create($ticketData)
    {
        $userId = $ticketData['user_id'];
        $subject = $ticketData['subject'];
        $message = $ticketData['message'];
        $status = 'open';

        $query = "INSERT INTO tickets (user_id, subject, message, status, created_at) 
                 VALUES (?, ?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isss", $userId, $subject, $message, $status);

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }

        return false;
    }

    public function read($id)
    {
        $query = "SELECT * FROM tickets WHERE ticket_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }

        return false;
    }

    public function update($id, $status)
    {
        $query = "UPDATE tickets SET status = ? WHERE ticket_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function delete($id)
    {
        $query = "DELETE FROM tickets WHERE ticket_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        return $stmt->execute() && $stmt->affected_rows > 0;
    }
    
}