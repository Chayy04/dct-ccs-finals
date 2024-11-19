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


$errors = [];
$conn = dbConnect(); // Connect to the database

// Initialize the subjects array in the session if it doesn't exist
if (!isset($_SESSION['subjects'])) {
    $_SESSION['subjects'] = [];
}

// Process the form submission for adding a subject
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get subject data from the form
    $subject_data = [
        'subject_code' => $_POST['subject_code'],
        'subject_name' => $_POST['subject_name']
    ];

    // Validate input data using the helper function
    $errors = validateSubjectData($subject_data);

    // Check for duplicate subject data using the helper function
    if (empty($errors)) {
        $errors = checkDuplicateSubjectData($subject_data);
    }

    // No errors, insert data into the database and update session
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO subjects (subject_code, subject_name) VALUES (?, ?)");
        $stmt->bind_param("is", $subject_data['subject_code'], $subject_data['subject_name']);

        if ($stmt->execute()) {
            // Refresh session data
            $_SESSION['subjects'][] = $subject_data;

            // Clear output buffer and redirect
            ob_end_clean();
            header("Location: add.php");
            exit();
        } else {
            $errors[] = 'Failed to add subject. Please try again.';
        }
        $stmt->close();
    }
}

// Fetch all subjects from the database
$result = $conn->query("SELECT * FROM subjects");
if ($result->num_rows > 0) {
    $_SESSION['subjects'] = [];
    while ($row = $result->fetch_assoc()) {
        $_SESSION['subjects'][] = $row;
    }
}

$conn->close();
?>

<!-- Template Files here -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">   
    
    <?php 
    // Display errors if any
    if (!empty($errors)) {
        echo displayErrors($errors);
    }
    ?>

    <h1 class="h2">Add a New Subject</h1>        
    
    <div class="row mt-5">
        <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
            <!-- Floating Label for Subject Code -->
            <div class="form-floating mb-3">
                <input type="number" class="form-control bg-light" id="subject_code" name="subject_code" 
                       placeholder="Student Code" value="" >
                <label for="subject_code">Subject Code</label>
            </div>

            <!-- Floating Label for Subject Name -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control bg-light" id="subject_name" name="subject_name" 
                       placeholder="Subject Name" value="" >
                <label for="subject_name">Subject Name</label>
            </div>


            <button type="submit" class="btn btn-primary w-100">Add Subject</button>
        </form>

            <!-- List of Registered Subject with Gray Border -->             
            <div class="border border-secondary-1 p-5">
                <h5>Subject List</h5>
                <hr>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($_SESSION['subjects']) && is_array($_SESSION['subjects'])): ?>
                            <?php foreach ($_SESSION['subjects'] as $index => $subject): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($subject['subject_code']); ?></td>
                                    <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                    <td>
                                        <!-- Edit Button -->
                                        <a href="edit.php?index=<?php echo $index; ?>" class="btn btn-info btn-sm">Edit</a>

                                        <!-- Delete Button -->
                                        <a href="delete.php?index=<?php echo $index; ?>" class="btn btn-danger btn-sm">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">No subjects found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

    </div>    
</main>

<?php
include '../partials/footer.php'; // Include footer here
?>
