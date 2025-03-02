<?php

class Wallet
{
    private $conn;

    public function __construct()
    {
        include(__DIR__ . "/../connection/conn.php");
        $this->conn = $conn;
    }

    public function create($walletData)
    {
        $userId = $walletData['user_id'];
        $cardNumber = $walletData['card_number'];
        $cvv = $walletData['cvv'];
        $expiryDate = $walletData['expiry_date'];
        $balance = 0;

        $query = "INSERT INTO wallets (user_id, card_number, cvv, expiry_date, balance, created_at) 
                 VALUES (?, ?, ?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isssd", $userId, $cardNumber, $cvv, $expiryDate, $balance);

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

    public function readByUserId($userId)
    {
        $query = "SELECT * FROM wallets WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $wallets = [];
        while ($row = $result->fetch_assoc()) {
            $wallets[] = $row;
        }

        return $wallets;
    }

    public function updateBalance($id, $balance)
    {
        $query = "UPDATE wallets SET balance = ? WHERE wallet_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("di", $balance, $id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}