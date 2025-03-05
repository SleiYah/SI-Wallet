<?php
include(__DIR__ . "/../../connection/conn.php");
require __DIR__ . '/../../vendor/autoload.php';
include(__DIR__ . "/../../models/Users.php");

use PHPMailer\PHPMailer\PHPMailer;

header('Content-Type: application/json');

function sendVerificationEmail($userId, $email, $name) {
        $secret_key = bin2hex(random_bytes(16));
        $token = md5(time() . $email . $secret_key);
    
    global $conn;
    
    $updateQuery = "UPDATE users SET email_token = ? WHERE user_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("si", $token, $userId);
    $updateStmt->execute();
    
    $verifyLink = "http://192.168.146.72/projects/SI-Wallet/Wallet-Server/verification/v1/verify-email.php?token=" . $token;
    
    $mail = new PHPMailer(true);
   
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  
        $mail->SMTPAuth = true;
        $mail->Username = 'shy300602@gmail.com';
        $mail->Password = 'grds unig nppp xmxc';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        $mail->setFrom('SI-Wallet@SI-Wallet.com', 'SI-Wallet.com');
        $mail->addAddress($email, $name);
        
        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email Address';
        $mail->Body = "
            <html>
            <body>
                <h2>Hello $name,</h2>
                <p>Please click the link below to verify your email:</p>
                <p><a href='$verifyLink'>Verify My Email</a></p>
            </body>
            </html>
        ";
        
       if($mail->send()){
        echo json_encode([
            'success' => true,
            'message' => 'Verification Email sent'
        ]);
       
       } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to send verification email'
        ]);
    }
    }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $userId = $data['user_id'] ?? null;
    
    if (!$userId) {
        echo json_encode([
            'success' => false,
            'message' => 'User ID is required'
        ]);
        exit;
    }
    
    $user = new User();
    $userData = $user->read($userId);
    
    if (!$userData) {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
        exit;
    }


    if($userData['tier'] > 1){
        echo json_encode([
            'success' => false,
            'message' => 'You are already verified'
        ]);
        exit;
    }

    $emailSent = sendVerificationEmail(
        $userId,
        $userData['email'],
        $userData['first_name'] . ' ' . $userData['last_name']
    );
    
    
}


?>