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
    <h1 class="h2">Dettach Subject</h1>        
    
    <div class="row mt-5">
        <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
            <div class="mb-2">
                <label class="form-label fs-5">Are you sure you want to dettach this subject from this student record?</label> 
                    <ul style="list-style-type:disc;">
                        <li><strong>Student ID:</strong> </li>
                        <li><strong>First Name:</strong> </li>
                        <li><strong>Last Name:</strong> </li>
                        <li><strong>Subject Code:</strong> </li>
                        <li><strong>Subject Name:</strong> </li>
                    </ul>

                <!-- Buttons for Dettach Subject from Student and Cancel -->
                <div>
                    <a href="register.php" class="btn btn-secondary btn-sm">Cancel</a> 
                    <button type="submit" class="btn btn-primary btn-sm">Dettach Subject from Student</button>
                </div>  
            </div>
        </form>
    </div>    
</main>

<?php
include '../partials/footer.php'; // Include footer here
?>
