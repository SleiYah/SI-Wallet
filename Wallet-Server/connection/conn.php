<?php 
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE'); 
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin, Accept'); 
header('Content-Type: application/json'); 

   
    $conn = new mysqli("localhost", "root", '$l3iyah', "si_wallet_db");


if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "test";

?>