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
    <h1 class="h2">Assign Grade to Subject</h1>        
    
    <div class="row mt-5">
        <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
            <div class="mb-2">
                <label class="form-label fs-5">Selected Student and Subject Information</label> 
                    <ul style="list-style-type:disc;">
                        <li><strong>Student ID:</strong> </li>
                        <li><strong>Name:</strong> </li>
                        <li><strong>Subject Code:</strong> </li>
                        <li><strong>Subject Name:</strong> </li>
                    </ul>

                    <hr>
                    <!-- Assign Grade here -->
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="grade" name="grade" 
                            placeholder="Grade" value="" >
                        <label for="grade">Grade</label>
                    </div>

                 
                <div>
                    <a href="register.php" class="btn btn-secondary btn-sm">Cancel</a> 
                    <button type="submit" class="btn btn-primary btn-sm">Assign Grade to Subject</button>
                </div>  
            </div>
        </form>
    </div>    
</main>

<?php
include '../partials/footer.php'; // Include footer here
?>
