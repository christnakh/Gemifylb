<?php
// Include database connection
include '../includes/db.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in.";
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$user_id = $_SESSION['user_id'];

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : null;
$product_type = isset($_POST['product_type']) ? $_POST['product_type'] : null;
    echo $product_id . $product_type;
    if (!$product_id || !$product_type) {
        echo "Invalid product ID or type.";
        exit;
    }

// Fetch product data if ID is provided
$product = [];
if ($product_id && $product_type) {
    $table = '';
    switch ($product_type) {
        case 'black_diamonds':
            $table = 'black_diamonds';
            break;
        case 'diamond':
            $table = 'diamond';
            break;
        case 'gadgets':
            $table = 'gadgets';
            break;
        case 'gemstone':
            $table = 'gemstone';
            break;
        case 'jewelry':
            $table = 'jewelry';
            break;
        case 'watches':
            $table = 'watches';
            break;
        default:
            echo "Invalid product type.";
            exit;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = :id");
    $stmt->execute(['id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && isset($_POST['product_type'])) {
    if ($product_id && $product_type) {
        // Prepare the update SQL query based on product type
        $sql = "";
        switch ($product_type) {
            case 'black_diamonds':
                $sql = "UPDATE black_diamonds SET 
                    name = :name, 
                    shape = :shape, 
                    weight = :weight, 
                    `price/ct` = :price_per_ct, 
                    photo_diamond = :photo_diamond, 
                    video_diamond = :video_diamond, 
                    photo_certificate = :photo_certificate
                    WHERE id = :id";
                break;
            case 'diamond':
                $sql = "UPDATE diamond SET 
                    nature = :nature, 
                    shape = :shape, 
                    weight = :weight, 
                    clarity = :clarity, 
                    color = :color, 
                    cut_type = :cut_type, 
                    `price/ct` = :price_per_ct, 
                    photo_diamond = :photo_diamond, 
                    video_diamond = :video_diamond, 
                    photo_certificate = :photo_certificate
                    WHERE id = :id";
                break;
            case 'gadgets':
                $sql = "UPDATE gadgets SET 
                    title = :title, 
                    description = :description, 
                    price = :price, 
                    photo_gadget = :photo_gadget, 
                    video_gadget = :video_gadget
                    WHERE id = :id";
                break;
            case 'gemstone':
                $sql = "UPDATE gemstone SET 
                    gemstone_name = :gemstone_name, 
                    weight = :weight, 
                    cut = :cut, 
                    shape = :shape, 
                    color = :color, 
                    type = :type, 
                    `price/ct` = :price_per_ct, 
                    photo_gemstone = :photo_gemstone, 
                    video_gemstone = :video_gemstone, 
                    photo_certificate = :photo_certificate
                    WHERE id = :id";
                break;
            case 'jewelry':
                $sql = "UPDATE jewelry SET 
                    title = :title, 
                    description = :description, 
                    price = :price, 
                    photo_jewelry = :photo_jewelry, 
                    video = :video, 
                    photo_certificate = :photo_certificate
                    WHERE id = :id";
                break;
            case 'watches':
                $sql = "UPDATE watches SET 
                    title = :title, 
                    brand = :brand, 
                    description = :description, 
                    price = :price, 
                    photo_watch = :photo_watch, 
                    video = :video, 
                    photo_certificate = :photo_certificate
                    WHERE id = :id";
                break;
            default:
                echo "Invalid product type.";
                exit;
        }

        // Prepare statement
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);

        // Bind the other parameters based on product type
        switch ($product_type) {
            case 'black_diamonds':
                $stmt->bindParam(':name', $_POST['name']);
                $stmt->bindParam(':shape', $_POST['shape']);
                $stmt->bindParam(':weight', $_POST['weight']);
                $stmt->bindParam(':price_per_ct', $_POST['price_per_ct']);
                $stmt->bindParam(':photo_diamond', $_FILES['photo_diamond']['name']);
                $stmt->bindParam(':video_diamond', $_FILES['video_diamond']['name']);
                $stmt->bindParam(':photo_certificate', $_FILES['photo_certificate']['name']);
                break;
            case 'diamond':
                $stmt->bindParam(':nature', $_POST['nature']);
                $stmt->bindParam(':shape', $_POST['shape']);
                $stmt->bindParam(':weight', $_POST['weight']);
                $stmt->bindParam(':clarity', $_POST['clarity']);
                $stmt->bindParam(':color', $_POST['color']);
                $stmt->bindParam(':cut_type', $_POST['cut_type']);
                $stmt->bindParam(':price_per_ct', $_POST['price_per_ct']);
                $stmt->bindParam(':photo_diamond', $_FILES['photo_diamond']['name']);
                $stmt->bindParam(':video_diamond', $_FILES['video_diamond']['name']);
                $stmt->bindParam(':photo_certificate', $_FILES['photo_certificate']['name']);
                break;
            case 'gadgets':
                $stmt->bindParam(':title', $_POST['title']);
                $stmt->bindParam(':description', $_POST['description']);
                $stmt->bindParam(':price', $_POST['price']);
                $stmt->bindParam(':photo_gadget', $_FILES['photo_gadget']['name']);
                $stmt->bindParam(':video_gadget', $_FILES['video_gadget']['name']);
                break;
            case 'gemstone':
                $stmt->bindParam(':gemstone_name', $_POST['gemstone_name']);
                $stmt->bindParam(':weight', $_POST['weight']);
                $stmt->bindParam(':cut', $_POST['cut']);
                $stmt->bindParam(':shape', $_POST['shape']);
                $stmt->bindParam(':color', $_POST['color']);
                $stmt->bindParam(':type', $_POST['type']);
                $stmt->bindParam(':price_per_ct', $_POST['price_per_ct']);
                $stmt->bindParam(':photo_gemstone', $_FILES['photo_gemstone']['name']);
                $stmt->bindParam(':video_gemstone', $_FILES['video_gemstone']['name']);
                $stmt->bindParam(':photo_certificate', $_FILES['photo_certificate']['name']);
                break;
            case 'jewelry':
                $stmt->bindParam(':title', $_POST['title']);
                $stmt->bindParam(':description', $_POST['description']);
                $stmt->bindParam(':price', $_POST['price']);
                $stmt->bindParam(':photo_jewelry', $_FILES['photo_jewelry']['name']);
                $stmt->bindParam(':video', $_FILES['video']['name']);
                $stmt->bindParam(':photo_certificate', $_FILES['photo_certificate']['name']);
                break;
            case 'watches':
                $stmt->bindParam(':title', $_POST['title']);
                $stmt->bindParam(':brand', $_POST['brand']);
                $stmt->bindParam(':description', $_POST['description']);
                $stmt->bindParam(':price', $_POST['price']);
                $stmt->bindParam(':photo_watch', $_FILES['photo_watch']['name']);
                $stmt->bindParam(':video', $_FILES['video']['name']);
                $stmt->bindParam(':photo_certificate', $_FILES['photo_certificate']['name']);
                break;
        }

        // Execute the statement
        try {
            $stmt->execute();

            // Handle file uploads
            foreach ($_FILES as $file_key => $file) {
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = "../uploads/{$product_type}/" . strtolower(substr($file_key, 0, strpos($file_key, '_'))) . '/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    move_uploaded_file($file['tmp_name'], $upload_dir . basename($file['name']));
                }
            }

            echo "Product updated successfully!";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Invalid product ID or type.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
      <!-- other meta tags and elements -->
    <meta name="apple-mobile-web-app-capable" content="yes">
  <!-- Android -->
    <meta name="mobile-web-app-capable" content="yes">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="container mt-4">
    <h2>Edit Product</h2>
    <form action="edit_post.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
        <input type="hidden" name="product_type" value="<?php echo htmlspecialchars($product_type); ?>">

        <?php if ($product_type == 'black_diamonds'): ?>
            <!-- Black Diamonds Fields -->
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="shape" class="form-label">Shape</label>
                <input type="text" class="form-control" id="shape" name="shape" value="<?php echo htmlspecialchars($product['shape'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="weight" class="form-label">Weight</label>
                <input type="text" class="form-control" id="weight" name="weight" value="<?php echo htmlspecialchars($product['weight'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="price_per_ct" class="form-label">Price per Carat</label>
                <input type="text" class="form-control" id="price_per_ct" name="price_per_ct" value="<?php echo htmlspecialchars($product['price_per_ct'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="photo_diamond" class="form-label">Photo</label>
                <input type="file" class="form-control" id="photo_diamond" name="photo_diamond">
                <?php if (!empty($product['photo_diamond'])): ?>
                    <img src="../uploads/black_diamonds/photo/<?php echo htmlspecialchars($product['photo_diamond']); ?>" alt="Current Photo" class="img-fluid mt-2">
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="video_diamond" class="form-label">Video</label>
                <input type="file" class="form-control" id="video_diamond" name="video_diamond">
                <?php if (!empty($product['video_diamond'])): ?>
                    <video controls class="w-100 mt-2">
                        <source src="../uploads/black_diamonds/video/<?php echo htmlspecialchars($product['video_diamond']); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="photo_certificate" class="form-label">Certificate</label>
                <input type="file" class="form-control" id="photo_certificate" name="photo_certificate">
                <?php if (!empty($product['photo_certificate'])): ?>
                    <img src="../uploads/black_diamonds/certificates/<?php echo htmlspecialchars($product['photo_certificate']); ?>" alt="Current Certificate" class="img-fluid mt-2">
                <?php endif; ?>
            </div>

        <?php elseif ($product_type == 'diamond'): ?>
            <!-- Diamond Fields -->
            <div class="mb-3">
                <label for="nature" class="form-label">Nature</label>
                <input type="text" class="form-control" id="nature" name="nature" value="<?php echo htmlspecialchars($product['nature'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="shape" class="form-label">Shape</label>
                <input type="text" class="form-control" id="shape" name="shape" value="<?php echo htmlspecialchars($product['shape'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="weight" class="form-label">Weight</label>
                <input type="text" class="form-control" id="weight" name="weight" value="<?php echo htmlspecialchars($product['weight'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="clarity" class="form-label">Clarity</label>
                <input type="text" class="form-control" id="clarity" name="clarity" value="<?php echo htmlspecialchars($product['clarity'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="color" class="form-label">Color</label>
                <input type="text" class="form-control" id="color" name="color" value="<?php echo htmlspecialchars($product['color'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="cut_type" class="form-label">Cut Type</label>
                <input type="text" class="form-control" id="cut_type" name="cut_type" value="<?php echo htmlspecialchars($product['cut_type'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="price_per_ct" class="form-label">Price per Carat</label>
                <input type="text" class="form-control" id="price_per_ct" name="price_per_ct" value="<?php echo htmlspecialchars($product['price_per_ct'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="photo_diamond" class="form-label">Photo</label>
                <input type="file" class="form-control" id="photo_diamond" name="photo_diamond">
                <?php if (!empty($product['photo_diamond'])): ?>
                    <img src="../uploads/diamond/photo/<?php echo htmlspecialchars($product['photo_diamond']); ?>" alt="Current Photo" class="img-fluid mt-2">
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="video_diamond" class="form-label">Video</label>
                <input type="file" class="form-control" id="video_diamond" name="video_diamond">
                <?php if (!empty($product['video_diamond'])): ?>
                    <video controls class="w-100 mt-2">
                        <source src="../uploads/diamond/video/<?php echo htmlspecialchars($product['video_diamond']); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="photo_certificate" class="form-label">Certificate</label>
                <input type="file" class="form-control" id="photo_certificate" name="photo_certificate">
                <?php if (!empty($product['photo_certificate'])): ?>
                    <img src="../uploads/diamond/certificates/<?php echo htmlspecialchars($product['photo_certificate']); ?>" alt="Current Certificate" class="img-fluid mt-2">
                <?php endif; ?>
            </div>

        <?php elseif ($product_type == 'gadgets'): ?>
            <!-- Gadgets Fields -->
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($product['title'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="text" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="photo_gadget" class="form-label">Photo</label>
                <input type="file" class="form-control" id="photo_gadget" name="photo_gadget">
                <?php if (!empty($product['photo_gadget'])): ?>
                    <img src="../uploads/gadgets/photo/<?php echo htmlspecialchars($product['photo_gadget']); ?>" alt="Current Photo" class="img-fluid mt-2">
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="video_gadget" class="form-label">Video</label>
                <input type="file" class="form-control" id="video_gadget" name="video_gadget">
                <?php if (!empty($product['video_gadget'])): ?>
                    <video controls class="w-100 mt-2">
                        <source src="../uploads/gadgets/video/<?php echo htmlspecialchars($product['video_gadget']); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php endif; ?>
            </div>

        <?php elseif ($product_type == 'gemstone'): ?>
            <!-- Gemstone Fields -->
            <div class="mb-3">
                <label for="gemstone_name" class="form-label">Gemstone Name</label>
                <input type="text" class="form-control" id="gemstone_name" name="gemstone_name" value="<?php echo htmlspecialchars($product['gemstone_name'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="weight" class="form-label">Weight</label>
                <input type="text" class="form-control" id="weight" name="weight" value="<?php echo htmlspecialchars($product['weight'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="cut" class="form-label">Cut</label>
                <input type="text" class="form-control" id="cut" name="cut" value="<?php echo htmlspecialchars($product['cut'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="shape" class="form-label">Shape</label>
                <input type="text" class="form-control" id="shape" name="shape" value="<?php echo htmlspecialchars($product['shape'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="color" class="form-label">Color</label>
                <input type="text" class="form-control" id="color" name="color" value="<?php echo htmlspecialchars($product['color'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <input type="text" class="form-control" id="type" name="type" value="<?php echo htmlspecialchars($product['type'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="price_per_ct" class="form-label">Price per Carat</label>
                <input type="text" class="form-control" id="price_per_ct" name="price_per_ct" value="<?php echo htmlspecialchars($product['price_per_ct'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="photo_gemstone" class="form-label">Photo</label>
                <input type="file" class="form-control" id="photo_gemstone" name="photo_gemstone">
                <?php if (!empty($product['photo_gemstone'])): ?>
                    <img src="../uploads/gemstones/photo/<?php echo htmlspecialchars($product['photo_gemstone']); ?>" alt="Current Photo" class="img-fluid mt-2">
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="video_gemstone" class="form-label">Video</label>
                <input type="file" class="form-control" id="video_gemstone" name="video_gemstone">
                <?php if (!empty($product['video_gemstone'])): ?>
                    <video controls class="w-100 mt-2">
                        <source src="../uploads/gemstones/video/<?php echo htmlspecialchars($product['video_gemstone']); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="photo_certificate" class="form-label">Certificate</label>
                <input type="file" class="form-control" id="photo_certificate" name="photo_certificate">
                <?php if (!empty($product['photo_certificate'])): ?>
                    <img src="../uploads/gemstones/certificates/<?php echo htmlspecialchars($product['photo_certificate']); ?>" alt="Current Certificate" class="img-fluid mt-2">
                <?php endif; ?>
            </div>

        <?php elseif ($product_type == 'jewelry'): ?>
            <!-- Jewelry Fields -->
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($product['title'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="text" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="photo_jewelry" class="form-label">Photo</label>
                <input type="file" class="form-control" id="photo_jewelry" name="photo_jewelry">
                <?php if (!empty($product['photo_jewelry'])): ?>
                    <img src="../uploads/jewelry/photo/<?php echo htmlspecialchars($product['photo_jewelry']); ?>" alt="Current Photo" class="img-fluid mt-2">
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="video" class="form-label">Video</label>
                <input type="file" class="form-control" id="video" name="video">
                <?php if (!empty($product['video'])): ?>
                    <video controls class="w-100 mt-2">
                        <source src="../uploads/jewelry/video/<?php echo htmlspecialchars($product['video']); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="photo_certificate" class="form-label">Certificate</label>
                <input type="file" class="form-control" id="photo_certificate" name="photo_certificate">
                <?php if (!empty($product['photo_certificate'])): ?>
                    <img src="../uploads/jewelry/certificates/<?php echo htmlspecialchars($product['photo_certificate']); ?>" alt="Current Certificate" class="img-fluid mt-2">
                <?php endif; ?>
            </div>

        <?php elseif ($product_type == 'watches'): ?>
            <!-- Watches Fields -->
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($product['title'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="brand" class="form-label">Brand</label>
                <input type="text" class="form-control" id="brand" name="brand" value="<?php echo htmlspecialchars($product['brand'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="text" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="photo_watch" class="form-label">Photo</label>
                <input type="file" class="form-control" id="photo_watch" name="photo_watch">
                <?php if (!empty($product['photo_watch'])): ?>
                    <img src="../uploads/watches/photo/<?php echo htmlspecialchars($product['photo_watch']); ?>" alt="Current Photo" class="img-fluid mt-2">
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="video" class="form-label">Video</label>
                <input type="file" class="form-control" id="video" name="video">
                <?php if (!empty($product['video'])): ?>
                    <video controls class="w-100 mt-2">
                        <source src="../uploads/watches/video/<?php echo htmlspecialchars($product['video']); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label for="photo_certificate" class="form-label">Certificate</label>
                <input type="file" class="form-control" id="photo_certificate" name="photo_certificate">
                <?php if (!empty($product['photo_certificate'])): ?>
                    <img src="../uploads/watches/certificates/<?php echo htmlspecialchars($product['photo_certificate']); ?>" alt="Current Certificate" class="img-fluid mt-2">
                <?php endif; ?>
            </div>

        <?php endif; ?>
        
        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
        <input type="hidden" name="product_type" value="<?php echo htmlspecialchars($product_type); ?>">
        <button type="submit" class="btn btn-primary">Update Product</button>
    </form>

    <!-- Include your scripts -->
</body>
</html>
