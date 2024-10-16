<?php
include('config.php');

// Get the notification ID from the request
$data = json_decode(file_get_contents("php://input"));

if (isset($data->id)) {
    $notificationId = $data->id;

    // Delete the notification from the database
    $query = "DELETE FROM notifications WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $notificationId);
    $stmt->execute();

    // Send a response back
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
?>
