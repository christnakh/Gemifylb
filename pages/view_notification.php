<?php
include "../config/db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch notifications where the logged-in user is Receiver
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE Receiver = :user_id ORDER BY created_at DESC");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Update notifications to mark them as read when displaying them
    foreach ($notifications as $notification) {
        if ($notification['read_status'] == 0) {
            // Mark notification as read in the database
            $updateStmt = $conn->prepare("UPDATE notifications SET read_status = 1 WHERE id = :notification_id");
            $updateStmt->bindParam(':notification_id', $notification['id'], PDO::PARAM_INT);
            $updateStmt->execute();

            // Update the read_status in the fetched data to reflect the change
            $notification['read_status'] = 1;
        }
    }
} catch(PDOException $e) {
    echo "Error fetching notifications: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
      <!-- Favicon -->
  <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
      <!-- other meta tags and elements -->
    <meta name="apple-mobile-web-app-capable" content="yes">
  <!-- Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <style>
        /* Basic styling for notification display */
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .notification {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }
        .notification h3 {
            margin-top: 0;
        }
        .notification p {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <h2>Notifications</h2>
    <?php if (!empty($notifications)) : ?>
        <?php foreach ($notifications as $notification) : ?>
            <div class="notification">
                <h3>From: <?php echo ($notification['Sender'] == $user_id) ? 'You' : 'User ' . $notification['Sender']; ?></h3>
                <p><strong>Message:</strong> <?php echo htmlspecialchars($notification['message']); ?></p>
                <p><strong>Sent at:</strong> <?php echo $notification['created_at']; ?></p>
                <p><strong>Status:</strong> <?php echo ($notification['read_status'] == 0) ? 'Unread' : 'Read'; ?></p>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p>No notifications found.</p>
    <?php endif; ?>
</body>
</html>