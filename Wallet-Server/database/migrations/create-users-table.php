<?php

class CreateUsersTable {
    public static function users($conn) {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            user_id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            username VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            email_token VARCHAR(50) DEFAULT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            tier INT NOT NULL DEFAULT 1,
            max_transaction_amount DECIMAL(10,2) DEFAULT 50.00,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );";

        if($conn->query($sql)){
            echo "table 'users' created successfully\n";
        }
        else {
            echo "Error creating table 'users'\n" . $conn->error . "\n";
        }
    }
}
?>
