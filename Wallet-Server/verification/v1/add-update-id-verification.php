<?php
include(__DIR__ . "/../../models/Verifications.php");
include(__DIR__ . "/../../connection/conn.php");
include(__DIR__ . "/../../utils/jwt-auth.php");


header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Only POST requests are allowed.'
    ]);
    exit;
}
$user_id = $_POST['user_id'] ?? null;
$userData = authenticate();

if (!$user_id) {
    echo json_encode([
        'success' => false,
        'message' => 'User ID is required.'
    ]);
    exit;
}

if ($userData->user_id != $user_id) {
    echo json_encode([
        'success' => false,
        'message' => 'You do not have permission to update verification for this user'
    ]);
    exit;
}

$images_dir = __DIR__ . "../../images";
$passport_path = null;
$selfie_path = null;

if (isset($_FILES['passport_image']) && $_FILES['passport_image']['error'] == 0) {
    $passport_name = 'passport_' . $user_id . '.jpg';
    $passport_destination = $images_dir . $passport_name;
    
    if (move_uploaded_file($_FILES['passport_image']['tmp_name'], $passport_destination)) {
        $passport_path = 'images/' . $passport_name;
    }
}

if (isset($_FILES['selfie_image']) && $_FILES['selfie_image']['error'] == 0) {
    $selfie_name = 'selfie_' . $user_id . '.jpg';
    $selfie_destination = $images_dir . $selfie_name;
    
    if (move_uploaded_file($_FILES['selfie_image']['tmp_name'], $selfie_destination)) {
        $selfie_path = 'images/' . $selfie_name;
    }
}

if (!$passport_path || !$selfie_path) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to upload one or both images.'
    ]);
    exit;
}

$verification = new Verification();

$query = "SELECT * FROM verifications WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $existingVerification = $result->fetch_assoc();
    
    $verification->delete($existingVerification['verification_id']);
    
    $verificationData = [
        'user_id' => $user_id,
        'passport_image' => $passport_path,
        'selfie_image' => $selfie_path
    ];
    
    $verification_id = $verification->create($verificationData);
    
    if ($verification_id) {
        echo json_encode([
            'success' => true,
            'message' => 'Verification documents updated successfully.',
            'verification_id' => $verification_id
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update verification documents.'
        ]);
    }
} else {
    $verificationData = [
        'user_id' => $user_id,
        'passport_image' => $passport_path,
        'selfie_image' => $selfie_path
    ];
    
    $verification_id = $verification->create($verificationData);
    
    if ($verification_id) {
        echo json_encode([
            'success' => true,
            'message' => 'Verification documents submitted successfully.',
            'verification_id' => $verification_id
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to submit verification documents.'
        ]);
    }
}