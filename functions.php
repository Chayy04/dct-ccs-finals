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
    $output = '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    $output .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    $output .= '<strong>System Errors</strong>';
    $output .= '<ul>';
        foreach ($errors as $error) {
            $output .= "<li>" . htmlspecialchars($error) . "</li>";
        }
    $output .= "</ul>";
    $output .= '</div>';
    return $output;
}

// student
function validateStudentData($student_data) {
    $errors = [];
    
    // Validate Student ID
    if (empty($student_data['student_id'])) {
        $errors[] = "Student ID is required.";
    }
    
    // Validate First Name
    if (empty($student_data['first_name'])) {
        $errors[] = "First Name is required.";
    }
    
    // Validate Last Name
    if (empty($student_data['last_name'])) {
        $errors[] = "Last Name is required.";
    }
    
    return $errors;
}

function checkDuplicateStudentData($student_id, $conn) {
    $stmt = $conn->prepare("SELECT student_id FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->store_result();
    
    $isDuplicate = $stmt->num_rows > 0;
    $stmt->close();
    
    return $isDuplicate ? ["Duplicate Student ID."] : [];
}

function getSelectedStudentIndex($student_id) {
    if (isset($_SESSION['students'])) {
        foreach ($_SESSION['students'] as $index => $student) {
            if ($student['student_id'] === $student_id) {
                return $index;
            }
        }
    }
    return null;  // Return null if student is not found
}

function getSelectedStudentData($index) {
    return isset($_SESSION['students'][$index]) ? $_SESSION['students'][$index] : null;
}

//sybject
function validateSubjectData($subject_data) {
    $arrErrors = [];

    if (empty($subject_data['subject_code'])) {
        $arrErrors[] = "Subject code is required.";
    }

    if (empty($subject_data['subject_name'])) {
        $arrErrors[] = "Subject name is required.";
    }

    return $arrErrors;
}

function checkDuplicateSubjectData($subject_data) {
    $arrErrors = [];

    foreach ($_SESSION['subjects'] as $subject) {
        if ($subject['subject_code'] === $subject_data['subject_code'] || $subject['subject_name'] === $subject_data['subject_name']) {
            $arrErrors[] = "Duplicate Subject";
            break;
        }
    }

    return $arrErrors;
}

function getSelectedSubjectIndex($subject_code) {
    foreach ($_SESSION['subjects'] as $index => $subject) {
        if ($subject['subject_code'] === $subject_code) {
            return $index;
        }
    }
    return null;  // Return null if subject is not found
}

function getSelectedSubjectData($index) {
    return isset($_SESSION['subjects'][$index]) ? $_SESSION['subjects'][$index] : null;
}   


?>