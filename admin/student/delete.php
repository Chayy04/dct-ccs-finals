<?php
ob_start();
session_start();

$pathDashboard = "../dashboard.php";
$pathLogout = "../logout.php";
$pathSubjects = "../subject/add.php";
$pathStudents = "register.php";

include '../partials/header.php';
include '../partials/side-bar.php';
include '../../functions.php';

// Ensure the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

// Get student_id from the query string
$student_id = isset($_GET['student_id']) ? trim($_GET['student_id']) : null;
$student = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission to delete the student
    $student_id = isset($_POST['student_id']) ? trim($_POST['student_id']) : null;

    if ($student_id) {
        $conn = dbConnect();
        $conn->begin_transaction();

        try {
            // Delete the student's subjects
            $stmt = $conn->prepare("DELETE FROM students_subjects WHERE student_id = ?");
            $stmt->bind_param("s", $student_id);
            $stmt->execute();
            $stmt->close();

            // Delete the student record
            $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
            $stmt->bind_param("s", $student_id);
            $stmt->execute();
            $stmt->close();

            // Commit the transaction
            $conn->commit();
            $conn->close();

            // Redirect to the student list
            header("Location: register.php");
            exit();
        } catch (Exception $e) {
            // Rollback the transaction on error
            $conn->rollback();
            $conn->close();
            $error = "Error deleting the student: " . $e->getMessage();
        }
    } else {
        $error = "Invalid student ID.";
    }
} elseif ($student_id) {
    // Fetch the student's details to confirm deletion
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT student_id, first_name, last_name FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();
    $conn->close();

    if (!$student) {
        $error = "Student not found.";
    }
} else {
    $error = "No student ID provided.";
}

?>

<!-- Template Files here -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">
    <h1 class="h2">Delete Student</h1>
        <div class="mt-5 mb-3 w-100">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="register.php" class="text-decoration-none">Register Student</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Delete Student</li>
                </ol>
            </nav>
        </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <a href="register.php" class="btn btn-secondary btn-sm">Back to Student List</a>
    <?php elseif ($student): ?>
        <div class="row mt-3">
            <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
                <div class="mb-2">
                    <label class="form-label fs-5">Are you sure you want to delete this student?</label>
                    <ul style="list-style-type:disc;">
                        <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></li>
                        <li><strong>First Name:</strong> <?php echo htmlspecialchars($student['first_name']); ?></li>
                        <li><strong>Last Name:</strong> <?php echo htmlspecialchars($student['last_name']); ?></li>
                    </ul>

                    <!-- Buttons for confirmation -->
                    <div>
                        <a href="register.php" class="btn btn-secondary btn-sm">Cancel</a>
                        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['student_id']); ?>">
                        <button type="submit" class="btn btn-primary btn-sm">Delete Student</button>
                    </div>
                </div>
            </form>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">No student selected for deletion.</div>
        <a href="register.php" class="btn btn-secondary btn-sm">Back to Student List</a>
    <?php endif; ?>
</main>

<?php
include '../partials/footer.php'; // Include footer
ob_end_flush();
?>
