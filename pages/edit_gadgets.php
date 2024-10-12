<?php
include '../config/db.php';


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Fetch the gadget details
    $stmt = $conn->prepare("SELECT * FROM gadgets WHERE id = :id");
    $stmt->execute(['id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "Product not found!";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Handle photo upload
    $photo_gadget = $product['photo_gadget'];
    if ($_FILES['photo_gadget']['name']) {
        $photo_gadget = basename($_FILES['photo_gadget']['name']);
        $photo_gadget_path = '../uploads/gadgets/photo/' . basename($_FILES['photo_gadget']['name']);
        move_uploaded_file($_FILES['photo_gadget']['tmp_name'], $photo_gadget_path);
    }

    // Handle video upload
    $video_gadget = $product['video_gadget'];
    if ($_FILES['video_gadget']['name']) {
        $video_gadget = basename($_FILES['video_gadget']['name']);
        $video_gadget_path = '../uploads/gadgets/video/' . basename($_FILES['video_gadget']['name']);
        move_uploaded_file($_FILES['video_gadget']['tmp_name'], $video_gadget_path);
    }

    // Update query
    $stmt = $conn->prepare("UPDATE gadgets SET title = :title, description = :description, price = :price, photo_gadget = :photo_gadget, video_gadget = :video_gadget WHERE id = :id");
    $stmt->execute([
        'title' => $title,
        'description' => $description,
        'price' => $price,
        'photo_gadget' => $photo_gadget,
        'video_gadget' => $video_gadget,
        'id' => $product_id
    ]);

    header("Location: my_post.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Gadget</title>

        <!-- Favicon -->
  <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
        <!-- other meta tags and elements -->
    <meta name="apple-mobile-web-app-capable" content="yes">
  <!-- Android -->
    <meta name="mobile-web-app-capable" content="yes">
</head>
<body>
<?php include '../includes/header.php';?>
<h1>Edit Gadget</h1>

<form action="edit_gadgets.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">

    <label for="title">Title:</label>
    <input type="text" id="title" name="title" value="<?= htmlspecialchars($product['title']) ?>">

    <label for="description">Description:</label>
    <textarea id="description" name="description"><?= htmlspecialchars($product['description']) ?></textarea>

    <label for="price">Price:</label>
    <input type="text" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>">

    <label for="photo_gadget">Gadget Photo:</label>
    <input type="file" id="photo_gadget" name="photo_gadget">
    <p>Current Gadget Photo:</p>
    <?php if ($product['photo_gadget']): ?>
        <img src="../uploads/gadgets/photo/<?= htmlspecialchars($product['photo_gadget']) ?>" alt="Gadget Photo" width="100">
    <?php endif; ?>

    <label for="video_gadget">Gadget Video:</label>
    <input type="file" id="video_gadget" name="video_gadget">
    <p>Current Gadget Video:</p>
    <?php if ($product['video_gadget']): ?>
        <video width="320" height="240" controls>
            <source src="../uploads/gadgets/video/<?= htmlspecialchars($product['video_gadget']) ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    <?php endif; ?>

    <button type="submit" name="submit">Update</button>
</form>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
