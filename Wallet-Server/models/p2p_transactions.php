<?php

class P2P_Transaction
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function create($transactionId, $toWalletId)
    {
        $query = "INSERT INTO p2p_transactions (transaction_id, to_wallet_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $transactionId, $toWalletId);

        if ($stmt->execute()) {
            return $this->conn->insert_id;
        }

        return false;
    }

    public function read($p2pId)
    {
        $query = "SELECT * FROM p2p_transactions WHERE p2p_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $p2pId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }

        return false;
    }

    public function delete($p2pId)
    {
        $query = "DELETE FROM p2p_transactions WHERE p2p_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $p2pId);

        return $stmt->execute() && $stmt->affected_rows > 0;
    }
}