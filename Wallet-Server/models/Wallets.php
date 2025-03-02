<?php

class Wallet
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function create($walletData)
    {
        $userId = $walletData['user_id'];
        $cardNumber = $walletData['card_number'];
        $cardType = $walletData['card_type'] ?? null;
        $cvv = $walletData['cvv'];
        $expiryDate = $walletData['expiry_date'];
        $balance = 0;

        $query = "INSERT INTO wallets (user_id, card_number, card_type, cvv, expiry_date, balance, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("issssd", $userId, $cardNumber, $cardType, $cvv, $expiryDate, $balance);

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }

        return false;
    }

    public function read($id)
    {
        $query = "SELECT * FROM wallets WHERE wallet_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }

        return false;
    }

    public function update($id, $walletData)
    {
        $balance = $walletData['balance'];
        
            $query = "UPDATE wallets SET balance = ? WHERE wallet_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("di", $balance, $id);
        

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function delete($id)
    {
        $query = "DELETE FROM wallets WHERE wallet_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        return $stmt->execute() && $stmt->affected_rows > 0;
    }
}