<?php
include '../../config/db.php';

// Check if the user is logged in and has the role 'admin'
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redirect if the user is not an admin or not logged in
    header("Location: login.php");
    exit(); // Ensure the script stops executing after the redirect
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    try {
        $stmt = $conn->prepare('UPDATE contact_us SET `read` = TRUE WHERE id = :id');
        $stmt->execute(['id' => $id]);

        header('Location: contact_us_management.php');
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
