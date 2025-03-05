<?php

class CreateTicketsTable {
    public static function tickets($conn) {
        $sql = "CREATE TABLE IF NOT EXISTS tickets (
            ticket_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            subject VARCHAR(100) NOT NULL,
            message TEXT NOT NULL,
            status ENUM('open', 'in_progress', 'resolved') DEFAULT 'open',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id)
        );";

        if($conn->query($sql)){
            echo "table 'tickets' created successfully\n";
        }
        else {
            echo "Error creating table 'tickets'\n" . $conn->error . "\n";
        }
    }
}
?>
