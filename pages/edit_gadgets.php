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
    $stmt = $conn->prepare("UPDATE gadgets SET title = :title, description = :description, price = :price, photo_gadget = :photo_gadget, video_gadget = :video_gadget, is_approved = 'Pending' WHERE id = :id");
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
                        <h2 class="h3 font-weight-normal">Edit Gadget</h2>
                    </div>
                    <form action="edit_gadgets.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">

                        <div class="form-group mb-3">
                            <label for="title">Title:</label>
                            <input type="text" id="title" name="title" value="<?= htmlspecialchars($product['title']) ?>" required class="form-control rounded-pill border-0 px-4">
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
                            <label for="photo_gadget">Gadget Photo:</label>
                            <div class="custom-file">
                                <input type="file" id="photo_gadget" name="photo_gadget" class="custom-file-input">
                                <label class="custom-file-label form-control rounded-pill border-0 px-4" for="photo_gadget">Choose file</label>
                            </div>
                            <p class="mt-2">Current Gadget Photo:</p>
                            <?php if ($product['photo_gadget']): ?>
                                <img src="../uploads/gadgets/photo/<?= htmlspecialchars($product['photo_gadget']) ?>" alt="Gadget Photo" width="100">
                            <?php endif; ?>
                        </div>

                        <div class="form-group mb-3">
                            <label for="video_gadget">Gadget Video:</label>
                            <div class="custom-file">
                                <input type="file" id="video_gadget" name="video_gadget" class="custom-file-input">
                                <label class="custom-file-label form-control rounded-pill border-0 px-4" for="video_gadget">Choose file</label>
                            </div>
                            <p class="mt-2">Current Gadget Video:</p>
                            <?php if ($product['video_gadget']): ?>
                                <video width="320" height="240" controls>
                                    <source src="../uploads/gadgets/video/<?= htmlspecialchars($product['video_gadget']) ?>" type="video/mp4">
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
