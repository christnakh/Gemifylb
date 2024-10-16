<?php
// notifications.php
include('../config/db.php');  // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");  // Redirect if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];  // Get logged-in user's ID

// Handle notification deletion
if (isset($_POST['delete_notification']) && !empty($_POST['notification_id'])) {
    $notification_id = intval($_POST['notification_id']); // Ensure it's an integer

    // Prepare the delete query
    $deleteQuery = "DELETE FROM notifications WHERE id = :id AND Receiver = :receiver";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bindParam(':id', $notification_id, PDO::PARAM_INT);
    $deleteStmt->bindParam(':receiver', $user_id, PDO::PARAM_INT);

    // Execute the delete query
    $deleteStmt->execute();
    // Optionally add a success message (you could display this on the page)
    $success_message = "Notification deleted successfully.";
    ?>
        <script>
            alert("Notification deleted successfully.");
        </script>
    <?php
}

// Fetch notifications for the logged-in user and join with relevant product tables to get the product name
$query = "
    SELECT notifications.*, 
           COALESCE(diamond.nature, gemstone.gemstone_name, jewelry.title, gadgets.title, watches.title) AS product_name
    FROM notifications
    LEFT JOIN diamond ON notifications.message LIKE CONCAT('%diamond post with ID ', diamond.id, '%')
    LEFT JOIN gemstone ON notifications.message LIKE CONCAT('%gemstone post with ID ', gemstone.id, '%')
    LEFT JOIN jewelry ON notifications.message LIKE CONCAT('%jewelry post with ID ', jewelry.id, '%')
    LEFT JOIN gadgets ON notifications.message LIKE CONCAT('%gadgets post with ID ', gadgets.id, '%')
    LEFT JOIN watches ON notifications.message LIKE CONCAT('%watches post with ID ', watches.id, '%')
    WHERE Receiver = :receiver
    ORDER BY notifications.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bindParam(':receiver', $user_id, PDO::PARAM_INT);
$stmt->execute();

// Mark notifications as read when the page is loaded
$updateQuery = "UPDATE notifications SET read_status = 1 WHERE Receiver = :receiver AND read_status = 0";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bindParam(':receiver', $user_id, PDO::PARAM_INT);
$updateStmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="styles.css"> <!-- Your custom CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .notification {
            border-bottom: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            display: flex;
            flex-direction: column;
        }

        .notification.unread {
            background-color: #eaf7ff;
            font-weight: bold;
        }

        .notification.read {
            background-color: #f5f5f5;
        }

        .notification-message {
            font-size: 1.1em;
            margin-bottom: 8px;
        }

        .notification-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85em;
            color: #999;
        }

        .notification-time {
            font-size: 0.85em;
            color: #999;
        }

        .delete-notification {
            background-color: transparent;
            color: #c0392b;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 0;
            transition: color 0.3s ease;
        }

        .delete-notification:hover {
            color: #e74c3c;
        }
    </style>
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="container">
    
    <?php if ($stmt->rowCount() > 0): ?>
        <!-- Loop through notifications -->
        <?php while ($notification = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="notification <?php echo ($notification['read_status'] == 0) ? 'unread' : 'read'; ?>">
                <!-- Replace the ID in the notification message with the product name -->
                <div class="notification-message">
                    <?php 
                    // Replace 'ID X' in the message with the actual product name
                    if (!empty($notification['product_name'])) {
                        // Remove 'ID X' and replace it with the product name
                        echo preg_replace(
                            '/post with ID \d+/', 
                            'post "' . htmlspecialchars($notification['product_name']) . '"', 
                            $notification['message']
                        );
                    } else {
                        echo htmlspecialchars($notification['message']);
                    }
                    ?>
                </div>

                <!-- Date and Delete Button -->
                <div class="notification-info">
                    <span class="notification-time"><?php echo date('M d, Y h:i A', strtotime($notification['created_at'])); ?></span>
                    <form method="post" style="display: inline;">
                        <input type="hidden" name="notification_id" value="<?php echo $notification['id']; ?>">
                        <button type="submit" name="delete_notification" class="delete-notification">&#10006;</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No notifications found.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>

</body>
</html>

<?php
// Close the statement and connection
$stmt = null;
$conn = null;
?>
