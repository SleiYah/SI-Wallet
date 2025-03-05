<?php

class SystemLog
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function create($logData)
    {
        $userId = $logData['user_id'] ?? null;
        $action = $logData['action'];
        $details = $logData['details'] ?? null;

        $query = "INSERT INTO system_logs (user_id, action, details, created_at) 
                  VALUES (?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iss", $userId, $action, $details);

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }

        return false;
    }

    public function read($logId)
    {
        $query = "SELECT * FROM system_logs WHERE log_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $logId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }

        return false;
    }

    public function update($logId, $logData)
    {
        $action = $logData['action'];
        $details = $logData['details'] ?? null;
        
        $query = "UPDATE system_logs SET action = ?, details = ? WHERE log_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssi", $action, $details, $logId);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function delete($logId)
    {
        $query = "DELETE FROM system_logs WHERE log_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $logId);

        return $stmt->execute() && $stmt->affected_rows > 0;
    }
}