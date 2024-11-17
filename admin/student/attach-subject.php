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
    <h1 class="h2">Attach Subject to Student</h1>        
    
    <div class="row mt-5">
        <form method="POST" action="" class="border border-secondary-1 p-5 mb-4">
            <div class="mb-3">
                <label class="form-label fs-5">Selected Student Information</label> 
                    <ul style="list-style-type:disc;">
                        <li><strong>Student ID:</strong> </li>
                        <li><strong>Name:</strong> </li>
                    </ul>
            </div>
            <hr class="mt-4">

            <div>
                <p>No subjects available to attach.</p>
            </div>

        </form>

            <!-- List of Subject -->             
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
                        <?php foreach ($_SESSION['student_data'] as $index => $student): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                                <td>
                                    <!-- dettach-subject Button -->
                                    <a href="dettach-subject.php?index=<?php echo $index; ?>" class="btn btn-danger btn-sm">Dettach Subject</a>

                                    <!-- ssign-grade Button -->
                                    <a href="assign-grade.php?index=<?php echo $index; ?>" class="btn btn-success btn-sm">Assign Grade</a>
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
