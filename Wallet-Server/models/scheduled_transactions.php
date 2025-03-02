<?php

class Scheduled_Transaction
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function create($transactionId, $executeDate)
    {
        $query = "INSERT INTO scheduled_transactions (transaction_id, execute_date, completed) 
                  VALUES (?, ?, 0)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("is", $transactionId, $executeDate);

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }

        return false;
    }

    public function read($scheduleId)
    {
        $query = "SELECT * FROM scheduled_transactions WHERE schedule_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $scheduleId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }

        return false;
    }

    public function update($scheduleId, $completed)
    {
        $query = "UPDATE scheduled_transactions SET completed = ? WHERE schedule_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $completed, $scheduleId);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function delete($scheduleId)
    {
        $query = "DELETE FROM scheduled_transactions WHERE schedule_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $scheduleId);

        return $stmt->execute() && $stmt->affected_rows > 0;
    }
}