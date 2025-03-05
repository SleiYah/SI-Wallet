<?php

class CreateVerificationsTable {
    public static function verifications($conn) {
        $sql = "CREATE TABLE IF NOT EXISTS verifications (
            verification_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            passport_image TEXT DEFAULT NULL,
            selfie_image TEXT DEFAULT NULL,
            verified TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
        );";

        if($conn->query($sql)){
            echo "table 'verifications' created successfully\n";
        }
        else {
            echo "Error creating table 'verifications'\n" . $conn->error . "\n";
        }
    }
}
?>
