<?php
session_start();
include_once '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}


// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid request method']);
    exit();
}

// Validate and sanitize input
$user_id = intval($_SESSION['user_id']);
$product_id = intval($_POST['id']);
$product_type = htmlspecialchars($_POST['type']);

// Validate product_type
$valid_types = ['diamond', 'gemstone', 'jewelry', 'black_diamond', 'gadget', 'watch'];
if (!in_array($product_type, $valid_types)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid product type']);
    exit();
}

try {
    // Check if the product is already favorited
    $queryCheck = "SELECT COUNT(*) FROM user_favorites WHERE user_id = :user_id AND product_id = :product_id AND product_type = :product_type";
    $stmtCheck = $conn->prepare($queryCheck);
    $stmtCheck->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmtCheck->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmtCheck->bindParam(':product_type', $product_type, PDO::PARAM_STR);
    $stmtCheck->execute();
    $isFavorited = $stmtCheck->fetchColumn() > 0;

    if ($isFavorited) {
        // Remove from favorites
        $queryDelete = "DELETE FROM user_favorites WHERE user_id = :user_id AND product_id = :product_id AND product_type = :product_type";
        $stmtDelete = $conn->prepare($queryDelete);
        $stmtDelete->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmtDelete->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmtDelete->bindParam(':product_type', $product_type, PDO::PARAM_STR);
        $stmtDelete->execute();
        $status = 'removed';
    } else {
        // Add to favorites
        $queryInsert = "INSERT INTO user_favorites (user_id, product_id, product_type) VALUES (:user_id, :product_id, :product_type)";
        $stmtInsert = $conn->prepare($queryInsert);
        $stmtInsert->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmtInsert->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmtInsert->bindParam(':product_type', $product_type, PDO::PARAM_STR);
        $stmtInsert->execute();
        $status = 'added';
    }

    // Send response
    echo json_encode(['status' => $status]);

} catch(PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => $e->getMessage()]);
    die();
}

?>
