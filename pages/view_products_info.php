<?php


// Include database connection
include_once '../config/db.php';
// Initialize product variable


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}


$product = null;

// Check if POST data (type and id) is received
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['type']) && isset($_POST['id'])) {
    $type = $_POST['type'];
    $id = (int) $_POST['id'];
//    echo "Type: " . htmlspecialchars($type) . "<br>";
//echo "ID: " . htmlspecialchars($id) . "<br>";

// Debugging POST data
//if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//    echo '<pre>';
//    print_r($_POST);
//    echo '</pre>';
//}


    try {
        // SQL queries for different product types
        $query = "";
        switch ($type) {
            case 'diamond':
                $query = "SELECT diamond.*, users.full_name, users.email, users.phone_number 
                          FROM diamond
                          JOIN users ON diamond.user_id = users.id 
                          WHERE diamond.id = :id AND diamond.is_approved = 'Accept'";
                break;
                
            case 'gemstone':
                $query = "SELECT gemstone.*, users.full_name, users.email, users.phone_number 
                          FROM gemstone 
                          JOIN users ON gemstone.user_id = users.id 
                          WHERE gemstone.id = :id AND gemstone.is_approved = 'Accept'";
                break;

            case 'jewelry':
                $query = "SELECT jewelry.*, users.full_name, users.email, users.phone_number 
                          FROM jewelry 
                          JOIN users ON jewelry.user_id = users.id 
                          WHERE jewelry.id = :id AND jewelry.is_approved = 'Accept'";
                break;

            case 'black_diamond':
                $query = "SELECT black_diamonds.*, users.full_name, users.email, users.phone_number 
                          FROM black_diamonds 
                          JOIN users ON black_diamonds.user_id = users.id 
                          WHERE black_diamonds.id = :id AND black_diamonds.is_approved = 'Accept'";
                break;

            case 'gadget':
                $query = "SELECT gadgets.*, users.full_name, users.email, users.phone_number 
                          FROM gadgets 
                          JOIN users ON gadgets.user_id = users.id 
                          WHERE gadgets.id = :id AND gadgets.is_approved = 'Accept'";
                break;

            case 'watch':
                $query = "SELECT watches.*, users.full_name, users.email, users.phone_number 
                          FROM watches 
                          JOIN users ON watches.user_id = users.id 
                          WHERE watches.id = :id AND watches.is_approved = 'Accept'";
                break;

            default:
                $error_message = 'Invalid product type.';
                break;
        }

        if (!isset($error_message)) {
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            // Determine upload directories based on the product type
            $uploadDirMap = [
                'diamond' => ['certificates' => 'diamond/certificates/', 'photo' => 'diamond/photo/', 'video' => 'diamond/video/'],
                'gemstone' => ['certificates' => 'gemstones/certificates/', 'photo' => 'gemstones/photo/', 'video' => 'gemstones/video/'],
                'jewelry' => ['certificates' => 'jewelry/certificates/', 'photo' => 'jewelry/photo/', 'video' => 'jewelry/video/'],
                'black_diamond' => ['certificates' => 'black_diamonds/certificates/', 'photo' => 'black_diamonds/photo/', 'video' => 'black_diamonds/video/'],
                'gadget' => ['photo' => 'gadgets/photo/', 'video' => 'gadgets/video/'],
                'watch' => ['certificates' => 'watches/certificates/', 'photo' => 'watches/photo/', 'video' => 'watches/video/'],
            ];


            if ($product) {
                // Extract file paths and prepare the product details
                $photoPath = '';
                $certificatePath = '';
                $videoPath = '';
                $price = '';

                // Handle paths and price for each type of product
                if ($type == 'diamond') {
                    $photoPath = !empty($product['photo_diamond']) ? $uploadDirMap['diamond']['photo'] . $product['photo_diamond'] : '';
                    $certificatePath = !empty($product['photo_certificate']) ? $uploadDirMap['diamond']['certificates'] . $product['photo_certificate'] : '';
                    $videoPath = !empty($product['video_diamond']) ? $uploadDirMap['diamond']['video'] . $product['video_diamond'] : '';
                    $price = '';
                } elseif ($type == 'gemstone') {
                    $photoPath = !empty($product['photo_gemstone']) ? $uploadDirMap['gemstone']['photo'] . $product['photo_gemstone'] : '';
                    $certificatePath = !empty($product['photo_certificate']) ? $uploadDirMap['gemstone']['certificates'] . $product['photo_certificate'] : '';
                    $videoPath = !empty($product['video_gemstone']) ? $uploadDirMap['gemstone']['video'] . $product['video_gemstone'] : '';
                    $price = $product['price/ct'] ?? '';
                } elseif ($type == 'jewelry') {
                    $photoPath = !empty($product['photo_jewelry']) ? $uploadDirMap['jewelry']['photo'] . $product['photo_jewelry'] : '';
                    $certificatePath = !empty($product['photo_certificate']) ? $uploadDirMap['jewelry']['certificates'] . $product['photo_certificate'] : '';
                    $videoPath = !empty($product['video']) ? $uploadDirMap['jewelry']['video'] . $product['video'] : '';
                    $price = $product['price'] ?? '';
                } elseif ($type == 'black_diamond') {
                    $photoPath = !empty($product['photo_diamond']) ? $uploadDirMap['black_diamond']['photo'] . $product['photo_diamond'] : '';
                    $certificatePath = !empty($product['photo_certificate']) ? $uploadDirMap['black_diamond']['certificates'] . $product['photo_certificate'] : '';
                    $videoPath = !empty($product['video_diamond']) ? $uploadDirMap['black_diamond']['video'] . $product['video_diamond'] : '';
                    $price = $product['price/ct'] ?? '';
                } elseif ($type == 'gadget') {
                    $photoPath = !empty($product['photo_gadget']) ? $uploadDirMap['gadget']['photo'] . $product['photo_gadget'] : '';
                    $certificatePath = ''; // Gadgets do not have certificates
                    $videoPath = !empty($product['video_gadget']) ? $uploadDirMap['gadget']['video'] . $product['video_gadget'] : '';
                    $price = $product['price'] ?? '';
                } elseif ($type == 'watch') {
                    $photoPath = !empty($product['photo_watch']) ? $uploadDirMap['watch']['photo'] . $product['photo_watch'] : '';
                    $certificatePath = !empty($product['photo_certificate']) ? $uploadDirMap['watch']['certificates'] . $product['photo_certificate'] : '';
                    $videoPath = !empty($product['video']) ? $uploadDirMap['watch']['video'] . $product['video'] : '';
                    $price = $product['price'] ?? '';
                }

                $productDetails = [
                    'ID' => $product['id'] ?? '',
                    'title' => $product['title'] ?? $product['nature'] ?? $product['gemstone_name'] ?? '',
                    'photo' => $photoPath,
                    'certificate' => $certificatePath,
                    'video' => $videoPath,
                    'weight' => $product['weight'] ?? '',
                    'cut' => $product['cut'] ?? $product['cut_type'] ?? '',
                    'shape' => $product['shape'] ?? '',
                    'color' => $product['color'] ?? '',
                    'clarity' => $product['clarity'] ?? '',
                    'fluorescence_type' => $product['fluorescence_type'] ?? '',
                    'discount_type' => $product['discount_type'] ?? '',
                    'description' => $product['description'] ?? '',
                    'comment' => $product['comment'] ?? '',
                    'brand' => $product['brand'] ?? '',
                    'price' => $price,
                    'full_name' => $product['full_name'] ?? '',
                    'email' => $product['email'] ?? '',
                    'phone_number' => $product['phone_number'] ?? '',
                    'boost' => $product['boost'] ?? '',
                    'certificate_type' => $product['certificate'] ?? '',
                    'is_approved' => $product['is_approved'] ?? '',
                ];
                
            } else {
                $error_message = 'Product not found.';
            }
        }

    } catch(PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
} else {
    $error_message = 'Product information is missing.';
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
    <link href="https://fonts.googleapis.com/css2?family=Morina:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../css/global.css"> 
    <link rel="stylesheet" href="../css/product_details.css"> 
      <!-- Favicon -->
  <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
      <!-- other meta tags and elements -->
    <meta name="apple-mobile-web-app-capable" content="yes">
  <!-- Android -->
    <meta name="mobile-web-app-capable" content="yes">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container product-details-container">
        <?php if (isset($productDetails) && !empty($productDetails)): ?>
            <h1 class="text-center"><?php echo htmlspecialchars($productDetails['title']); ?></h1>
            <div class="product-images">
                <?php if (!empty($productDetails['photo'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($productDetails['photo']); ?>" alt="<?php echo htmlspecialchars($productDetails['title']); ?>" class="product-image">
                <?php endif; ?>
                <?php if (!empty($productDetails['certificate'])): ?>
                    <img src="../uploads/<?php echo htmlspecialchars($productDetails['certificate']); ?>" alt="Certificate for <?php echo htmlspecialchars($productDetails['title']); ?>" class="product-image">
                <?php endif; ?>
            </div>
            <div class="text-center product-info">
                <?php if (!empty($productDetails['ID'])): ?>
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($productDetails['ID']); ?></p>
                <?php endif; ?>
                <?php if (!empty($productDetails['certificate_type'])): ?>
                    <p><strong>Certificate:</strong> <?php echo htmlspecialchars($productDetails['certificate_type']); ?></p>
                <?php endif; ?>
                <?php if (!empty($productDetails['weight'])): ?>
                    <p><strong>Weight:</strong> <?php echo htmlspecialchars($productDetails['weight']); ?> carats</p>
                <?php endif; ?>
                <?php if (!empty($productDetails['cut'])): ?>
                    <p><strong>Cut:</strong> <?php echo htmlspecialchars($productDetails['cut']); ?></p>
                <?php endif; ?>
                <?php if (!empty($productDetails['shape'])): ?>
                    <p><strong>Shape:</strong> <?php echo htmlspecialchars($productDetails['shape']); ?></p>
                <?php endif; ?>
                <?php if (!empty($productDetails['color'])): ?>
                    <p><strong>Color:</strong> <?php echo htmlspecialchars($productDetails['color']); ?></p>
                <?php endif; ?>
                <?php if (!empty($productDetails['clarity'])): ?>
                    <p><strong>Clarity:</strong> <?php echo htmlspecialchars($productDetails['clarity']); ?></p>
                <?php endif; ?>
                <?php if (!empty($productDetails['fluorescence_type'])): ?>
                    <p><strong>Fluorescence Type:</strong> <?php echo htmlspecialchars($productDetails['fluorescence_type']); ?></p>
                <?php endif; ?>
                <?php if (!empty($productDetails['discount_type'])): ?>
                    <p><strong>Discount Type:</strong> <?php echo htmlspecialchars($productDetails['discount_type']); ?></p>
                <?php endif; ?>
                <?php if (!empty($productDetails['description'])): ?>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($productDetails['description']); ?></p>
                <?php endif; ?>
                <?php if (!empty($productDetails['comment'])): ?>
                    <p><strong>Comment:</strong> <?php echo htmlspecialchars($productDetails['comment']); ?></p>
                <?php endif; ?>
                <?php if (!empty($productDetails['brand'])): ?>
                    <p><strong>Brand:</strong> <?php echo htmlspecialchars($productDetails['brand']); ?></p>
                <?php endif; ?>
                <?php if (!empty($productDetails['price'])): ?>
                    <p><strong>Price:</strong> $<?php echo htmlspecialchars($productDetails['price']); ?></p>
                <?php endif; ?>
                <?php if (!empty($productDetails['boost'])): ?>
                    <p><strong>Boost:</strong> <?php echo htmlspecialchars($productDetails['boost']); ?></p>
                <?php endif; ?>
                <?php if (!empty($productDetails['is_approved'])): ?>
                    <p><strong>Approval Status:</strong> <?php echo htmlspecialchars($productDetails['is_approved']); ?></p>
                <?php endif; ?>
                <?php if (!empty($productDetails['full_name'])): ?>
                    <p><strong>Posted by:</strong> <?php echo htmlspecialchars($productDetails['full_name']); ?></p>
                <?php endif; ?>
                <?php if (!empty($productDetails['email'])): ?>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($productDetails['email']); ?></p>
                <?php endif; ?>
                <?php if (!empty($productDetails['phone_number'])): ?>
                    <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($productDetails['phone_number']); ?></p>
                <?php endif; ?>
                <!-- Button to view all products by this user -->
                <form action="view_user_products.php" method="POST">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($product['user_id']); ?>">
                    <button type="submit">View User Products</button>
                </form>


                <br><br>
                <?php if (!empty($productDetails['video'])): ?>
                    <h2>Video</h2>
                    <video controls width="400" height="250">
                        <source src="../uploads/<?php echo htmlspecialchars($productDetails['video']); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php endif; ?>
            </div>
        <?php elseif (isset($error_message)): ?>
            <p class="text-center text-danger"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
    </div>

    <?php include '../includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
