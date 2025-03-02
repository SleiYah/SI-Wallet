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

        $query = "INSERT INTO users (first_name, last_name, email,username ,password_hash, tier, max_transaction_amount, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";


        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("sssssdi", $firstName, $lastName, $email,$username, $passwordHash, $tier, $maxAmount);

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }

        return false;
    }

    public function search($id)
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

    public function searchByEmail($email)
    {
        $query = "SELECT * FROM users  WHERE email = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("s", $email);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }

        return false;
    }
    public function searchByUsername($username)
    {
        $query = "SELECT * FROM users  WHERE username = ?";

        $stmt = $this->conn->prepare($query);

        $stmt->bind_param("s", $username);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }

        return false;
    }
    public function AllUsers()
    {
        $query = "SELECT * FROM users ORDER BY created_at DESC";


        $stmt = $this->conn->prepare($query);


        $stmt->execute();
        $result = $stmt->get_result();


        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        return $users;
    }


    public function update($id, $userData)
    {

        $firstName = $userData['first_name'];
        $lastName = $userData['last_name'];
        $email = $userData['email'];



        $query = "UPDATE users SET 
                  first_name = ?,
                  last_name = ?,
                  email = ?,
                  tier = ?,
                  WHERE user_id = ?";


        $stmt = $this->conn->prepare($query);


        $stmt->bind_param("sssii", $firstName, $lastName, $email, $tier, $id);


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
