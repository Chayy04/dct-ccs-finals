<?php
session_start();
ob_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

include '../partials/header.php';
include '../partials/side-bar.php';
include '../../functions.php';

$conn = dbConnect();

// Fetch attachment details based on `id` and `student_id`
$attachment_id = $_GET['id'] ?? null;
$student_id = $_GET['student_id'] ?? null;

if (!$attachment_id || !$student_id) {
    die("Invalid request. Missing required parameters.");
}

// Fetch details of the attached subject
$stmt = $conn->prepare("
    SELECT ss.id, s.subject_code, s.subject_name, st.student_id, st.first_name, st.last_name
    FROM students_subjects ss
    INNER JOIN subjects s ON ss.subject_id = s.id
    INNER JOIN students st ON ss.student_id = st.student_id
    WHERE ss.id = ?
");
$stmt->bind_param("i", $attachment_id);
$stmt->execute();
$result = $stmt->get_result();
$attachment = $result->fetch_assoc();
$stmt->close();

if (!$attachment) {
    die("Attachment not found.");
}

// Handle Detach Confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    detachSubject($attachment_id); // Call your custom function

    // Redirect back to attach-subject.php with the student ID
    header("Location: attach-subject.php?student_id=$student_id");
    exit();
}
?>

<!-- Template for Confirmation -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">    
    <h1 class="h2">Detach Subject</h1>        
    
    <div class="row mt-5">
        <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
            <div class="mb-2">
                <label class="form-label fs-5">Are you sure you want to detach this subject from this student record?</label> 
                <ul style="list-style-type:disc;">
                    <li><strong>Student ID:</strong> <?php echo htmlspecialchars($attachment['student_id']); ?></li>
                    <li><strong>First Name:</strong> <?php echo htmlspecialchars($attachment['first_name']); ?></li>
                    <li><strong>Last Name:</strong> <?php echo htmlspecialchars($attachment['last_name']); ?></li>
                    <li><strong>Subject Code:</strong> <?php echo htmlspecialchars($attachment['subject_code']); ?></li>
                    <li><strong>Subject Name:</strong> <?php echo htmlspecialchars($attachment['subject_name']); ?></li>
                </ul>

                <!-- Buttons for Confirmation -->
                <div>
                    <a href="attach-subject.php?student_id=<?php echo htmlspecialchars($student_id); ?>" 
                       class="btn btn-secondary btn-sm">Cancel</a> 
                    <button type="submit" class="btn btn-primary btn-sm">Detach Subject from Student</button>
                </div>  
            </div>
        </form>
    </div>    
</main>

<?php
include '../partials/footer.php';
ob_end_flush();
?>
