<?php

class CreateWalletsTable {
    public static function wallets($conn) {
        $sql = "CREATE TABLE IF NOT EXISTS wallets (
            wallet_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            card_number VARCHAR(19) NOT NULL,
            card_type ENUM('Visa', 'Mastercard', 'Amex', 'Discover') DEFAULT NULL,
            cvv VARCHAR(3) NOT NULL,
            expiry_date VARCHAR(5) NOT NULL,
            balance DECIMAL(10,2) DEFAULT 0.00,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
        );";

        if($conn->query($sql)){
            echo "table 'wallets' created successfully\n";
        }
        else {
            echo "Error creating table 'wallets'\n" . $conn->error . "\n";
        }
    }
}
?>
