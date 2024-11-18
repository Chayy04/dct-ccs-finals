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

function validateLoginCredentials($email, $password) {
    $arrErrors = [];
    $email = htmlspecialchars(stripslashes(trim($email)));
    $password = htmlspecialchars(stripslashes(trim($password)));

    if (empty($email)) {
        $arrErrors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $arrErrors[] = 'Invalid email.';
    }

    if (empty($password)) {
        $arrErrors[] = 'Password is required.';
    }

    return $arrErrors;
}

function checkLoginCredentials($email, $password, $users) {
    if (isset($users[$email]) && $users[$email] === $password) {
        return true;
    }
    return false; 
}

function displayErrors($errors) {
    $output = "<ul class='mb-0'>";
    foreach ($errors as $error) {
        $output .= "<li>" . htmlspecialchars($error) . "</li>";
    }
    $output .= "</ul>";
    return $output;
}

?>