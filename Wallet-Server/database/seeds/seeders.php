<?php

include("conn.php");

class Seeder {
    public static function seedUsers($conn) {
        $users = [
                    [
                        'first_name' => 'Admin',
                        'last_name' => 'User',
                        'username' => 'admin',
                        'email' => 'admin@walletapp.com',
                        'password' => password_hash('admin123', PASSWORD_BCRYPT),
                        'tier' => 0, 
                        'max_transaction_amount' => NULL 
                    
                    ],
                    [
                        'first_name' => 'John',
                        'last_name' => 'Doe',
                        'username' => 'johndoe',
                        'email' => 'john.doe@example.com',
                        'password' => password_hash('password123', PASSWORD_BCRYPT),
                        'tier' => 1, 
                        'max_transaction_amount' => 50.00, 
            
                    ],
                    [
                        'first_name' => 'Jane',
                        'last_name' => 'Smith',
                        'username' => 'janesmith',
                        'email' => 'jane.smith@example.com',
                        'password' => password_hash('password123', PASSWORD_BCRYPT),
                        'tier' => 2,
                        'max_transaction_amount' => NULL
                    
                    ],
                    [
                        'first_name' => 'Alex',
                        'last_name' => 'Johnson',
                        'username' => 'alexj',
                        'email' => 'alex.johnson@example.com',
                        'password' => password_hash('password123', PASSWORD_BCRYPT),
                        'tier' => 3,
                        'max_transaction_amount' => NULL
                    ]
                ];

        foreach($users as $user) {
            $sql = "INSERT INTO users 
                    (first_name, last_name, username, email, password_hash, tier, max_transaction_amount, is_admin) 
                    VALUES ('{$user['first_name']}', 
                            '{$user['last_name']}', 
                            '{$user['username']}', 
                            '{$user['email']}', 
                            '{$user['password']}', 
                            {$user['tier']}, 
                            " . ($user['max_transaction_amount'] === NULL ? "NULL" : $user['max_transaction_amount']) . ", 
                            {$user['is_admin']})";
            
            if($conn->query($sql)) {
                echo "Inserted user: {$user['first_name']} {$user['last_name']} (Tier {$user['tier']})\n";
            } else {
                echo "Error inserting user: " . $conn->error . "\n";
            }
        }
    }
    
    public static function seedWallets($conn) {
        $wallets = [
          
            [
                'user_id' => 2,
                'card_number' => '5555555555554444',
                'card_type' => 'Mastercard',
                'cvv' => '456',
                'expiry_date' => '10/26',
                'balance' => 500.00
            ],
          
            [
                'user_id' => 3,
                'card_number' => '378282246310005',
                'card_type' => 'Amex',
                'cvv' => '789',
                'expiry_date' => '08/27',
                'balance' => 1000.00
            ],
          
            [
                'user_id' => 4,
                'card_number' => '4005519200000004',
                'card_type' => 'Visa',
                'cvv' => '321',
                'expiry_date' => '05/26',
                'balance' => 2500.00
            ],
       
            [
                'user_id' => 4,
                'card_number' => '5105105105105100',
                'card_type' => 'Mastercard',
                'cvv' => '654',
                'expiry_date' => '09/28',
                'balance' => 1500.00
            ]
        ];

        foreach($wallets as $wallet) {
            $sql = "INSERT INTO wallets 
                    (user_id, card_number, card_type, cvv, expiry_date, balance) 
                    VALUES ({$wallet['user_id']}, 
                            '{$wallet['card_number']}', 
                            '{$wallet['card_type']}', 
                            '{$wallet['cvv']}', 
                            '{$wallet['expiry_date']}', 
                            {$wallet['balance']})";
            
            if($conn->query($sql)) {
                echo "Inserted wallet for user_id: {$wallet['user_id']}\n";
            } else {
                echo "Error inserting wallet: " . $conn->error . "\n";
            }
        }
    }

    public static function seedTransactions($conn) {
        $transactions = [
     
            [
                'wallet_id' => 1,
                'note' => 'Initial deposit',
                'amount' => 50.00,
                'transaction_type' => 'deposit',
                'status' => 'completed'
            ],
        
            [
                'wallet_id' => 1,
                'note' => 'Salary deposit',
                'amount' => 450.00,
                'transaction_type' => 'deposit',
                'status' => 'completed'
            ],
      
            [
                'wallet_id' => 2,
                'note' => 'Initial deposit',
                'amount' => 1000.00,
                'transaction_type' => 'deposit',
                'status' => 'completed'
            ],
        
            [
                'wallet_id' => 3,
                'note' => 'Initial deposit',
                'amount' => 2500.00,
                'transaction_type' => 'deposit',
                'status' => 'completed'
            ],
       
            [
                'wallet_id' => 4,
                'note' => 'Initial deposit',
                'amount' => 1500.00,
                'transaction_type' => 'deposit',
                'status' => 'completed'
            ],
        
            [
                'wallet_id' => 3,
                'note' => 'Transfer to email verified user',
                'amount' => 200.00,
                'transaction_type' => 'p2p',
                'status' => 'completed',
                'to_wallet_id' => 2
            ],
       
            [
                'wallet_id' => 3,
                'note' => 'Transfer to basic user',
                'amount' => 50.00,
                'transaction_type' => 'p2p',
                'status' => 'completed',
                'to_wallet_id' => 1
            ]
        ];

        foreach($transactions as $transaction) {
            $sql = "INSERT INTO transactions 
                    (wallet_id, note, amount, transaction_type, status) 
                    VALUES ({$transaction['wallet_id']}, 
                            '{$transaction['note']}', 
                            {$transaction['amount']}, 
                            '{$transaction['transaction_type']}', 
                            '{$transaction['status']}')";
            
            if($conn->query($sql)) {
                echo "Inserted transaction for wallet_id: {$transaction['wallet_id']}\n";
                
           
                if($transaction['transaction_type'] == 'p2p') {
                    $transaction_id = $conn->insert_id;
                    $p2p_sql = "INSERT INTO p2p_transactions 
                                (transaction_id, to_wallet_id) 
                                VALUES ({$transaction_id}, {$transaction['to_wallet_id']})";
                    
                    if($conn->query($p2p_sql)) {
                        echo "Inserted p2p_transaction record (to wallet_id: {$transaction['to_wallet_id']})\n";
                    } else {
                        echo "Error inserting p2p_transaction: " . $conn->error . "\n";
                    }
                }
            } else {
                echo "Error inserting transaction: " . $conn->error . "\n";
            }
        }
    }
    
    public static function runSeeds($conn) {
        self::seedUsers($conn);
        self::seedWallets($conn);
        self::seedTransactions($conn);
    }
}
    

Seeder::runSeeds($conn);
echo "\nSeeding completed";
?>