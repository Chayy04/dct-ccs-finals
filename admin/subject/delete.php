<?php
session_start();
ob_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

include '../partials/header.php'; // Include header
include '../partials/side-bar.php';
include '../../functions.php'; // Include functions

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
    $stmt = $conn->prepare("DELETE FROM subjects WHERE subject_code = ?");
    $stmt->bind_param("i", $subject['subject_code']);
    
    if ($stmt->execute()) {
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

        $stmt->close();
        $conn->close();
        header("Location: add.php?message=SubjectDeleted");
        exit();
    } else {
        $errors[] = "Failed to delete the subject. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>


<!-- Template -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">    
    <h1 class="h2">Delete a Subject</h1>        

    <div class="row mt-5">
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
