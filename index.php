<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title></title>
</head>
<?php
session_start();
include 'functions.php';

// Redirect logged-in users to the dashboard
if (isset($_SESSION['user'])) {
    header("Location: admin/dashboard.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate input using your custom function
    $errors = validateLoginCredentials($email, $password);

    // If no validation errors, proceed to check credentials
    if (empty($errors)) {
        $hashedPassword = md5($password); // MD5 hashing

        // Connect to the database and check credentials
        $conn = dbConnect();
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $hashedPassword);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['user'] = $email; // Store session data
            header("Location: admin/dashboard.php");
            exit();
        } else {
            $errors[] = "Invalid email or password!";
        }
        $stmt->close();
        $conn->close();
    }
}
?>



<body class="bg-secondary-subtle">
    <div class="d-flex align-items-center justify-content-center vh-100">
        <div class="col-3">
            <!-- Server-Side Validation Messages should be placed here -->
                <?php 
                if (!empty($errors)) {
                   // echo "<div class='alert alert-danger'>";
                    echo displayErrors($errors);

                }
                ?>
 
            <div class="card">
                <div class="card-body">
                    <h1 class="h3 mb-4 fw-normal">Login</h1>
                    <form method="post" action="">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="email" name="email" placeholder="user1@example.com">
                            <label for="email">Email address</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                            <label for="password">Password</label>
                        </div>
                        <div class="form-floating mb-3">
                            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                        </div>
                    </form>
                    <!-- <?php// if (isset($error)) echo "<p>$error</p>"; ?> -->
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>