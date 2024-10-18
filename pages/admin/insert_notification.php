<?php
session_start();
include "../../config/db.php";

// Check if the user is logged in and has the role 'admin'
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redirect if the user is not an admin or not logged in
    header("Location: login.php");
    exit(); // Ensure the script stops executing after the redirect
}
// Initialize alert message variable
$alertMessage = "";

$Sender = $_SESSION['user_id']; // Fetch sender ID from session
$Receiver = $_POST['id_ofReceiver']; // Fetch receiver ID from form
$message = $_POST['message']; // Fetch message from form

try {
    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO notifications (Sender, Receiver, message, created_at, read_status) 
                            VALUES (:Sender, :Receiver, :message, NOW(), 0)");

    // Bind parameters
    $stmt->bindParam(':Sender', $Sender, PDO::PARAM_INT);
    $stmt->bindParam(':Receiver', $Receiver, PDO::PARAM_INT);
    $stmt->bindParam(':message', $message);

    // Execute the statement
    $stmt->execute();

    // Set alert message
    $alertMessage = "Notification inserted successfully.";
} catch(PDOException $e) {
    $alertMessage = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Notification</title>
    <!-- Include a simple JavaScript script for displaying alert -->
    <script>
        // JavaScript function to display an alert message
        function showAlert(message) {
            alert(message);
        }

        // Check if PHP has set an alert message and display it using JavaScript
        <?php
        if (!empty($alertMessage)) {
            echo "window.onload = function() { showAlert('$alertMessage'); }";
        }
        ?>
    </script>
</head>
<body>
    <h2>Insert Notification</h2>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <!-- Since Sender ID is automatically set from session, it's hidden in the form -->
        <input type="hidden" id="id_ofSender" name="id_ofSender" value="<?php echo $Sender; ?>">
        
        <label for="id_ofReceiver">Receiver ID:</label>
        <input type="number" id="id_ofReceiver" name="id_ofReceiver" required><br><br>
        
        <label for="message">Message:</label>
        <textarea id="message" name="message" required></textarea><br><br>
        
        <input type="submit" value="Insert Notification">
    </form>
</body>
</html>
