<?php
session_start();
ob_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

include '../../functions.php'; // Include the functions
include '../partials/header.php';

$logoutPage = '../logout.php';
$dashboardPage = '../dashboard.php';
$studentPage = '../student/register.php';
$subjectPage = './subject/add.php';
include '../partials/side-bar.php';

$errors = [];
//$student_data = [];
$conn = dbConnect(); // Connect to the database


    // Initialize the student data array if it doesn't exist
    if (!isset($_SESSION['student_data'])) {
        $_SESSION['student_data'] = [];
    }

    // Process the form submission for registering a student
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get student data from the form
    $student_data = [
        'student_id' => $_POST['student_id'],
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name']
    ];

    //Validate input data
    $errors = validateStudentData($student_data);

    //Check for duplicate student ID in the database
    if (empty($errors)) {
        $errors = checkDuplicateStudentData($student_data['student_id'], $conn);
    }

    // no errors, insert data into the database
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO students (student_id, first_name, last_name) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $student_data['student_id'], $student_data['first_name'], $student_data['last_name']);
        
        if ($stmt->execute()) {
            // Clear output buffer before redirecting
            ob_end_clean();
            header("Location: register.php");
            exit();
        } else {
            $errors[] = 'Failed to add student. Please try again.';
        }
        $stmt->close();
    }

    }
    // Fetch all students from the database
    $students = [];
    $result = $conn->query("SELECT * FROM students");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }

    // Store the fetched students in the session so they are available on page reload
    $_SESSION['student_data'] = $students;

    $conn->close();


?>

<!-- Template Files here -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5"> 
   
    <h1 class="h2">Register a New Student</h1>     
    
    <div class="mt-5 w-100">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Register Student</li>
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

            <!-- Floating Label for Student ID -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="student_id" name="student_id" 
                       placeholder="Student ID" value="" >
                <label for="student_id">Student ID</label>
            </div>

            <!-- Floating Label for First Name -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="first_name" name="first_name" 
                       placeholder="First Name" value="" >
                <label for="first_name">First Name</label>
            </div>

            <!-- Floating Label for Last Name -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="last_name" name="last_name" 
                       placeholder="Last Name" value="" >
                <label for="last_name">Last Name</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Add Student</button>
        </form>

            <!-- List of Registered Students with Gray Border -->             
            <div class="border border-secondary-1 p-5">
                <h5>Student List</h5>
                <hr>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($_SESSION['student_data']) && is_array($_SESSION['student_data'])): ?>
                            <?php foreach ($_SESSION['student_data'] as $index => $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                                    <td>
                                        <!-- Edit Button -->
                                        <a href="edit.php?student_id=<?php echo $student['student_id']; ?>" class="btn btn-info btn-sm">Edit</a>

                                        <!-- Delete Button -->
                                        <a href="delete.php?student_id=<?php echo $student['student_id']; ?>" class="btn btn-danger btn-sm">Delete</a>

                                        <!-- Attach Subject -->
                                        <a href="attach-subject.php?index=<?php echo $index; ?>" class="btn btn-warning btn-sm">Attach Subject</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No students found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
            </div>

    </div>    
</main>

<?php
include '../partials/footer.php'; // Include footer here
ob_end_flush(); // Flush the output buffer and send output
?>
