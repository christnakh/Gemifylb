<?php
include '../config/db.php';


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Fetch the gemstone details
    $stmt = $conn->prepare("SELECT * FROM gemstone WHERE id = :id");
    $stmt->execute(['id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "Product not found!";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $gemstone_name = $_POST['gemstone_name'];
    $shape = $_POST['shape'];
    $weight = $_POST['weight'];
    $color = $_POST['color'];
    $price = $_POST['price'];

    // Handle image upload
    $photo_certificate = $product['photo_certificate'];
    if ($_FILES['photo_certificate']['name']) {
        $photo_certificate = '../uploads/gemstone/photo_certificate/' . basename($_FILES['photo_certificate']['name']);
        move_uploaded_file($_FILES['photo_certificate']['tmp_name'], $photo_certificate);
    }

    $photo_gemstone = $product['photo_gemstone'];
    if ($_FILES['photo_gemstone']['name']) {
        $photo_gemstone = '../uploads/gemstone/photo_gemstone/' . basename($_FILES['photo_gemstone']['name']);
        move_uploaded_file($_FILES['photo_gemstone']['tmp_name'], $photo_gemstone);
    }

    // Handle video upload
    $video_gemstone = $product['video_gemstone'];
    if ($_FILES['video_gemstone']['name']) {
        $video_gemstone = '../uploads/gemstone/video/' . basename($_FILES['video_gemstone']['name']);
        move_uploaded_file($_FILES['video_gemstone']['tmp_name'], $video_gemstone);
    }

    // Update query
    $stmt = $conn->prepare("UPDATE gemstone SET gemstone_name = :gemstone_name, shape = :shape, weight = :weight, color = :color, price/ct = :price, photo_certificate = :photo_certificate, photo_gemstone = :photo_gemstone, video_gemstone = :video_gemstone WHERE id = :id");
    $stmt->execute([
        'gemstone_name' => $gemstone_name,
        'shape' => $shape,
        'weight' => $weight,
        'color' => $color,
        'price' => $price,
        'photo_certificate' => $photo_certificate,
        'photo_gemstone' => $photo_gemstone,
        'video_gemstone' => $video_gemstone,
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
    <title>Edit Gemstone</title>

        <!-- Favicon -->
  <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
        <!-- other meta tags and elements -->
    <meta name="apple-mobile-web-app-capable" content="yes">
  <!-- Android -->
    <meta name="mobile-web-app-capable" content="yes">
</head>
<body>
<?php include '../includes/header.php';?>
<h1>Edit Gemstone</h1>

<form action="edit_gemstone.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">

    <label for="gemstone_name">Gemstone Name:</label>
    <input type="text" id="gemstone_name" name="gemstone_name" value="<?= htmlspecialchars($product['gemstone_name']) ?>">

    <label for="shape">Shape:</label>
    <input type="text" id="shape" name="shape" value="<?= htmlspecialchars($product['shape']) ?>">

    <label for="weight">Weight:</label>
    <input type="text" id="weight" name="weight" value="<?= htmlspecialchars($product['weight']) ?>">

    <label for="color">Color:</label>
    <input type="text" id="color" name="color" value="<?= htmlspecialchars($product['color']) ?>">

    <label for="price">Price per Carat:</label>
    <input type="text" id="price" name="price" value="<?= htmlspecialchars($product['price/ct']) ?>">

    <label for="photo_certificate">Certificate Photo:</label>
    <input type="file" id="photo_certificate" name="photo_certificate">
    <p>Current Certificate Photo:</p>
    <?php if ($product['photo_certificate']): ?>
        <img src="<?= htmlspecialchars($product['photo_certificate']) ?>" alt="Certificate Photo" width="100">
    <?php endif; ?>

    <label for="photo_gemstone">Gemstone Photo:</label>
    <input type="file" id="photo_gemstone" name="photo_gemstone">
    <p>Current Gemstone Photo:</p>
    <?php if ($product['photo_gemstone']): ?>
        <img src="<?= htmlspecialchars($product['photo_gemstone']) ?>" alt="Gemstone Photo" width="100">
    <?php endif; ?>

    <label for="video_gemstone">Gemstone Video:</label>
    <input type="file" id="video_gemstone" name="video_gemstone">
    <p>Current Gemstone Video:</p>
    <?php if ($product['video_gemstone']): ?>
        <video width="320" height="240" controls>
            <source src="<?= htmlspecialchars($product['video_gemstone']) ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    <?php endif; ?>

    <button type="submit" name="submit">Update</button>
</form>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
