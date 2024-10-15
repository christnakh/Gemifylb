<?php
include '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$product = []; // Initialize product variable
$product_id = null; // Initialize product ID variable

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];

    // Fetch the black diamond details
    $stmt = $conn->prepare("SELECT * FROM black_diamonds WHERE id = :id");
    $stmt->execute(['id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "Product not found!";
        exit;
    }
}

// Handle form submission and image upload logic here
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $name = $_POST['name'];
    $shape = $_POST['shape'];
    $weight = $_POST['weight'];
    $price = $_POST['price'];

    // Initialize file paths
    $photo_certificate = $product['photo_certificate'];
    $photo_diamond = $product['photo_diamond'];
    $video_diamond = $product['video_diamond'];

    // Handle file uploads
    if ($_FILES['photo_certificate']['name']) {
        $photo_certificate = basename($_FILES['photo_certificate']['name']);
        $photo_certificate_path = '../uploads/black_diamond/certificates/' . basename($_FILES['photo_certificate']['name']);
        move_uploaded_file($_FILES['photo_certificate']['tmp_name'], $photo_certificate_path);
    }

    if ($_FILES['photo_diamond']['name']) {
        $photo_diamond = basename($_FILES['photo_diamond']['name']);
        $photo_diamond_path = '../uploads/black_diamond/photo/' . basename($_FILES['photo_diamond']['name']);
        move_uploaded_file($_FILES['photo_diamond']['tmp_name'], $photo_diamond_path);
    }

    if ($_FILES['video_diamond']['name']) {
        $video_diamond = basename($_FILES['video_diamond']['name']);
        $video_diamond_path = '../uploads/black_diamond/video/' . basename($_FILES['video_diamond']['name']);
        move_uploaded_file($_FILES['video_diamond']['tmp_name'], $video_diamond_path);
    }

    // Update query
    $stmt = $conn->prepare("UPDATE black_diamonds SET name = :name, shape = :shape, weight = :weight, `price/ct` = :price, photo_certificate = :photo_certificate, photo_diamond = :photo_diamond, video_diamond = :video_diamond, is_approved = 'Pending' WHERE id = :id");
    $stmt->execute([
        'name' => $name,
        'shape' => $shape,
        'weight' => $weight,
        'price' => $price,
        'photo_certificate' => $photo_certificate,
        'photo_diamond' => $photo_diamond,
        'video_diamond' => $video_diamond,
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
    <title>Edit Black Diamond</title>
    <!-- Favicon -->
    <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
</head>
<body>
<?php include '../includes/header.php';?>
<h1>Edit Black Diamond</h1>

<!-- Debugging output -->
<!--<h2>Debugging Output</h2>
<p>Product ID: <?= htmlspecialchars($product_id) ?></p>
<p>Name: <?= htmlspecialchars($product['name'] ?? 'N/A') ?></p>
<p>Shape: <?= htmlspecialchars($product['shape'] ?? 'N/A') ?></p>
<p>Weight: <?= htmlspecialchars($product['weight'] ?? 'N/A') ?></p>
<p>Price: <?= htmlspecialchars($product['price/ct'] ?? 'N/A') ?></p>
<p>Certificate Photo: <?= htmlspecialchars($product['photo_certificate'] ?? 'N/A') ?></p>
<p>Diamond Photo: <?= htmlspecialchars($product['photo_diamond'] ?? 'N/A') ?></p>
<p>Diamond Video: <?= htmlspecialchars($product['video_diamond'] ?? 'N/A') ?></p>-->

<form action="edit_black_diamonds.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id'] ?? '') ?>">

    <div>
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required>
    </div>

    <div>
        <label for="shape">Shape:</label>
        <input type="text" id="shape" name="shape" value="<?= htmlspecialchars($product['shape'] ?? '') ?>" required>
    </div>

    <div>
        <label for="weight">Weight:</label>
        <input type="text" id="weight" name="weight" value="<?= htmlspecialchars($product['weight'] ?? '') ?>" required>
    </div>

    <div>
        <label for="price">Price per Carat:</label>
        <input type="text" id="price" name="price" value="<?= htmlspecialchars($product['price/ct'] ?? '') ?>" required>
    </div>

    <div>
        <label for="photo_certificate">Certificate Photo:</label>
        <input type="file" id="photo_certificate" name="photo_certificate">
        <p>Current Certificate Photo:</p>
        <?php if ($product['photo_certificate']): ?>
            <img src="../uploads/black_diamond/certificates/<?= htmlspecialchars($product['photo_certificate']) ?>" alt="Certificate Photo" width="100">
        <?php endif; ?>
    </div>

    <div>
        <label for="photo_diamond">Diamond Photo:</label>
        <input type="file" id="photo_diamond" name="photo_diamond">
        <p>Current Diamond Photo:</p>
        <?php if ($product['photo_diamond']): ?>
            <img src="../uploads/black_diamond/photo/<?= htmlspecialchars($product['photo_diamond']) ?>" alt="Diamond Photo" width="100">
        <?php endif; ?>
    </div>

    <div>
        <label for="video_diamond">Diamond Video:</label>
        <input type="file" id="video_diamond" name="video_diamond">
        <p>Current Diamond Video:</p>
        <?php if ($product['video_diamond']): ?>
            <video src="../uploads/black_diamond/video/<?= htmlspecialchars($product['video_diamond']) ?>" width="200" controls></video>
        <?php endif; ?>
    </div>

    <button type="submit" name="submit">Update</button>
</form>

<?php include '../includes/footer.php'; ?>

</body>
</html>
