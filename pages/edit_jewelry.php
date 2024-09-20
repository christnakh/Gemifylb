<?php
include '../config/db.php';


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Fetch the jewelry details
    $stmt = $conn->prepare("SELECT * FROM jewelry WHERE id = :id");
    $stmt->execute(['id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "Product not found!";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $title = $_POST['title'];
    $type = $_POST['type'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Handle image upload
    $photo_jewelry = $product['photo_jewelry'];
    if ($_FILES['photo_jewelry']['name']) {
        $photo_jewelry = '../uploads/jewelry/photo_jewelry/' . basename($_FILES['photo_jewelry']['name']);
        move_uploaded_file($_FILES['photo_jewelry']['tmp_name'], $photo_jewelry);
    }

    $photo_certificate = $product['photo_certificate'];
    if ($_FILES['photo_certificate']['name']) {
        $photo_certificate = '../uploads/jewelry/photo_certificate/' . basename($_FILES['photo_certificate']['name']);
        move_uploaded_file($_FILES['photo_certificate']['tmp_name'], $photo_certificate);
    }

    // Handle video upload
    $video = $product['video'];
    if ($_FILES['video']['name']) {
        $video = '../uploads/jewelry/video/' . basename($_FILES['video']['name']);
        move_uploaded_file($_FILES['video']['tmp_name'], $video);
    }

    // Update query
    $stmt = $conn->prepare("UPDATE jewelry SET title = :title, type = :type, description = :description, price = :price, photo_jewelry = :photo_jewelry, photo_certificate = :photo_certificate, video = :video WHERE id = :id");
    $stmt->execute([
        'title' => $title,
        'type' => $type,
        'description' => $description,
        'price' => $price,
        'photo_jewelry' => $photo_jewelry,
        'photo_certificate' => $photo_certificate,
        'video' => $video,
        'id' => $product_id
    ]);

    header("Location: mypost.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jewelry</title>
</head>
<body>
<?php include '../includes/header.php';?>
<h1>Edit Jewelry</h1>

<form action="edit_jewelry.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">

    <label for="title">Title:</label>
    <input type="text" id="title" name="title" value="<?= htmlspecialchars($product['title']) ?>">

    <label for="type">Type:</label>
    <input type="text" id="type" name="type" value="<?= htmlspecialchars($product['type']) ?>">

    <label for="description">Description:</label>
    <textarea id="description" name="description"><?= htmlspecialchars($product['description']) ?></textarea>

    <label for="price">Price:</label>
    <input type="text" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>">

    <label for="photo_jewelry">Jewelry Photo:</label>
    <input type="file" id="photo_jewelry" name="photo_jewelry">
    <p>Current Jewelry Photo:</p>
    <?php if ($product['photo_jewelry']): ?>
        <img src="<?= htmlspecialchars($product['photo_jewelry']) ?>" alt="Jewelry Photo" width="100">
    <?php endif; ?>

    <label for="photo_certificate">Certificate Photo:</label>
    <input type="file" id="photo_certificate" name="photo_certificate">
    <p>Current Certificate Photo:</p>
    <?php if ($product['photo_certificate']): ?>
        <img src="<?= htmlspecialchars($product['photo_certificate']) ?>" alt="Certificate Photo" width="100">
    <?php endif; ?>

    <label for="video">Jewelry Video:</label>
    <input type="file" id="video" name="video">
    <p>Current Jewelry Video:</p>
    <?php if ($product['video']): ?>
        <video width="320" height="240" controls>
            <source src="<?= htmlspecialchars($product['video']) ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    <?php endif; ?>

    <button type="submit" name="submit">Update</button>
</form>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
