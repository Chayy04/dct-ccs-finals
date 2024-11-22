<?php
session_start();
ob_start();
$title = 'Edit Subject';
include '../../functions.php';
guard();

$pathDashboard = "../dashboard.php";
$pathLogout = "../logout.php";
$pathSubjects = "add.php";
$pathStudents = "../student/register.php";



include '../partials/header.php'; // Include header here
include '../partials/side-bar.php';

$errors = [];
$success = false;


// Check if the subject_code is provided
if (isset($_GET['index'])) {
    $index = intval($_GET['index']); // Ensure it’s an integer
    $subject = getSelectedSubjectData($index);

    if ($subject === null) {
        echo "Subject not found.";
        exit();
    }

   // $subject_code = $subject['subject_code']; // Get subject data
} else {
    echo "No subject selected for editing.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_code = $_POST['subject_code'];
    $subject_name = $_POST['subject_name'];

    $errors = updateSubject($subject_code, $subject_name);
    if (empty($errors)) {
        $success = true;
        header("Location: add.php");
        exit();
    }
}


?>

<!-- Template Files here -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">    
    <h1 class="h2">Edit Subject</h1>        
        <div class="mt-5 mb-3 w-100">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="../dashboard.php" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="add.php" class="text-decoration-none">Add Subject</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Subject</li>
                </ol>
            </nav>
        </div>

    
    <div class="row mt-5">
    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
        
        <?php if ($success): ?>
            <div class="alert alert-success">Subject updated successfully!</div>
        <?php endif; ?>

            <?php 
            // Display errors if any
            if (!empty($errors)) {
                echo displayErrors($errors);
            }
            ?>

            <!-- Floating Label for Subject Code -->
            <div class="form-floating mb-3">
                <input type="number" class="form-control bg-light" id="subject_code" name="subject_code" 
                       placeholder="Student ID" value="<?php echo htmlspecialchars($subject['subject_code']); ?>" readonly >
                <label for="subject_code">Subject ID</label>
            </div>

            <!-- Floating Label for Subject Name -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control bg-light" id="subject_name" name="subject_name" 
                       placeholder="Subject Name" value="<?php echo htmlspecialchars($subject['subject_name']); ?>" >
                <label for="subject_name">Subject Name</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Update Subject</button>
        </form>

    </div>    
</main>

<?php
include '../partials/footer.php'; // Include footer here
ob_end_flush();
?>
