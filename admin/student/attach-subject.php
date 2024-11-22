<?php
session_start();
ob_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

$pathDashboard = "../dashboard.php";
$pathLogout = "../logout.php";
$pathSubjects = "../subject/add.php";
$pathStudents = "register.php";


include '../partials/header.php';
include '../partials/side-bar.php';
include '../../functions.php';

$conn = dbConnect();

// Get the selected student ID
$student_id = $_GET['student_id'] ?? null;

if (!$student_id) {
    die("No student selected. Please go back and select a student.");
}

// Fetch selected student information
$stmt = $conn->prepare("SELECT student_id, first_name, last_name FROM students WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    die("Student not found. Please go back and select a valid student.");
}

// Handle attaching subjects
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_ids = $_POST['subject_ids'] ?? []; // Array of selected subject IDs

    if (!empty($subject_ids)) {
        foreach ($subject_ids as $subject_id) {
            $stmt = $conn->prepare("INSERT INTO students_subjects (student_id, subject_id, grade) VALUES (?, ?, ?)");
            $grade = 0.00; // Default grade
            $stmt->bind_param("iid", $student_id, $subject_id, $grade);
            $stmt->execute();
            $stmt->close();
        }

        // Redirect to refresh the page
        header("Location: attach-subject.php?student_id=$student_id");
        exit();
    } else {
        echo "<div class='alert alert-danger'>No subjects selected. Please select at least one subject.</div>";
    }
}

// Fetch attached subjects for the student
$attached_subjects = [];
    $stmt = $conn->prepare("
        SELECT ss.id AS students_subject_id, s.id AS subject_id, s.subject_code, s.subject_name, ss.grade 
        FROM students_subjects ss
        INNER JOIN subjects s ON ss.subject_id = s.id
        WHERE ss.student_id = ?
    ");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $attached_subjects[] = $row;
    }
    $stmt->close();



// Fetch subjects not attached to the student
$available_subjects = [];
$stmt = $conn->prepare("
    SELECT id, subject_code, subject_name 
    FROM subjects 
    WHERE id NOT IN (
        SELECT subject_id 
        FROM students_subjects 
        WHERE student_id = ?
    )
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $available_subjects[] = $row;
}
$stmt->close();
?>

<!-- Template Files here -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">    
    <h1 class="h2">Attach Subject to Student</h1>        
        <div class="mt-5 mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="register.php" class="text-decoration-none">Register Student</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Attach Subject to Student</li>
                </ol>
            </nav>
        </div>

    
    <div class="row mt-3">
        <!-- Attach Subjects Form -->
        <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
            <div class="mb-3">
                <label class="form-label fs-5">Selected Student Information</label> 
                <ul style="list-style-type:disc;">
                    <li><strong>Student ID:</strong> <?php echo htmlspecialchars($student['student_id']); ?></li>
                    <li><strong>Name:</strong> <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></li>
                </ul>
            </div>
            <hr class="mt-4">

            <div>
                <?php if (count($available_subjects) > 0): ?>
                    <label class="form-label">Select Subjects:</label>
                    <div class="form-check">
                        <?php foreach ($available_subjects as $subject): ?>
                            <input type="checkbox" class="form-check-input" name="subject_ids[]" 
                                value="<?php echo htmlspecialchars($subject['id']); ?>" 
                                id="subject_<?php echo htmlspecialchars($subject['id']); ?>">
                            <label class="form-check-label" 
                                for="subject_<?php echo htmlspecialchars($subject['id']); ?>">
                                <?php echo htmlspecialchars($subject['subject_code'] . ' - ' . $subject['subject_name']); ?>
                            </label>
                            <br>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Attach Selected Subjects</button>
                <?php else: ?>
                    <p>No subjects available to attach.</p>
                <?php endif; ?>
            </div>
        </form>

        <!-- List of Attached Subjects -->
        <div class="border border-secondary-1 p-5">
            <h5>Subject List</h5>
            <hr>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Grade</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attached_subjects as $subject): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                            <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                            <td><?php echo ($subject['grade'] == 0.00) ? '--.--' : htmlspecialchars($subject['grade']); ?></td>
                            <td>
                                <!-- Detach Button -->
                                <a href="dettach-subject.php?id=<?php echo htmlspecialchars($subject['students_subject_id']); ?>&student_id=<?php echo htmlspecialchars($student_id); ?>" 
                                    class="btn btn-danger btn-sm">Detach Subject</a>

                                <!-- Assign Grade Button -->
                                <a href="assign-grade.php?subject_id=<?php echo htmlspecialchars($subject['subject_id']); ?>&student_id=<?php echo htmlspecialchars($student_id); ?>" 
                                    class="btn btn-success btn-sm">Assign Grade</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>    
</main>

<?php
include '../partials/footer.php';
ob_end_flush();
?>
