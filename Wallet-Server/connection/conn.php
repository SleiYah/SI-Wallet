<?php 
   
    $conn = new mysqli("localhost", "root", "", "si_wallet_db");


if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";


?>