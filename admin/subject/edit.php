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
    <h1 class="h2">Edit Subject</h1>        
    
    <div class="row mt-5">
    <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">

            <!-- Floating Label for Subject Code -->
            <div class="form-floating mb-3">
                <input type="number" class="form-control bg-light" id="subject_code" name="subject_code" 
                       placeholder="Student ID" value="" >
                <label for="subject_code">Subject ID</label>
            </div>

            <!-- Floating Label for Subject Name -->
            <div class="form-floating mb-3">
                <input type="number" class="form-control bg-light" id="subject_name" name="subject_name" 
                       placeholder="Subject Name" value="" >
                <label for="subject_name">Subject Name</label>
            </div>

            <button type="submit" class="btn btn-primary w-100">Update Subject</button>
        </form>

    </div>    
</main>

<?php
include '../partials/footer.php'; // Include footer here
?>
