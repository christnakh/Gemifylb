<?php
// Include database connection
include '../../config/db.php'; // Ensure this path is correct

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Query to fetch messages sorted from newest to oldest
$query = "SELECT * FROM contact_us ORDER BY submitted_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if the form is submitted to mark messages as read or unread
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_read'])) {
        $id = $_POST['message_id'];
        $update_query = "UPDATE contact_us SET `read` = 1 WHERE id = :id";
        $stmt = $conn->prepare($update_query);
        $stmt->execute(['id' => $id]);
    }
    if (isset($_POST['mark_unread'])) {
        $id = $_POST['message_id'];
        $update_query = "UPDATE contact_us SET `read` = 0 WHERE id = :id";
        $stmt = $conn->prepare($update_query);
        $stmt->execute(['id' => $id]);
    }

    // Refresh the page after marking as read/unread
    header("Location: contact_us_management.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
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
        .message-container {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 20px;
            background-color: #fff;
        }
        .message-container.unread {
            background-color: #fff3cd;
        }
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .message-header .message-info {
            display: flex;
            flex-direction: column;
        }
        .message-header .message-info span {
            font-size: 1rem;
        }
        .message-body {
            margin-top: 15px;
            font-size: 1rem;
            color: #495057;
        }
        .message-actions {
            margin-top: 10px;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
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
                            <a class="nav-link active" href="#">
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
                    <h1 class="h2">Contact Us Messages</h1>
                </header>

                <!-- Messages Flexbox -->
                <?php foreach ($messages as $row): ?>
                    <div class="message-container <?= $row['read'] ? '' : 'unread' ?>">
                        <div class="message-header">
                            <div class="message-info">
                                <span><strong>Name:</strong> <?= htmlspecialchars($row['name']) ?></span>
                                <span><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></span>
                                <span><strong>Submitted At:</strong> <?= htmlspecialchars($row['submitted_at']) ?></span>
                            </div>
                            <div>
                                <span><strong>Status:</strong> <?= $row['read'] ? 'Read' : 'Unread' ?></span>
                            </div>
                        </div>
                        <div class="message-body">
                            <strong>Subject:</strong> <?= htmlspecialchars($row['subject']) ?><br>
                            <strong>Message:</strong> <?= htmlspecialchars($row['message']) ?>
                        </div>
                        <div class="message-actions">
                            <form method="POST" action="">
                                <input type="hidden" name="message_id" value="<?= htmlspecialchars($row['id']) ?>">
                                <?php if ($row['read']): ?>
                                    <button type="submit" name="mark_unread" class="btn btn-secondary">Mark as Unread</button>
                                <?php else: ?>
                                    <button type="submit" name="mark_read" class="btn btn-primary">Mark as Read</button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </main>
        </div>
    </div>
</body>
</html>
