<?php
session_start();
ob_start();

$pathDashboard = "../dashboard.php";
$pathLogout = "../logout.php";
$pathSubjects = "add.php";
$pathStudents = "../student/register.php";


// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

include '../partials/header.php'; // Include header here
include '../partials/side-bar.php';
include '../../functions.php';


$errors = [];
$conn = dbConnect(); // Connect to the database

// Initialize the subjects array in the session if it doesn't exist
if (!isset($_SESSION['subjects'])) {
    $_SESSION['subjects'] = [];
}

// Check if editing an existing subject
$is_edit = isset($_GET['index']);
$subject = null;
$subject_code = '';
$subject_name = '';

// If editing, retrieve subject data from session
if ($is_edit) {
    $index = intval($_GET['index']);
    $subject = getSelectedSubjectData($index);

    if ($subject) {
        $subject_code = $subject['subject_code'];
        $subject_name = $subject['subject_name'];
    } else {
        $errors[] = "Subject not found.";
    }
}

// Process the form submission for adding a subject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get subject data from the form
    $subject_data = [
        'subject_code' => $_POST['subject_code'],
        'subject_name' => $_POST['subject_name']
    ];

    // Validate input data using the helper function
    $errors = validateSubjectData($subject_data);

    // Check for duplicate subject data using the helper function
    // if (empty($errors)) {
    //     $errors = checkDuplicateSubject($subject_data['subject_code'], $subject_data['subject_name']);
    // }
     // Check for duplicate subject_code in the database
// Check for duplicate subject_code in the database
if (empty($errors)) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM subjects WHERE subject_code = ?");
    $stmt->bind_param("s", $subject_data['subject_code']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        $errors[] = 'Duplicate subject code.';
    }
    $stmt->close();
}

// Check for duplicate subject_name in the database
if (empty($errors)) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM subjects WHERE subject_name = ?");
    $stmt->bind_param("s", $subject_data['subject_name']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row['count'] > 0) {
        $errors[] = 'Duplicate subject name.';
    }
    $stmt->close();
}


    // If no errors, update or insert the subject
    if (empty($errors)) {
        if ($is_edit) {
            // Update the subject in the database and session
            $stmt = $conn->prepare("UPDATE subjects SET subject_name = ? WHERE subject_code = ?");
            $stmt->bind_param("si", $subject_data['subject_name'], $subject_data['subject_code']);
            if ($stmt->execute()) {
                $_SESSION['subjects'][$index]['subject_name'] = $subject_data['subject_name'];
                header("Location: add.php");
                exit();
            } else {
                $errors[] = 'Failed to update subject. Please try again.';
            }
            $stmt->close();
        } else {
            // Insert new subject into the database and session
            $stmt = $conn->prepare("INSERT INTO subjects (subject_code, subject_name) VALUES (?, ?)");
            $stmt->bind_param("is", $subject_data['subject_code'], $subject_data['subject_name']);
            if ($stmt->execute()) {
                $_SESSION['subjects'][] = $subject_data;
                header("Location: add.php");
                exit();
            } else {
                $errors[] = 'Failed to add subject. Please try again.';
            }
            $stmt->close();
        }
    }
}

// Fetch all subjects from the database
$result = $conn->query("SELECT * FROM subjects");
if ($result->num_rows > 0) {
    $_SESSION['subjects'] = [];
    while ($row = $result->fetch_assoc()) {
        $_SESSION['subjects'][] = $row;
    }
}

$conn->close();
?>

<!-- Template Files here -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">   
    


    <h1 class="h2">Add a New Subject</h1>      
        <div class="mt-5 mb-3 w-100">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Add Subject</li>
                </ol>
            </nav>
        </div>
    
    
    <div class="row mt-3">
        <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
            
            <?php 
            // Display errors if any
            if (!empty($errors)) {
                echo displayErrors($errors);
            }
            ?>

            <!-- Floating Label for Subject Code -->
            <div class="form-floating mb-3">
                <input type="number" class="form-control bg-light" id="subject_code" name="subject_code" 
                       placeholder="Student Code" value="" >
                <label for="subject_code">Subject Code</label>
            </div>

            <!-- Floating Label for Subject Name -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control bg-light" id="subject_name" name="subject_name" 
                       placeholder="Subject Name" value="" >
                <label for="subject_name">Subject Name</label>
            </div>


            <button type="submit" class="btn btn-primary w-100">Add Subject</button>
        </form>

            <!-- List of Registered Subject with Gray Border -->             
            <div class="border border-secondary-1 p-5">
                <h5>Subject List</h5>
                <hr>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($_SESSION['subjects']) && is_array($_SESSION['subjects'])): ?>
                            <?php foreach ($_SESSION['subjects'] as $index => $subject): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                    <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                    <td>
                                        <!-- Edit Button -->
                                        <a href="edit.php?index=<?php echo $index; ?>" class="btn btn-info btn-sm">Edit</a>

                                        <!-- Delete Button -->
                                        <a href="delete.php?index=<?php echo $index; ?>" class="btn btn-danger btn-sm">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">No subjects found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

    </div>    
</main>

<?php
include '../partials/footer.php'; // Include footer here
ob_end_flush();
?>
