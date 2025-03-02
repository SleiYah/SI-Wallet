<?php

class Verification
{
    private $conn;

    public function __construct()
    {
        include(__DIR__ . "/../connection/conn.php");
        $this->conn = $conn;
    }
    
    public function create($verificationData)
    {
        $userId = $verificationData['user_id'];
        $passportImage = $verificationData['passport_image'] ?? null;
        $selfieImage = $verificationData['selfie_image'] ?? null;
        $verified = 0;

        $query = "INSERT INTO verifications (user_id, passport_image, selfie_image, verified, created_at) 
                 VALUES (?, ?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("issi", $userId, $passportImage, $selfieImage, $verified);

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }

        return false;
    }

    public function read($id)
    {
        $query = "SELECT * FROM verifications WHERE verification_id = ?";
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
        $query = "UPDATE verifications SET 
                  verified = ?
                  WHERE verification_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $status, $id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function delete($id)
    {
        $query = "DELETE FROM verifications WHERE verification_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        return $stmt->execute() && $stmt->affected_rows > 0;
    }
}