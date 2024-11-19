<?php
session_start();
ob_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

include '../partials/header.php'; // Include header here
include '../partials/side-bar.php';
include '../../functions.php';

// Database connection
$conn = dbConnect(); // Replace with your function for database connection

// Get the student ID from the query string
$student_id = $_GET['student_id'] ?? null;

if (!$student_id) {
    header("Location: register.php");
    exit();
}

// Fetch the student record from the database
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    header("Location: register.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete the student record from the database
    $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    
    if ($stmt->execute()) {
        // Redirect back to the register page after successful deletion
        header("Location: register.php");
        exit();
    } else {
        $error = "Failed to delete the student record. Please try again.";
    }
}

$conn->close();

?>

<!-- Template Files here -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">    
    <h1 class="h2">Delete a Student</h1>        
    
    <div class="row mt-5">
        <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
            <div class="mb-2">
                <label class="form-label fs-5">Are you sure you want to delete the following student record?</label> 
                    <ul style="list-style-type:disc;">
                        <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></li>
                        <li><strong>First Name:</strong> <?php echo htmlspecialchars($student['first_name']); ?></li>
                        <li><strong>Last Name:</strong>  <?php echo htmlspecialchars($student['last_name']); ?></li>
                    </ul>

                <!-- Buttons for Submit and Cancel -->
                <div>
                    <a href="register.php" class="btn btn-secondary btn-sm">Cancel</a> 
                    <button type="submit" class="btn btn-primary btn-sm">Delete Student Record</button>
                </div>  
            </div>
        </form>
    </div>    
</main>

<?php
include '../partials/footer.php'; // Include footer here
ob_end_flush();
?>
