<?php

    // include("conn.php");
    $data = json_decode(file_get_contents('php://input'),true);
    if(empty($data['username'])) {
        echo "its not there";
    }
    var_dump($data);
    

    ?>