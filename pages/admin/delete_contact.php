<?php
include '../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    try {
        $stmt = $conn->prepare('DELETE FROM contact_us WHERE id = :id');
        $stmt->execute(['id' => $id]);

        header('Location: contact_us_management.php');
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
