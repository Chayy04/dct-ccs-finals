<?php
session_start();
$title = 'Dashboard';
require '../functions.php';
guard();

$logoutPage = 'logout.php';
$subjectPage = './subject/add.php';
$studentPage = './student/register.php';

require './partials/header.php';
require './partials/side-bar.php';


    $conn = dbConnect();

    // Count total subjects
    $totalSubjectsQuery = "SELECT COUNT(*) AS total_subjects FROM subjects";
    $totalSubjectsResult = $conn->query($totalSubjectsQuery);
    $totalSubjects = $totalSubjectsResult->fetch_assoc()['total_subjects'];

    // Count total students
    $totalStudentsQuery = "SELECT COUNT(*) AS total_students FROM students";
    $totalStudentsResult = $conn->query($totalStudentsQuery);
    $totalStudents = $totalStudentsResult->fetch_assoc()['total_students'];

        // Count passed and failed students
        $passFailQuery = "
            SELECT st.student_id, 
                AVG(ss.grade) AS avg_grade
            FROM students st
            LEFT JOIN students_subjects ss ON st.student_id = ss.student_id
            GROUP BY st.student_id
        ";

        $result = $conn->query($passFailQuery);
        $passed = 0;
        $failed = 0;

        while ($row = $result->fetch_assoc()) {
            $avgGrade = $row['avg_grade'];

            if ($avgGrade !== null) {
                if ($avgGrade >= 75) {
                    $passed++;
                } else {
                    $failed++;
                }
            }
        }

?>

<!-- Template Files here -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 pt-5">    
    <h1 class="h2">Dashboard</h1>        
    
    <div class="row mt-5">
        <div class="col-12 col-xl-3">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white border-primary">Number of Subjects:</div>
                <div class="card-body text-primary">
                    <h5 class="card-title"><?php echo $totalSubjects; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-3">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white border-primary">Number of Students:</div>
                <div class="card-body text-primary">
                    <h5 class="card-title"><?php echo $totalStudents; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-3">
            <div class="card border-danger mb-3">
                <div class="card-header bg-danger text-white border-danger">Number of Failed Students:</div>
                <div class="card-body text-danger">
                    <h5 class="card-title"><?php echo $failed; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-3">
            <div class="card border-success mb-3">
                <div class="card-header bg-success text-white border-success">Number of Passed Students:</div>
                <div class="card-body text-success">
                    <h5 class="card-title"><?php echo $passed; ?></h5>
                </div>
            </div>
        </div>
    </div>    
</main>

<?php
include 'partials/footer.php'; 
?>