<?php    
    // All project functions should be placed here

function dbConnect() {
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $dbname = 'dct_ccs_finals';

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}


?>