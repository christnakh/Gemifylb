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
    // Sanitize input
    $nature = htmlspecialchars($_POST['nature']);
    $shape = htmlspecialchars($_POST['shape']);
    $weight = htmlspecialchars($_POST['weight']);
    $cut_type = htmlspecialchars($_POST['cut_type']);
    $clarity = htmlspecialchars($_POST['clarity']);
    $color = htmlspecialchars($_POST['color']);
    $fluorescence_type = htmlspecialchars($_POST['fluorescence_type']);
    $discount_type = htmlspecialchars($_POST['discount_type']);
    $certificate = htmlspecialchars($_POST['certificate']); // New field

    // Initialize file paths
    $photo_certificate = $product['photo_certificate'];
    $photo_diamond = $product['photo_diamond'];
    $video_diamond = $product['video_diamond'];

    // Handle file uploads with validation
    if ($_FILES['photo_certificate']['name']) {
        $allowed_types = ['image/jpeg', 'image/png'];
        if (in_array($_FILES['photo_certificate']['type'], $allowed_types) && $_FILES['photo_certificate']['size'] < 5000000) {
            $photo_certificate = basename($_FILES['photo_certificate']['name']);
            $photo_certificate_path = '../uploads/diamond/certificates/' . $photo_certificate;
            move_uploaded_file($_FILES['photo_certificate']['tmp_name'], $photo_certificate_path);
        } else {
            echo "Invalid certificate photo format or size!";
        }
    }

    if ($_FILES['photo_diamond']['name']) {
        if (in_array($_FILES['photo_diamond']['type'], $allowed_types) && $_FILES['photo_diamond']['size'] < 5000000) {
            $photo_diamond = basename($_FILES['photo_diamond']['name']);
            $photo_diamond_path = '../uploads/diamond/photo/' . $photo_diamond;
            move_uploaded_file($_FILES['photo_diamond']['tmp_name'], $photo_diamond_path);
        } else {
            echo "Invalid diamond photo format or size!";
        }
    }

    if ($_FILES['video_diamond']['name']) {
        $allowed_video_types = ['video/mp4'];
        if (in_array($_FILES['video_diamond']['type'], $allowed_video_types) && $_FILES['video_diamond']['size'] < 10000000) {
            $video_diamond = basename($_FILES['video_diamond']['name']);
            $video_diamond_path = '../uploads/diamond/video/' . $video_diamond;
            move_uploaded_file($_FILES['video_diamond']['tmp_name'], $video_diamond_path);
        } else {
            echo "Invalid video format or size!";
        }
    }

    // Update query
    $stmt = $conn->prepare("UPDATE diamond SET nature = :nature, shape = :shape, weight = :weight, cut_type = :cut_type, clarity = :clarity, color = :color, fluorescence_type = :fluorescence_type, discount_type = :discount_type, certificate = :certificate, photo_certificate = :photo_certificate, photo_diamond = :photo_diamond, video_diamond = :video_diamond WHERE id = :id");
    $stmt->execute([
        'nature' => $nature,
        'shape' => $shape,
        'weight' => $weight,
        'cut_type' => $cut_type,
        'clarity' => $clarity,
        'color' => $color,
        'fluorescence_type' => $fluorescence_type,
        'discount_type' => $discount_type,
        'certificate' => $certificate, // New field
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
    <title>Edit Diamond</title>
    <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
</head>
<body>

<h1>Edit Diamond</h1>

<form action="edit_diamond.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">

    <label for="nature">Nature:</label>
    <input type="text" id="nature" name="nature" value="<?= htmlspecialchars($product['nature']) ?>" required>

    <label for="shape">Shape:</label>
    <input type="text" id="shape" name="shape" value="<?= htmlspecialchars($product['shape']) ?>" required>

    <label for="weight">Weight (carats):</label>
    <input type="text" id="weight" name="weight" value="<?= htmlspecialchars($product['weight']) ?>" required>

    <label for="cut_type">Cut Type:</label>
    <input type="text" id="cut_type" name="cut_type" value="<?= htmlspecialchars($product['cut_type']) ?>" required>

    <label for="clarity">Clarity:</label>
    <input type="text" id="clarity" name="clarity" value="<?= htmlspecialchars($product['clarity']) ?>" required>

    <label for="color">Color:</label>
    <input type="text" id="color" name="color" value="<?= htmlspecialchars($product['color']) ?>" required>

    <label for="fluorescence_type">Fluorescence Type:</label>
    <input type="text" id="fluorescence_type" name="fluorescence_type" value="<?= htmlspecialchars($product['fluorescence_type']) ?>" required>

    <label for="discount_type">Discount Type:</label>
    <input type="text" id="discount_type" name="discount_type" value="<?= htmlspecialchars($product['discount_type']) ?>" required>

    <label for="certificate">Certificate:</label>
    <input type="text" id="certificate" name="certificate" value="<?= htmlspecialchars($product['certificate']) ?>" required>

    <label for="photo_certificate">Certificate Photo:</label>
    <input type="file" id="photo_certificate" name="photo_certificate">
    <?php if ($product['photo_certificate']): ?>
        <p>Current Certificate Photo:</p>
        <img src="../uploads/diamond/certificates/<?= htmlspecialchars($product['photo_certificate']) ?>" alt="Certificate Photo" width="100">
    <?php endif; ?>

    <label for="photo_diamond">Diamond Photo:</label>
    <input type="file" id="photo_diamond" name="photo_diamond">
    <?php if ($product['photo_diamond']): ?>
        <p>Current Diamond Photo:</p>
        <img src="../uploads/diamond/photo/<?= htmlspecialchars($product['photo_diamond']) ?>" alt="Diamond Photo" width="100">
    <?php endif; ?>

    <label for="video_diamond">Diamond Video:</label>
    <input type="file" id="video_diamond" name="video_diamond">
    <?php if ($product['video_diamond']): ?>
        <p>Current Diamond Video:</p>
        <video width="320" height="240" controls>
            <source src="../uploads/diamond/video/<?= htmlspecialchars($product['video_diamond']) ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    <?php endif; ?>

    <button type="submit" name="submit">Update Diamond</button>
</form>

</body>
</html>
