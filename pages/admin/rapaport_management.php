<?php
// Include your database connection or necessary files here
include '../../config/db.php';

// Check if the user is logged in and has the role 'admin'
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redirect if the user is not an admin or not logged in
    header("Location: login.php");
    exit(); // Ensure the script stops executing after the redirect
}


// Define file paths
$pearPath = '../../uploads/rapaport/pear.pdf';
$roundPath = '../../uploads/rapaport/round.pdf';

// Initialize an empty message variable for alerts
$message = '';
$message_type = '';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Variable to store messages
    $messages = [];

    // Handling pear.pdf upload
    if (isset($_FILES['pear_file']) && $_FILES['pear_file']['name'] !== '') {
        // Check if the previous pear.pdf exists and remove it
        if (file_exists($pearPath)) {
            unlink($pearPath);
        }
        // Save the new pear.pdf file
        if (move_uploaded_file($_FILES['pear_file']['tmp_name'], $pearPath)) {
            $messages[] = 'Pear.pdf has been uploaded successfully.';
        } else {
            $messages[] = 'Error uploading Pear.pdf.';
        }
    }

    // Handling round.php upload
    if (isset($_FILES['round_file']) && $_FILES['round_file']['name'] !== '') {
        // Check if the previous round.php exists and remove it
        if (file_exists($roundPath)) {
            unlink($roundPath);
        }
        // Save the new round.php file
        if (move_uploaded_file($_FILES['round_file']['tmp_name'], $roundPath)) {
            $messages[] = 'Round.php has been uploaded successfully.';
        } else {
            $messages[] = 'Error uploading Round.php.';
        }
    }

    // Redirect and display messages in JS alert
    header("location: rapaport_management.php?messages=" . urlencode(json_encode($messages)));
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapaport Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
               body {
    font-family: Arial, sans-serif;
}

.sidebar {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    z-index: 100;
    padding: 48px 0 0;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
}

.sidebar-sticky {
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    height: calc(100vh - 48px);
    padding-top: .5rem;
    overflow-x: hidden;
    overflow-y: auto;
}

.nav-link.active {
    color: #007bff;
}

.nav-link {
    font-size: 1.1rem;
    padding: 15px;
}

.nav-link i {
    margin-right: 10px;
}

.header {
    margin-bottom: 20px;
}

.container .row .btn {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    padding: 20px;
}

.footer {
    position: fixed;
    bottom: 0;
    width: 100%;
    background-color: #f8f9fa;
}

    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <h4 class="text-center my-4">Admin Panel</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="user_management.php">
                                <i class="fas fa-users"></i> User Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="post_management.php">
                                <i class="fas fa-newspaper"></i> Post Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="rapaport_management.php">
                                <i class="fas fa-file-alt"></i> Rapaport Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact_us_management.php">
                                <i class="fas fa-envelope"></i> Contact Us Management
                            </a>
                        </li>
                          <li class="nav-item">
                            <a class="nav-link" href="../logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <header class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Rapaport Management</h1>
                </header>

                <!-- Display Bootstrap alert messages -->
                <?php if ($message !== ''): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Form for uploading files -->
                <form action="rapaport_management.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="pear_file">Upload Pear PDF:</label>
                        <input type="file" name="pear_file" id="pear_file" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="round_file">Upload Round PDF:</label>
                        <input type="file" name="round_file" id="round_file" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Files</button>
                </form>
            </main>
        </div>
    </div>
    <script>
        window.onload = function() {
            // Check if there are messages in the URL
            const urlParams = new URLSearchParams(window.location.search);
            const messages = urlParams.get('messages');
            
            if (messages) {
                // Decode the messages and display them as alerts
                const decodedMessages = JSON.parse(decodeURIComponent(messages));
                decodedMessages.forEach(message => {
                    alert(message);  // Show alert for each message
                });
            }
        };
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
