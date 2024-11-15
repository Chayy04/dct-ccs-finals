<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

include '../partials/header.php'; // Include header here
?>

<div class="d-flex">
<?php include '../partials/side-bar.php'; // Sidebar content ?>
    
    <!-- Main Content Section (Form on the Right) -->
    <div class="main-container flex-grow-1">
        <main class="container py-5">
            <div class="row justify-content-start">
                <div class="">
                    <h2 class="">Register a New Student</h2>

                    <!-- Student Registration Form with Gray Border -->
                    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Student ID</label>
                            <input type="number" class="form-control" id="student_id" name="student_id" placeholder="Enter Student ID" required>
                        </div>

                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter First Name" required>
                        </div>

                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Add Student</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
include '../partials/footer.php'; // Include footer here
?>
