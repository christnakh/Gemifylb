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
    $cut = $_POST['cut']; // New field for cut
    $type = $_POST['type']; // New field for type
    $certificate = $_POST['certificate']; // New field for certificate

    // Handle image upload
    $photo_certificate = $product['photo_certificate'];
    if ($_FILES['photo_certificate']['name']) {
        $photo_certificate = basename($_FILES['photo_certificate']['name']);
        $photo_certificate_path = '../uploads/gemstones/certificates/' . basename($_FILES['photo_certificate']['name']);
        move_uploaded_file($_FILES['photo_certificate']['tmp_name'], $photo_certificate_path);
    }

    $photo_gemstone = $product['photo_gemstone'];
    if ($_FILES['photo_gemstone']['name']) {
        $photo_gemstone = basename($_FILES['photo_gemstone']['name']);
        $photo_gemstone_path = '../uploads/gemstones/photo/' . basename($_FILES['photo_gemstone']['name']);
        move_uploaded_file($_FILES['photo_gemstone']['tmp_name'], $photo_gemstone_path);
    }

    // Handle video upload
    $video_gemstone = $product['video_gemstone'];
    if ($_FILES['video_gemstone']['name']) {
        $video_gemstone = basename($_FILES['video_gemstone']['name']);
        $video_gemstone_path = '../uploads/gemstones/video/' . basename($_FILES['video_gemstone']['name']);
        move_uploaded_file($_FILES['video_gemstone']['tmp_name'], $video_gemstone_path);
    }

    // Update query
    $stmt = $conn->prepare("UPDATE gemstone SET gemstone_name = :gemstone_name, shape = :shape, weight = :weight, color = :color, `price/ct` = :price, cut = :cut, type = :type, certificate = :certificate, photo_certificate = :photo_certificate, photo_gemstone = :photo_gemstone, video_gemstone = :video_gemstone, is_approved = 'Pending' WHERE id = :id");
    $stmt->execute([
        'gemstone_name' => $gemstone_name,
        'shape' => $shape,
        'weight' => $weight,
        'color' => $color,
        'price' => $price,
        'cut' => $cut,
        'type' => $type,
        'certificate' => $certificate,
        'photo_certificate' => $photo_certificate,
        'photo_gemstone' => $photo_gemstone,
        'video_gemstone' => $video_gemstone,
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
    <title>Edit Gemstone</title>
    <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
    <meta name="apple-mobile-web-app-capable" content="yes">
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
                        <h2 class="h3 font-weight-normal">Edit Gemstone</h2>
                    </div>
                    <form action="edit_gemstone.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">

                        <div class="form-group mb-3">
                            <label for="gemstone_name">Gemstone Name:</label>
                            <input type="text" id="gemstone_name" name="gemstone_name" value="<?= htmlspecialchars($product['gemstone_name']) ?>" required class="form-control rounded-pill border-0 px-4">
                        </div>

                        <div class="form-group mb-3">
                            <label for="shape">Shape:</label>
                            <input type="text" id="shape" name="shape" value="<?= htmlspecialchars($product['shape']) ?>" required class="form-control rounded-pill border-0 px-4">
                        </div>

                        <div class="form-group mb-3">
                            <label for="weight">Weight:</label>
                            <input type="text" id="weight" name="weight" value="<?= htmlspecialchars($product['weight']) ?>" required class="form-control rounded-pill border-0 px-4">
                        </div>

                        <div class="form-group mb-3">
                            <label for="color">Color:</label>
                            <input type="text" id="color" name="color" value="<?= htmlspecialchars($product['color']) ?>" required class="form-control rounded-pill border-0 px-4">
                        </div>

                        <div class="form-group mb-3">
                            <label for="price">Price per Carat:</label>
                            <input type="text" id="price" name="price" value="<?= htmlspecialchars($product['price/ct']) ?>" required class="form-control rounded-pill border-0 px-4">
                        </div>

                        <div class="form-group mb-3">
                            <label for="cut">Cut:</label>
                            <input type="text" id="cut" name="cut" value="<?= htmlspecialchars($product['cut']) ?>" required class="form-control rounded-pill border-0 px-4">
                        </div>

                        <div class="form-group mb-3">
                            <label for="type">Type:</label>
                            <input type="text" id="type" name="type" value="<?= htmlspecialchars($product['type']) ?>" required class="form-control rounded-pill border-0 px-4">
                        </div>

                        <div class="form-group mb-3">
                            <label for="certificate">Certificate:</label>
                            <input type="text" id="certificate" name="certificate" value="<?= htmlspecialchars($product['certificate']) ?>" required class="form-control rounded-pill border-0 px-4">
                        </div>

                        <div class="form-group mb-3">
                            <label for="photo_certificate">Certificate Photo:</label>
                            <div class="custom-file">
                                <input type="file" id="photo_certificate" name="photo_certificate" class="custom-file-input">
                                <label class="custom-file-label form-control rounded-pill border-0 px-4" for="photo_certificate">Choose file</label>
                            </div>
                            <p class="mt-2">Current Certificate Photo:</p>
                            <?php if ($product['photo_certificate']): ?>
                                <img src="../uploads/gemstones/certificates/<?= htmlspecialchars($product['photo_certificate']) ?>" alt="Certificate Photo" width="100">
                            <?php endif; ?>
                        </div>

                        <div class="form-group mb-3">
                            <label for="photo_gemstone">Gemstone Photo:</label>
                            <div class="custom-file">
                                <input type="file" id="photo_gemstone" name="photo_gemstone" class="custom-file-input">
                                <label class="custom-file-label form-control rounded-pill border-0 px-4" for="photo_gemstone">Choose file</label>
                            </div>
                            <p class="mt-2">Current Gemstone Photo:</p>
                            <?php if ($product['photo_gemstone']): ?>
                                <img src="../uploads/gemstones/photo/<?= htmlspecialchars($product['photo_gemstone']) ?>" alt="Gemstone Photo" width="100">
                            <?php endif; ?>
                        </div>

                        <div class="form-group mb-3">
                            <label for="video_gemstone">Gemstone Video:</label>
                            <div class="custom-file">
                                <input type="file" id="video_gemstone" name="video_gemstone" class="custom-file-input">
                                <label class="custom-file-label form-control rounded-pill border-0 px-4" for="video_gemstone">Choose file</label>
                            </div>
                            <p class="mt-2">Current Gemstone Video:</p>
                            <?php if ($product['video_gemstone']): ?>
                                <video width="320" height="240" controls>
                                    <source src="../uploads/gemstones/video/<?= htmlspecialchars($product['video_gemstone']) ?>" type="video/mp4">
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
