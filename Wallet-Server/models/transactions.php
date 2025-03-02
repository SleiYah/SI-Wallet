<?php

class Transaction
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function create($transactionData)
    {
        $wallet_id = $transactionData['wallet_id'];
        $note = $transactionData['note'] ?? null;
        $amount = $transactionData['amount'];
        $transaction_type = $transactionData['transaction_type'];
        $status = 'pending';

        $query = "INSERT INTO transactions (wallet_id, note, amount, transaction_type, status, created_at) 
                  VALUES (?, ?, ?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("isdss", $wallet_id, $note, $amount, $transaction_type, $status);

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }

        return false;
    }

    public function read($id)
    {
        $query = "SELECT * FROM transactions WHERE transaction_id = ?";
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
        $query = "UPDATE transactions SET status = ? WHERE transaction_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function delete($id)
    {
        $query = "DELETE FROM transactions WHERE transaction_id = ? and status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        return $stmt->execute() && $stmt->affected_rows > 0;
    }
}