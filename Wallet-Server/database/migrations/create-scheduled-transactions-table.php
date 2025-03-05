<?php

class CreateScheduledTransactionsTable {
    public static function scheduled_transactions($conn) {
        $sql = "CREATE TABLE IF NOT EXISTS scheduled_transactions (
            schedule_id INT AUTO_INCREMENT PRIMARY KEY,
            transaction_id INT NOT NULL,
            execute_date DATETIME NOT NULL,
            completed TINYINT(1) DEFAULT 0,
            FOREIGN KEY (transaction_id) REFERENCES transactions(transaction_id) ON DELETE CASCADE
        );";

        if($conn->query($sql)){
            echo "table 'scheduled_transactions' created successfully\n";
        }
        else {
            echo "Error creating table 'scheduled_transactions'\n" . $conn->error . "\n";
        }
    }
}
?>
