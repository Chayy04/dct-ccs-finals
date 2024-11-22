<?php
session_start();
ob_start();
$title = 'Assign Grade to Subject';
include '../../functions.php';
guard();

$pathDashboard = "../dashboard.php";
$pathLogout = "../logout.php";
$pathSubjects = "../subject/add.php";
$pathStudents = "register.php";

include '../partials/header.php'; // Include header here
include '../partials/side-bar.php';

$conn = dbConnect();

// Get subject_id and student_id from the URL
$subject_id = $_GET['subject_id'] ?? null; 
$student_id = $_GET['student_id'] ?? null;

if (!$subject_id || !$student_id) {
    die("Invalid request. Please select a valid student and subject.");
}

// Fetch subject and student information for display
$stmt = $conn->prepare("
    SELECT 
        s.subject_code, 
        s.subject_name, 
        st.student_id, 
        st.first_name, 
        st.last_name
    FROM 
        subjects s
    JOIN 
        students_subjects ss ON s.id = ss.subject_id
    JOIN 
        students st ON ss.student_id = st.student_id
    WHERE 
        s.id = ? AND st.student_id = ?
");
$stmt->bind_param("ii", $subject_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    die("Subject or student not found.");
}

// Handle form submission to update grade
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grade = $_POST['grade'] ?? null;

    if ($grade !== null) {
        $stmt = $conn->prepare("
            UPDATE students_subjects 
            SET grade = ? 
            WHERE student_id = ? AND subject_id = ?
        ");
        $stmt->bind_param("dii", $grade, $student_id, $subject_id);
        $stmt->execute();
        $stmt->close();

        header("Location: attach-subject.php?student_id=$student_id");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Please enter a valid grade.</div>";
    }
}
?>

<!-- Template Files here -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">    
    <h1 class="h2">Assign Grade to Subject</h1>     
        <div class="mt-5 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="register.php" class="text-decoration-none">Register Student</a></li>
                    <li class="breadcrumb-item">
                        <a href="attach-subject.php?student_id=<?= htmlspecialchars($student_id); ?>" class="text-decoration-none">Attach Subject to Student</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Assign Grade to Subject</li>
                </ol>
            </nav>
        </div>

    <div class="row mt-3">
        <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
            <div class="mb-2">
                <label class="form-label fs-5">Selected Student and Subject Information</label> 
                <ul style="list-style-type:disc;">
                    <li><strong>Student ID:</strong> <?php echo htmlspecialchars($data['student_id']); ?></li>
                    <li><strong>Name:</strong> <?php echo htmlspecialchars($data['first_name'] . ' ' . $data['last_name']); ?></li>
                    <li><strong>Subject Code:</strong> <?php echo htmlspecialchars($data['subject_code']); ?></li>
                    <li><strong>Subject Name:</strong> <?php echo htmlspecialchars($data['subject_name']); ?></li>
                </ul>

                <hr>
                <!-- Assign Grade here -->
                <div class="form-floating mb-3">
                    <input type="number" class="form-control" id="grade" name="grade" 
                        placeholder="Grade" value="" step="0.01" required>
                    <label for="grade">Grade</label>
                </div>

                <div>
                    <a href="attach-subject.php?student_id=<?php echo htmlspecialchars($student_id); ?>" class="btn btn-secondary btn-sm">Cancel</a> 
                    <button type="submit" class="btn btn-primary btn-sm">Assign Grade to Subject</button>
                </div>  
            </div>
        </form>
    </div>    
</main>

<?php
include '../partials/footer.php'; // Include footer here
?>
