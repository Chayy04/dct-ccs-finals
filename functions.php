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

function checkDuplicateSubject($subject_code, $subject_name) {
    $errors = [];
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT * FROM subjects WHERE subject_name = ? AND subject_code != ?");
    $stmt->bind_param("si", $subject_name, $subject_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errors[] = "A subject with the same name already exists.";
    }

    $stmt->close();
    $conn->close();
    return $errors;
}




function getSelectedSubjectIndex($subject_code) {
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT * FROM subjects WHERE subject_code = ?");
    $stmt->bind_param("i", $subject_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $subject = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $subject;
}

function updateSubject($subject_code, $subject_name) {
    $errors = checkDuplicateSubject($subject_code, $subject_name);
    if (!empty($errors)) {
        return $errors;
    }

    $conn = dbConnect();
    $stmt = $conn->prepare("UPDATE subjects SET subject_name = ? WHERE subject_code = ?");
    $stmt->bind_param("si", $subject_name, $subject_code);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();

    return $success ? [] : ["Failed to update subject."];
}




function getSelectedSubjectData($index) {
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT * FROM subjects LIMIT 1 OFFSET ?");
    $stmt->bind_param("i", $index);
    $stmt->execute();
    $result = $stmt->get_result();
    $subject = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $subject;
}


?>