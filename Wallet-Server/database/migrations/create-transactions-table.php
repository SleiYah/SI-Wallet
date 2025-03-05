<?php

class CreateTransactionsTable {
    public static function transactions($conn) {
        $sql = "CREATE TABLE IF NOT EXISTS transactions (
            transaction_id INT AUTO_INCREMENT PRIMARY KEY,
            wallet_id INT NOT NULL,
            note VARCHAR(100) DEFAULT NULL,
            amount DECIMAL(10,2) NOT NULL,
            transaction_type ENUM('deposit', 'withdraw', 'p2p') NOT NULL,
            status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (wallet_id) REFERENCES wallets(wallet_id) ON DELETE CASCADE
        );";

        if($conn->query($sql)){
            echo "table 'transactions' created successfully\n";
        }
        else {
            echo "Error creating table 'transactions'\n" . $conn->error . "\n";
        }
    }
}
?>
