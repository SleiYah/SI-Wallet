<?php

class CreateP2PTransactionsTable {
    public static function p2p_transactions($conn) {
        $sql = "CREATE TABLE IF NOT EXISTS p2p_transactions (
            p2p_id INT AUTO_INCREMENT PRIMARY KEY,
            transaction_id INT NOT NULL,
            to_wallet_id INT NOT NULL,
            FOREIGN KEY (transaction_id) REFERENCES transactions(transaction_id) ON DELETE CASCADE,
            FOREIGN KEY (to_wallet_id) REFERENCES wallets(wallet_id) ON DELETE CASCADE
        );";

        if($conn->query($sql)){
            echo "table 'p2p_transactions' created successfully\n";
        }
        else {
            echo "Error creating table 'p2p_transactions'\n" . $conn->error . "\n";
        }
    }
}
?>
