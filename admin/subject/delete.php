<?php
session_start();
ob_start();
$title = 'Delete Subject';
include '../../functions.php';
guard();

$pathDashboard = "../dashboard.php";
$pathLogout = "../logout.php";
$pathSubjects = "add.php";
$pathStudents = "../student/register.php";

include '../partials/header.php'; // Include header
include '../partials/side-bar.php';

$errors = [];

// Fetch the subject to delete
if (isset($_GET['index'])) {
    $index = intval($_GET['index']);
    $subject = getSelectedSubjectData($index);

    if (!$subject) {
        $errors[] = "Subject not found.";
    }
} else {
    $errors[] = "No subject selected for deletion.";
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnDelete']) && $subject) {
    $conn = dbConnect();

    // Start a transaction to ensure atomic operations
    $conn->begin_transaction();

    try {
        // First, delete the student-subject relationship (the records in the students_subjects table)
        $stmt = $conn->prepare("DELETE FROM students_subjects WHERE subject_id = ?");
        $stmt->bind_param("i", $subject['id']); // Assuming subject has an 'id' field as primary key
        $stmt->execute();
        $stmt->close();

        // Now, delete the subject
        $stmt = $conn->prepare("DELETE FROM subjects WHERE id = ?");
        $stmt->bind_param("i", $subject['id']);
        $stmt->execute();
        $stmt->close();

        // Commit the transaction
        $conn->commit();

        // Re-fetch all subjects from the database
        $result = $conn->query("SELECT * FROM subjects");
        if ($result->num_rows > 0) {
            $_SESSION['subjects'] = [];
            while ($row = $result->fetch_assoc()) {
                $_SESSION['subjects'][] = $row;
            }
        } else {
            // Clear session subjects if no subjects exist
            $_SESSION['subjects'] = [];
        }

        $conn->close();
        header("Location: add.php?message=SubjectDeleted");
        exit();
    } catch (Exception $e) {
        // If any error occurs, rollback the transaction
        $conn->rollback();
        $errors[] = "Failed to delete the subject. Please try again.";
        $conn->close();
    }
}
?>

<!-- Template -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">    
    <h1 class="h2">Delete a Subject</h1>        
    <div class="mt-5 mb-3 w-100">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="add.php" class="text-decoration-none">Add Subject</a></li>
                <li class="breadcrumb-item active" aria-current="page">Delete Subject</li>
            </ol>
        </nav>
    </div>

    <div class="row mt-3">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <form method="POST" class="border border-secondary-1 p-5 mb-4">
                <label class="form-label fs-5">
                    Are you sure you want to delete the following subject record?
                </label> 
                <ul>
                    <li><strong>Subject Code:</strong> <?php echo htmlspecialchars($subject['subject_code']); ?></li>
                    <li><strong>Subject Name:</strong> <?php echo htmlspecialchars($subject['subject_name']); ?></li>
                </ul>
                <div>
                    <a href="add.php" class="btn btn-secondary btn-sm">Cancel</a> 
                    <button name="btnDelete" type="submit" class="btn btn-primary btn-sm">Delete Subject Record</button>
                </div>
            </form>
        <?php endif; ?>
    </div>    
</main>

<?php include '../partials/footer.php'; // Include footer ?>
<?php ob_end_flush(); ?>
