<?php 
  header("Access-Control-Allow-Origin:*");
  header("Access-Control-Allow-Methods: POST");
   header("Access-Control-Allow-Headers: *");
   header("Content-Type: application/json");

   
    $conn = new mysqli("localhost", "root", "", "si_wallet_db");


if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


?>