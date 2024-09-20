<?php
include '../config/db.php';


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $post_id = $_POST['id'];
    $post_type = $_POST['type'];

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

    // Prepare the delete query
    $query = $conn->prepare("DELETE FROM $table WHERE id = :post_id AND user_id = :user_id");
    $query->execute(['post_id' => $post_id, 'user_id' => $user_id]);

    // Check if the deletion was successful
    if ($query->rowCount() > 0) {
        echo "Post deleted successfully.";
        header("Location: my_post.php");
    } else {
        echo "Failed to delete post. Either it doesn't exist or you don't have permission.";
        header("Location: my_post.php");
    }
} else {
    echo "Invalid request method.";
}
?>
