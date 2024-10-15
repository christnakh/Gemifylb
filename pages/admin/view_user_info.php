<?php
session_start();
include('../../config/db.php'); // Database connection file

// Check if the user is logged in and has the role 'admin'
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redirect if the user is not an admin or not logged in
    header("Location: login.php");
    exit(); // Ensure the script stops executing after the redirect
}


$user_id = $_POST['id']; // Get the user ID from POST

// Fetch user details based on the provided user ID
$query = "SELECT * FROM users WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $user_id);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found");
}

// Handle approval or disapproval
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['toggle_approval'])) {
        $is_approved = $user['is_approved'] == 1 ? 0 : 1; // Toggle approval status

        // Update the approval status
        $updateQuery = "UPDATE users SET is_approved = :is_approved WHERE id = :id";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':is_approved', $is_approved);
        $updateStmt->bindParam(':id', $user_id);
        $updateStmt->execute();

        // Redirect back to the same page using POST method
        header("Location: user_management.php"); // Return to the user management page
        exit;
    }

    // Handle user deletion
    if (isset($_POST['delete'])) {
        $deleteQuery = "DELETE FROM users WHERE id = :id";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bindParam(':id', $user_id);
        $deleteStmt->execute();

        // Redirect to user management page after deletion
        header("Location: user_management.php"); // Return to the user management page
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User Info</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f7fa;
        }
        .container {
            margin-top: 30px;
            padding: 20px;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .user-info {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .user-info div {
            flex: 1 1 45%; /* Adjusts the width of the items */
            margin: 10px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f8f9fa;
        }
        .user-info img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .status-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 20px 0;
        }
        .status-label {
            font-weight: bold;
        }
        .btn-toggle {
            margin-top: 10px;
        }
        .btn-danger {
            background-color: #e63946;
            border-color: #e63946;
        }
        .btn-danger:hover {
            background-color: #d62839;
            border-color: #d62839;
        }
        .btn-success {
            background-color: #2a9d8f;
            border-color: #2a9d8f;
        }
        .btn-success:hover {
            background-color: #21867a;
            border-color: #21867a;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">User Details</h2>
        <div class="user-info">
            <div><strong>ID:</strong> <?= htmlspecialchars($user['id']); ?></div>
            <div><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></div>
            <div><strong>Full Name:</strong> <?= htmlspecialchars($user['full_name']); ?></div>
            <div><strong>Username:</strong> <?= htmlspecialchars($user['username']); ?></div>
            <div><strong>Phone Number:</strong> <?= htmlspecialchars($user['phone_number']); ?></div>
            <div><strong>Role:</strong> <?= htmlspecialchars($user['role']); ?></div>
            <?php if ($user['role'] == 'business') : ?>
                <div>
                    <strong>Business Certificate:</strong>
                    <img src="../../uploads/user/business_certificate/<?= htmlspecialchars($user['business_certificate']); ?>" alt="Business Certificate">
                </div>
            <?php endif; ?>
            <div>
                <strong>Profile Picture:</strong>
                <img src="../../uploads/user/profile_picture/<?= htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
            </div>
            <div>
                <strong>Passport Photo:</strong>
                <img src="../../uploads/user/passport_photo/<?= htmlspecialchars($user['passport_photo']); ?>" alt="Passport Photo">
            </div>
            <div>
                <strong>Front ID Photo:</strong>
                <img src="../../uploads/user/front_id_photo/<?= htmlspecialchars($user['front_id_photo']); ?>" alt="Front ID">
            </div>
            <div>
                <strong>Back ID Photo:</strong>
                <img src="../../uploads/user/back_id_photo/<?= htmlspecialchars($user['back_id_photo']); ?>" alt="Back ID">
            </div>
        </div>

        <!-- Approval Status -->
        <div class="status-container">
            <div class="status-label">
                <strong>Status:</strong> <?= $user['is_approved'] == 1 ? 'Approved' : 'Disapproved'; ?>
            </div>
        </div>
        <form method="post" action="view_user_info.php">
            <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']); ?>">
            <button type="submit" name="toggle_approval" class="btn btn-toggle <?= $user['is_approved'] == 1 ? 'btn-danger' : 'btn-success'; ?>">
                <?= $user['is_approved'] == 1 ? 'Disapprove' : 'Approve'; ?>
            </button>
        </form>

        <!-- Delete User -->
        <form method="post" action="view_user_info.php">
            <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']); ?>">
            <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete User</button>
        </form>

        <!-- Back Button -->
        <a href="user_management.php" class="btn btn-secondary mt-3">Back</a>
    </div>
</body>
</html>
