<?php
include '../config/db.php';


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Fetch the watch details
    $stmt = $conn->prepare("SELECT * FROM watches WHERE id = :id");
    $stmt->execute(['id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo "Product not found!";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $title = $_POST['title'];
    $brand = $_POST['brand'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Handle photo upload
    $photo_watch = $product['photo_watch'];
    if ($_FILES['photo_watch']['name']) {
        $photo_watch = basename($_FILES['photo_watch']['name']);
        $photo_watch_path = '../uploads/watches/photo/' . basename($_FILES['photo_watch']['name']);
        move_uploaded_file($_FILES['photo_watch']['tmp_name'], $photo_watch_path);
    }

    $photo_certificate = $product['photo_certificate'];
    if ($_FILES['photo_certificate']['name']) {
        $photo_certificate = basename($_FILES['photo_certificate']['name']);
        $photo_certificate_path = '../uploads/watches/certificates/' . basename($_FILES['photo_certificate']['name']);
        move_uploaded_file($_FILES['photo_certificate']['tmp_name'], $photo_certificate_path);
    }

    // Handle video upload
    $video = $product['video'];
    if ($_FILES['video']['name']) {
        $video = basename($_FILES['video']['name']);
        $video_path = '../uploads/watches/video/' . basename($_FILES['video']['name']);
        move_uploaded_file($_FILES['video']['tmp_name'], $video_path);
    }

    // Update query
    $stmt = $conn->prepare("UPDATE watches SET title = :title, brand = :brand, description = :description, price = :price, photo_watch = :photo_watch, photo_certificate = :photo_certificate, video = :video, is_approved = 'Pending' WHERE id = :id");
    $stmt->execute([
        'title' => $title,
        'brand' => $brand,
        'description' => $description,
        'price' => $price,
        'photo_watch' => $photo_watch,
        'photo_certificate' => $photo_certificate,
        'video' => $video,
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
    <title>Edit Watch</title>

        <!-- Favicon -->
  <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
        <!-- other meta tags and elements -->
    <meta name="apple-mobile-web-app-capable" content="yes">
  <!-- Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<?php include '../includes/header.php';?>
<div class="container mt-4 p-3">
    <div class="row justify-content-center align-items-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <h2 class="h3 font-weight-normal">Edit Watch</h2>
                    </div>
                    <form action="edit_watches.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">

                        <div class="form-group mb-3">
                            <label for="title">Title:</label>
                            <input type="text" id="title" name="title" value="<?= htmlspecialchars($product['title']) ?>" required class="form-control rounded-pill border-0 px-4">
                        </div>

                        <div class="form-group mb-3">
                            <label for="brand">Brand:</label>
                            <input type="text" id="brand" name="brand" value="<?= htmlspecialchars($product['brand']) ?>" required class="form-control rounded-pill border-0 px-4">
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">Description:</label>
                            <textarea id="description" name="description" required class="form-control rounded-pill border-0 px-4"><?= htmlspecialchars($product['description']) ?></textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label for="price">Price:</label>
                            <input type="text" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" required class="form-control rounded-pill border-0 px-4">
                        </div>

                        <div class="form-group mb-3">
                            <label for="photo_watch">Watch Photo:</label>
                            <div class="custom-file">
                                <input type="file" id="photo_watch" name="photo_watch" class="custom-file-input">
                                <label class="custom-file-label form-control rounded-pill border-0 px-4" for="photo_watch">Choose file</label>
                            </div>
                            <p class="mt-2">Current Watch Photo:</p>
                            <?php if ($product['photo_watch']): ?>
                                <img src="../uploads/watches/photo/<?= htmlspecialchars($product['photo_watch']) ?>" alt="Watch Photo" width="100">
                            <?php endif; ?>
                        </div>

                        <div class="form-group mb-3">
                            <label for="photo_certificate">Certificate Photo:</label>
                            <div class="custom-file">
                                <input type="file" id="photo_certificate" name="photo_certificate" class="custom-file-input">
                                <label class="custom-file-label form-control rounded-pill border-0 px-4" for="photo_certificate">Choose file</label>
                            </div>
                            <p class="mt-2">Current Certificate Photo:</p>
                            <?php if ($product['photo_certificate']): ?>
                                <img src="../uploads/watches/certificates/<?= htmlspecialchars($product['photo_certificate']) ?>" alt="Certificate Photo" width="100">
                            <?php endif; ?>
                        </div>

                        <div class="form-group mb-3">
                            <label for="video">Watch Video:</label>
                            <div class="custom-file">
                                <input type="file" id="video" name="video" class="custom-file-input">
                                <label class="custom-file-label form-control rounded-pill border-0 px-4" for="video">Choose file</label>
                            </div>
                            <p class="mt-2">Current Watch Video:</p>
                            <?php if ($product['video']): ?>
                                <video width="320" height="240" controls>
                                    <source src="../uploads/watches/video/<?= htmlspecialchars($product['video']) ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            <?php endif; ?>
                        </div>

                        <div class="text-center">
                            <button type="submit" name="submit" class="btn btn-submit rounded-pill px-4">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
