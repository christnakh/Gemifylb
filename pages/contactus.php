<?php
include '../config/db.php'; // Include your database connection

// Initialize message variable
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $messageContent = $_POST['message'];

    // Validate and sanitize input data
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $subject = filter_var($subject, FILTER_SANITIZE_STRING);
    $messageContent = filter_var($messageContent, FILTER_SANITIZE_STRING);

    if (!empty($name) && !empty($email) && !empty($subject) && !empty($messageContent)) {
        // Insert data into the database
        $stmt = $conn->prepare('INSERT INTO contact_us (name, email, subject, message) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $email, $subject, $messageContent]);

        // Set a success message
        $message = "Thank you for contacting us! We will get back to you soon.";
    } else {
        $message = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Contact Us</title>
    </head>
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <div class="container form-content mt-4 mb-4">
        <div class="row justify-content-center align-items-center signup-form-container">
            <div class="col-md-6 d-flex align-items-center flex-column">
                <div class="logo-text-container">
                    <img src="../images/logo.png" alt="Logo" class="logo-img img-fluid">
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="text-center mb-3">
                            <h1 class="h3 mb-2 font-weight-normal">Contact Us</h1>
                        </div>
                        <?php
                        if (!empty($message)) {
                            echo '<script>alert("' . $message . '");</script>';
                        }
                        ?>
                        <form action="contactus.php" method="post">
                            <div class="form-group mb-4">
                                <label for="name">Name:</label>
                                <input type="name" id="name" name="name" class="form-control rounded-pill border-0 px-4" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="email" class="form-control rounded-pill border-0 px-4" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="subject">Subject:</label>
                                <input type="text" id="subject" name="subject" class="form-control rounded-pill border-0 px-4" required>
                            </div>
                            <div class="form-group mb-4">
                                <label for="message">Message:</label>
                                <textarea id="message" name="message" class="form-control rounded-pill border-0 px-4" required></textarea>
                            </div>
                            <div class="form-group mb-4 mt-4">
                                <button type="submit" class="btn btn-submit text-uppercase rounded-pill ">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>

</html>