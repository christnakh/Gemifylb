<?php
include '../config/db.php';


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];

    // Fetch the diamond details
    $stmt = $conn->prepare("SELECT * FROM diamond WHERE id = :id");
    $stmt->execute(['id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "Product not found!";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $nature = $_POST['nature'];
    $shape = $_POST['shape'];
    $weight = $_POST['weight'];
    $cut_type = $_POST['cut_type'];
    $price = $_POST['price'];

    // Initialize file paths
    $photo_certificate = $product['photo_certificate'];
    $photo_diamond = $product['photo_diamond'];
    $video_diamond = $product['video_diamond'];

    // Handle file uploads
    if ($_FILES['photo_certificate']['name']) {
        $photo_certificate = '../uploads/diamond/photo/' . basename($_FILES['photo_certificate']['name']);
        move_uploaded_file($_FILES['photo_certificate']['tmp_name'], $photo_certificate);
    }

    if ($_FILES['photo_diamond']['name']) {
        $photo_diamond = '../uploads/diamond/photo/' . basename($_FILES['photo_diamond']['name']);
        move_uploaded_file($_FILES['photo_diamond']['tmp_name'], $photo_diamond);
    }

    if ($_FILES['video_diamond']['name']) {
        $video_diamond = '../uploads/diamond/video/' . basename($_FILES['video_diamond']['name']);
        move_uploaded_file($_FILES['video_diamond']['tmp_name'], $video_diamond);
    }

    // Update query
    $stmt = $conn->prepare("UPDATE diamond SET nature = :nature, shape = :shape, weight = :weight, cut_type = :cut_type, price/ct = :price, photo_certificate = :photo_certificate, photo_diamond = :photo_diamond, video_diamond = :video_diamond WHERE id = :id");
    $stmt->execute([
        'nature' => $nature,
        'shape' => $shape,
        'weight' => $weight,
        'cut_type' => $cut_type,
        'price' => $price,
        'photo_certificate' => $photo_certificate,
        'photo_diamond' => $photo_diamond,
        'video_diamond' => $video_diamond,
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
    <title>Edit Diamond</title>
</head>s
<body>

<h1>Edit Diamond</h1>

<form action="edit_diamond.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">

    <label for="nature">Nature:</label>
    <input type="text" id="nature" name="nature" value="<?= htmlspecialchars($product['nature']) ?>">

    <label for="shape">Shape:</label>
    <input type="text" id="shape" name="shape" value="<?= htmlspecialchars($product['shape']) ?>">

    <label for="weight">Weight:</label>
    <input type="text" id="weight" name="weight" value="<?= htmlspecialchars($product['weight']) ?>">

    <label for="cut_type">Cut Type:</label>
    <input type="text" id="cut_type" name="cut_type" value="<?= htmlspecialchars($product['cut_type']) ?>">

    <label for="price">Price per Carat:</label>
    <input type="text" id="price" name="price" value="<?= htmlspecialchars($product['price/ct']) ?>">

    <label for="photo_certificate">Certificate Photo:</label>
    <input type="file" id="photo_certificate" name="photo_certificate">
    <p>Current Certificate Photo:</p>
    <?php if ($product['photo_certificate']): ?>
        <img src="<?= htmlspecialchars($product['photo_certificate']) ?>" alt="Certificate Photo" width="100">
    <?php endif; ?>

    <label for="photo_diamond">Diamond Photo:</label>
    <input type="file" id="photo_diamond" name="photo_diamond">
    <p>Current Diamond Photo:</p>
    <?php if ($product['photo_diamond']): ?>
        <img src="<?= htmlspecialchars($product['photo_diamond']) ?>" alt="Diamond Photo" width="100">
    <?php endif; ?>

    <label for="video_diamond">Diamond Video:</label>
    <input type="file" id="video_diamond" name="video_diamond">
    <p>Current Diamond Video:</p>
    <?php if ($product['video_diamond']): ?>
        <video src="<?= htmlspecialchars($product['video_diamond']) ?>" width="200" controls></video>
    <?php endif; ?>

    <button type="submit" name="submit">Update</button>
</form>

<?php include '../includes/footer.php'; ?>


</body>
</html>
