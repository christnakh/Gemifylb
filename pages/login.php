<?php
session_start();
include '../config/db.php';

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); // Redirect to index page
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        echo "Login successful!";
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        header("Location: products.php");
        exit();
    } else {
        echo "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="path/to/bootstrap.css">
    <link rel="stylesheet" href="path/to/your-custom.css">
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="container form-content mt-4">
        <div class="row justify-content-center align-items-center signup-form-container">
            <div class="col-md-6 d-flex align-items-center flex-column">
                <div class="logo-text-container">
                    <img src="../images/logo.png" alt="Logo" class="logo-img img-fluid">
                    <!--<h1 class="text-white text-center">GEMIFY</h1>-->
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <h1 class="h3 mb-2 font-weight-normal">Welcome Back</h1>
                            <p class="text-muted mb-4">Please login to your account</p>
                        </div>
                        <form method="POST" action="">
                            <div class="form-group mb-4">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" class="form-control rounded-pill border-0  px-4" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="password">Password:</label>
                                <input type="password" id="password" name="password" class="form-control rounded-pill border-0 px-4" required>
                            </div>
                            <div class="form-group mb-4 mt-4">
                                <button type="submit" class="btn btn-block text-uppercase rounded-pill btn-submit">Login</button>
                                <p class="text-center mt-4">Don't have an account? <a href="signup.php" class="text-black">Register here</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>