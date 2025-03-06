<?php 
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS, POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

   
    $conn = new mysqli("localhost", "root", "S13iy@h02", "si_wallet_db");


if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


?>