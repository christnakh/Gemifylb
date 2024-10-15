<?php
include('../../config/db.php');

// Check if the user is logged in and has the role 'admin'
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redirect if the user is not an admin or not logged in
    header("Location: login.php");
    exit(); // Ensure the script stops executing after the redirect
}

// Handle search query
$searchQuery = "";
if (isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
}

// Handle user approval/disapproval
if (isset($_POST['toggle_user'])) {
    $userId = $_POST['user_id'];
    $isApproved = $_POST['is_approved'];
    try {
        $stmt = $conn->prepare("UPDATE users SET is_approved = :is_approved WHERE id = :id");
        $newApprovalStatus = $isApproved ? 0 : 1; // Toggle approval status
        $stmt->bindParam(':is_approved', $newApprovalStatus, PDO::PARAM_INT);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        echo "<script>alert('User approval status updated successfully.')</script>";
        header("location:user_management.php");
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error updating user: " . $e->getMessage() . "</div>";
        header("location:user_management.php");
    }
}

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $userId = $_POST['user_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        echo "<script>alert('User deleted successfully.')</script>";
        header("location:user_management.php");
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error deleting user: " . $e->getMessage() . "</div>";
        header("location:user_management.php");
    }
}

// Fetch users
$query = "SELECT * FROM users WHERE email LIKE :search OR full_name LIKE :search OR username LIKE :search ORDER BY is_approved ASC;";
$stmt = $conn->prepare($query);
$searchTerm = "%" . $searchQuery . "%";
$stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
            background-color: #f8f9fa;
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
        .flex-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
        }
        .card {
            flex: 0 0 300px; /* Width of each card */
            margin: 10px; /* Space between cards */
            border: 1px solid #dee2e6; /* Optional border */
            border-radius: 0.25rem; /* Rounded corners */
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
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
                                <i class="fas fa-envelope"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>


            <!-- Main Content -->
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <header class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Users</h1>
                </header>

                <!-- Search Form -->
                <form method="POST" class="form-inline search-bar mb-3">
                    <input class="form-control mr-sm-2" type="text" name="search" placeholder="Search users" value="<?= htmlentities($searchQuery) ?>">
                    <button class="btn btn-outline-primary my-2 my-sm-0" type="submit">Search</button>
                </form>

                <!-- User Flexbox Container -->
                <div class="flex-container">
                    <?php if ($users): ?>
                        <?php foreach ($users as $user): ?>
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlentities($user['full_name']) ?></h5>
                                    <h6 class="card-subtitle mb-2 text-muted"><?= htmlentities($user['email']) ?></h6>
                                    <p class="card-text"><strong>Username:</strong> <?= htmlentities($user['username']) ?></p>
                                    <p class="card-text"><strong>Phone:</strong> <?= htmlentities($user['phone_number']) ?></p>
                                    <p class="card-text"><strong>Role:</strong> <?= ucfirst($user['role']) ?></p>
                                    <p class="card-text"><strong>Approved:</strong> <?= $user['is_approved'] ? 'Yes' : 'No' ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <input type="hidden" name="is_approved" value="<?= $user['is_approved'] ?>">
                                            <button type="submit" name="toggle_user" class="btn btn-sm <?= $user['is_approved'] ? 'btn-warning' : 'btn-success' ?>">
                                                <i class="fas <?= $user['is_approved'] ? 'fa-times-circle' : 'fa-check-circle' ?>"></i>
                                                <?= $user['is_approved'] ? 'Disapprove' : 'Approve' ?>
                                            </button>
                                        </form>

                                        <!-- View All Information -->
                                        <form action="view_user_info.php" method="POST" style="display: inline;">
                                            <input type="hidden" name="id" value="<?= $user['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-info">
                                                <i class="fas fa-info-circle"></i> View Info
                                            </button>
                                        </form>

                                        <!-- Delete User -->
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                            <button type="submit" name="delete_user" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info">No users found</div>
                    <?php endif; ?>
                </div>

                <!-- Back Button -->
                <a href="dashboard.php" class="btn btn-secondary mt-3">Back</a>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
