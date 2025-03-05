<?php

include("conn.php");

include("./create_users_table.php");
include("./create_wallets_table.php");
include("./create_transactions_table.php");
include("./create_p2p_transactions_table.php");
include("./create_scheduled_transactions_table.php");
include("./create_verifications_table.php");
include("./create_tickets_table.php");
include("./create_system_logs_table.php");

class Migration {
    public static function createTables($connection) {
        CreateUsersTable::users($connection);
        CreateWalletsTable::wallets($connection);
        CreateTransactionsTable::transactions($connection);
        CreateP2PTransactionsTable::p2p_transactions($connection);
        CreateScheduledTransactionsTable::scheduled_transactions($connection);
        CreateVerificationsTable::verifications($connection);
        CreateTicketsTable::tickets($connection);
        CreateSystemLogsTable::system_logs($connection);
    }
}

// Execute migrations
Migration::createTables($conn);
echo "\nAll migrations completed successfully!";
?>
