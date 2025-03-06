<?php 
    $conn = new mysqli("localhost", "root", '$l3iyah', "si_wallet_db");


if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


?>