<?php

class CreateSystemLogsTable {
    public static function system_logs($conn) {
        $sql = "CREATE TABLE IF NOT EXISTS system_logs (
            log_id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT DEFAULT NULL,
            action VARCHAR(100) NOT NULL,
            details TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
        );";

        if($conn->query($sql)){
            echo "table 'system_logs' created successfully\n";
        }
        else {
            echo "Error creating table 'system_logs'\n" . $conn->error . "\n";
        }
    }
}
?>
