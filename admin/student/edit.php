<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit();
}

include '../partials/header.php'; // Include header here
include '../partials/side-bar.php';

?>

<!-- Template Files here -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">    
    <h1 class="h2">Edit Student</h1>        
    
    <div class="row mt-5">
    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
            <!-- Floating Label for Student ID -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control bg-light" id="student_id" name="student_id" 
                       placeholder="Student ID" value="" readonly>
                <label for="student_id">Student ID</label>
            </div>

            <!-- Floating Label for First Name -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="first_name" name="first_name" 
                       placeholder="First Name" value="" >
                <label for="first_name">First Name</label>
            </div>

            <!-- Floating Label for Last Name -->
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="last_name" name="last_name" 
                       placeholder="Last Name" value="" >
                <label for="last_name">Last Name</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Update Student</button>
        </form>

    </div>    
</main>

<?php
include '../partials/footer.php'; // Include footer here
?>
