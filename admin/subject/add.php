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
                <input type="number" class="form-control bg-light" id="subject_name" name="subject_name" 
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
                        <?php foreach ($_SESSION['student_data'] as $index => $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                                <td>
                                    <!-- Edit Button -->
                                    <a href="edit.php?index=<?php echo $index; ?>" class="btn btn-info btn-sm">Edit</a>

                                    <!-- Delete Button -->
                                    <a href="delete.php?index=<?php echo $index; ?>" class="btn btn-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

    </div>    
</main>

<?php
include '../partials/footer.php'; // Include footer here
?>
