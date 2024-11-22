<?php
session_start();
ob_start();
$title = 'Edit Student';
include '../../functions.php';
guard();

$pathDashboard = "../dashboard.php";
$pathLogout = "../logout.php";
$pathSubjects = "../subject/add.php";
$pathStudents = "register.php";



include '../partials/header.php';
include '../partials/side-bar.php';

// Connect to the database
$conn = dbConnect();

// Check if `student_id` is provided in the URL
$student_id = $_GET['student_id'] ?? null;
if (!$student_id) {
    header("Location: register.php");
    exit();
}

// Fetch student data from the database using the student ID
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student_data = $result->fetch_assoc();
$stmt->close();

// If no student data is found, redirect to the register page
if (!$student_data) {
    header("Location: register.php");
    exit();
}

// Handle form submission for updating student data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get updated data from the form
    $updated_data = [
        'student_id' => $_POST['student_id'],
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name']
    ];

    // Update student data in the database
    $stmt = $conn->prepare("UPDATE students SET first_name = ?, last_name = ? WHERE student_id = ?");
    $stmt->bind_param("ssi", $updated_data['first_name'], $updated_data['last_name'], $updated_data['student_id']);
    
    if ($stmt->execute()) {
        // Clear output buffer before redirecting
        ob_end_clean();
        header("Location: register.php");
        exit();
    } else {
        $errors[] = 'Failed to update student. Please try again.';
    }
    $stmt->close();
}

$conn->close();
?>

<!-- HTML Form for Editing Student -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">    
    <h1 class="h2">Edit Student</h1>     
        <div class="mt-5 mb-3 w-100">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="register.php" class="text-decoration-none">Register Student</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
                </ol>
            </nav>
        </div>

    <div class="row mt-3">
        <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
            <!-- Floating Label for Student ID (Read-Only) -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control bg-light" id="student_id" name="student_id" 
                       value="<?php echo htmlspecialchars($student_data['student_id']); ?>" readonly>
                <label for="student_id">Student ID</label>
            </div>

            <!-- Floating Label for First Name -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="first_name" name="first_name" 
                       value="<?php echo htmlspecialchars($student_data['first_name']); ?>">
                <label for="first_name">First Name</label>
            </div>

            <!-- Floating Label for Last Name -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="last_name" name="last_name" 
                       value="<?php echo htmlspecialchars($student_data['last_name']); ?>">
                <label for="last_name">Last Name</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Update Student</button>
        </form>
    </div>    
</main>

<?php
include '../partials/footer.php';
ob_end_flush();
?>
