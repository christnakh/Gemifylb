<?php
include '../config/db.php';
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
    $id = $_POST['id'];

    try {
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

            // Display product details
            if ($product) {
                $productDetails = [
                    'title' => $product['title'] ?? $product['nature'] ?? $product['gemstone_name'] ?? '',
                    'photo' => $product['photo'] ?? $product['photo_diamond'] ?? $product['photo_gemstone'] ?? $product['photo_watch'] ?? '',
                    'certificate' => $product['photo_certificate'] ?? $product['certificate'] ?? '',
                    'weight' => $product['weight'] ?? '',
                    'cut' => $product['cut'] ?? $product['cut_type'] ?? '',
                    'shape' => $product['shape'] ?? '',
                    'color' => $product['color'] ?? '',
                    'clarity' => $product['clarity'] ?? '',
                    'fluorescence_type' => $product['fluorescence_type'] ?? '',
                    'discount_type' => $product['discount_type'] ?? '',
                    'description' => $product['description'] ?? '',
                    'comment' => $product['comment'] ?? '',
                    'brand' => $product['brand'] ?? '', // Watches-specific field
                    'full_name' => $product['full_name'] ?? '',
                    'email' => $product['email'] ?? '',
                    'phone_number' => $product['phone_number'] ?? ''
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
    <style>
        .carousel-item img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container product-details-container">
        <?php if (isset($productDetails) && !empty($productDetails)): ?>
            <h1 class="text-center"><?php echo htmlspecialchars($productDetails['title']); ?></h1>
            <div id="productCarousel" class="carousel slide">
                <div class="carousel-inner">
                    <?php if (!empty($productDetails['photo'])): ?>
                        <div class="carousel-item active">
                            <img src="../uploads/<?php echo htmlspecialchars($productDetails['photo']); ?>" alt="<?php echo htmlspecialchars($productDetails['title']); ?>">
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($productDetails['certificate'])): ?>
                        <div class="carousel-item">
                            <img src="../uploads/certificates/<?php echo htmlspecialchars($productDetails['certificate']); ?>" alt="Certificate for <?php echo htmlspecialchars($productDetails['title']); ?>">
                        </div>
                    <?php endif; ?>
                </div>
                <a class="carousel-control-prev" href="#productCarousel" role="button" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </a>
                <a class="carousel-control-next" href="#productCarousel" role="button" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </a>
            </div>
            <div class="text-center product-info">
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
                    <p><strong>Fluorescence:</strong> <?php echo htmlspecialchars($productDetails['fluorescence_type']); ?></p>
                <?php endif; ?>
                <?php if (!empty($productDetails['discount_type'])): ?>
                    <p><strong>Discount:</strong> <?php echo htmlspecialchars($productDetails['discount_type']); ?></p>
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
                <!-- User Details -->
                <?php if (!empty($productDetails['full_name']) || !empty($productDetails['email']) || !empty($productDetails['phone_number'])): ?>
                    <div class="user-info">
                        <?php if (!empty($productDetails['full_name'])): ?>
                            <p><strong>Posted by:</strong> <?php echo htmlspecialchars($productDetails['full_name']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($productDetails['email'])): ?>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($productDetails['email']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($productDetails['phone_number'])): ?>
                            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($productDetails['phone_number']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php elseif (isset($error_message)): ?>
            <p class="text-center"><?php echo htmlspecialchars($error_message); ?></p>
        <?php else: ?>
            <p class="text-center">Product not found.</p>
        <?php endif; ?>

        <!-- Back button -->
        <div class="text-center mt-3">
            <a href="javascript:history.back()" class="btn btn-submit">Back</a>
        </div>
    </div>
    <script src="../js/slider.js"></script>
    <?php include '../includes/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
