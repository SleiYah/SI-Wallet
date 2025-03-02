<?php

class User
{
    private $conn;

    public function __construct()
    {
        include(__DIR__ . "/../connection/conn.php");
        $this->conn = $conn;
    }
    
    public function create($userData)
    {
        $firstName = $userData['first_name'];
        $lastName = $userData['last_name'];
        $email = $userData['email'];
        $username = $userData['username'];
        $passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
        $tier = 1;
        $maxAmount = 50.00;

        $query = "INSERT INTO users (first_name, last_name, email, username, password_hash, tier, max_transaction_amount, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssdd", $firstName, $lastName, $email, $username, $passwordHash, $tier, $maxAmount);

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }

        return false;
    }

    public function read($id)
    {
        $query = "SELECT * FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }

        return false;
    }

    public function update($id, $userData)
    {
        $firstName = $userData['first_name'];
        $lastName = $userData['last_name'];
        $email = $userData['email'];
        $username = $userData['username'];

        $query = "UPDATE users SET 
                  first_name = ?,
                  last_name = ?,
                  email = ?,
                  username = ?
                  WHERE user_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssi", $firstName, $lastName, $email, $username, $id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function delete($id)
    {
        $query = "DELETE FROM users WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        return $stmt->execute() && $stmt->affected_rows > 0;
    }
}