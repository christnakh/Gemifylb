<?php
include '../config/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_id = $_POST['id'];
    $post_type = $_POST['type'];
    $is_active = $_POST['is_active'] == 1 ? 0 : 1; // Toggle the status

    // Map post types to their respective tables
    $tables = [
        'diamond' => 'diamond',
        'gemstone' => 'gemstone',
        'black_diamonds' => 'black_diamonds',
        'gadgets' => 'gadgets',
        'jewelry' => 'jewelry',
        'watches' => 'watches'
    ];

    // Check if the post type is valid
    if (!isset($tables[$post_type])) {
        echo "Invalid post type.";
        exit;
    }

    // Get the correct table name
    $table = $tables[$post_type];

    // Prepare the update query
    $query = $conn->prepare("UPDATE $table SET is_active = :is_active WHERE id = :id AND user_id = :user_id");
    $result = $query->execute([
        'is_active' => $is_active,
        'id' => $post_id,
        'user_id' => $_SESSION['user_id']
    ]);

    if ($result) {
        $_SESSION['message'] = "Status updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update status.";
    }

    header("Location: my_post.php");
    exit;
}
?>
