<?php


// Include database connection
include_once '../config/db.php';
// Initialize product variable


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Check if user is approved
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT is_approved FROM users WHERE id = ?");
$stmt->bindParam(1, $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user['is_approved'] != 1) {
    // User is not approved, show a message and wait for OK before redirecting
    echo "<script>
            alert('You are not authorized to post. Please wait for admin approval.');
            window.location.href = '../index.php'; // Redirect after OK is clicked
          </script>";
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
                $query = "SELECT diamond.*, users.full_name, users.email, users.phone_number, users.id AS userID
                          FROM diamond
                          JOIN users ON diamond.user_id = users.id 
                          WHERE diamond.id = :id AND diamond.is_approved = 'Accept'";
                break;
                
            case 'gemstone':
                $query = "SELECT gemstone.*, users.full_name, users.email, users.phone_number, users.id AS userID
                          FROM gemstone 
                          JOIN users ON gemstone.user_id = users.id 
                          WHERE gemstone.id = :id AND gemstone.is_approved = 'Accept'";
                break;

            case 'jewelry':
                $query = "SELECT jewelry.*, users.full_name, users.email, users.phone_number, users.id AS userID, `type` AS type_p 
                          FROM jewelry 
                          JOIN users ON jewelry.user_id = users.id 
                          WHERE jewelry.id = :id AND jewelry.is_approved = 'Accept'";
                break;

            case 'black_diamond':
                $query = "SELECT black_diamonds.*, users.full_name, users.email, users.phone_number, users.id AS userID 
                          FROM black_diamonds 
                          JOIN users ON black_diamonds.user_id = users.id 
                          WHERE black_diamonds.id = :id AND black_diamonds.is_approved = 'Accept'";
                break;

            case 'gadget':
                $query = "SELECT gadgets.*, users.full_name, users.email, users.phone_number, users.id AS userID 
                          FROM gadgets 
                          JOIN users ON gadgets.user_id = users.id 
                          WHERE gadgets.id = :id AND gadgets.is_approved = 'Accept'";
                break;

            case 'watch':
                $query = "SELECT watches.*, users.full_name, users.email, users.phone_number, users.id AS userID 
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
                    'ID' => $product['userID'] ?? '',
                    'title' => $product['title'] ?? $product['nature'] ?? $product['gemstone_name'] ?? '',
                    'photo' => $photoPath,
                    'certificate' => $certificatePath,
                    'video' => $videoPath,
                    'weight' => $product['weight'] ?? '',
                    'cut' => $product['cut'] ?? $product['cut_type'] ?? '',
                    'shape' => $product['shape'] ?? '',
                    'type_p' => $product['type_p'] ?? '',
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
    <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/product_details.css">
    <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <style>
        /* Modern styling */
        .product-details-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .product-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            background-color: #ffffff;
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Carousel Styles */
        .carousel-inner img {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 30px;
            padding: 20px;
        }

        .carousel-inner video {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 30px;
            padding: 20px;
        }

        .carousel-control-prev-icon, .carousel-control-next-icon {
            background-color: #000;
        }

        /* Product Information Styling */
        .product-info {
            margin: 0 auto;
            padding: 20px;
        }

        .product-info h1 {
            font-size: 2rem;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .product-info p {
            font-size: 1rem;
            margin-bottom: 10px;
        }

        /* Adjust grid for 3 columns */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 3 columns layout */
            gap: 15px;
            margin: 20px 0;
            justify-content: center; /* Center the grid */
            align-items: start; /* Ensure all items align at the top */
        }

        /* Modern Icons and Side-by-Side Info */
        .info-item {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
            justify-content: center; /* Center content in each item */
            text-align: center; /* Ensure text and icon are centered */
        }

        .info-item i {
            font-size: 24px;
            margin-right: 10px;
            color: #0069d9;
        }

        .info-item span {
            font-size: 16px;
            font-weight: 500;
        }

        /* Back Button Styling */
        .back-button {
            display: inline-flex;
            align-items: center;
            background-color: #ffd700; /* Gold color */
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-bottom: 15px;
            margin-left: 20px;
        }

        .back-button:hover {
            background-color: #e6c200;
            transform: scale(1.05);
        }

        .back-button i {
            margin-right: 10px;
            font-size: 24px;
        }

        /* Contact Button Styling */
        .contact-button {
            display: inline-flex;
            align-items: center;
            background-color: #28a745; /* Green color */
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin-top: 20px;
        }

        .contact-button:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        .contact-button i {
            margin-right: 10px;
            font-size: 24px;
        }

        /* Responsive design adjustments */
        @media screen and (max-width: 1024px) {
            .info-grid {
                grid-template-columns: repeat(2, 1fr); /* 2 columns on medium screens */
            }
        }

        @media screen and (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr; /* Stack vertically on smaller screens */
            }
        }

        /* Styling for product description */
        .product-description {
            background-color: #f9f9f9; /* Light gray background for readability */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); /* Slight shadow for depth */
            margin-bottom: 20px;
            max-width: 90%; /* Ensure it's responsive and fits well on mobile */
            margin-left: auto;
            margin-right: auto;
        }

        .description-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333; /* Darker color for title */
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }

        .description-title i {
            margin-right: 10px;
            color: #0069d9; /* Blue color for the icon */
        }

        .description-text {
            font-size: 1rem;
            line-height: 1.6;
            color: #555; /* Slightly muted gray for readability */
        }

        @media screen and (max-width: 768px) {
            .product-description {
                max-width: 100%; /* Full width on smaller screens */
            }
        }

        form {
            display: inline-block;
        }


    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <br><br>
    <div class="container product-details-container">
        
        <?php if (isset($productDetails) && !empty($productDetails)): ?>
            <!-- Back Button -->
            <a href="javascript:history.back();" class="back-button">
                <span class="d-flex align-items-center"><i class="material-icons">arrow_back</i> back</span>
            </a>
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <!-- Product Details Card -->
                    <div class="product-card">
                        <!-- Product Image Carousel -->
                        <div class="carousel-container">
                            <div id="productImageCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <!-- Product Photo -->
                                    <?php if (!empty($productDetails['photo'])): ?>
                                        <div class="carousel-item active">
                                            <img src="../uploads/<?php echo htmlspecialchars($productDetails['photo']); ?>" alt="Product Image" class="d-block w-100">
                                        </div>
                                    <?php endif; ?>

                                    <!-- Product Certificate -->
                                    <?php if (!empty($productDetails['certificate'])): ?>
                                        <div class="carousel-item">
                                            <img src="../uploads/<?php echo htmlspecialchars($productDetails['certificate']); ?>" alt="Certificate Image" class="d-block w-100">
                                        </div>
                                    <?php endif; ?>

                                    <!-- Video -->
                                    <?php if (!empty($productDetails['video'])): ?>
                                        <div class="carousel-item">
                                            <video controls class="d-block w-100">
                                                <source src="../uploads/<?php echo htmlspecialchars($productDetails['video']); ?>" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Carousel Controls -->
                                <!--<button class="carousel-control-prev" type="button" data-bs-target="#productImageCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#productImageCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>-->

                                <!-- Carousel Indicators (Dots) -->
                                <div class="carousel-indicators">
                                    <!-- Dynamic Indicators based on the number of items -->
                                    <?php 
                                    $items = 0;
                                    $items += !empty($productDetails['photo']) ? 1 : 0;
                                    $items += !empty($productDetails['certificate']) ? 1 : 0;
                                    $items += !empty($productDetails['video']) ? 1 : 0;

                                    // Generate the dots (indicators)
                                    for ($i = 0; $i < $items; $i++) { 
                                        echo '<button type="button" data-bs-target="#productImageCarousel" data-bs-slide-to="' . $i . '" class="' . ($i === 0 ? 'active' : '') . '" aria-current="' . ($i === 0 ? 'true' : 'false') . '" aria-label="Slide ' . ($i + 1) . '"></button>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>


                        <!-- Product Information -->
                        <div class="col-lg-8 mx-auto">
                            <div class="product-info">
                                <div class="row justify-content-center info-grid">
                                    
                                    <!-- Product Title -->
                                    <?php if (!empty($productDetails['title'])): ?>
                                        <h1><?php echo htmlspecialchars($productDetails['title']); ?></h1>
                                    <?php endif; ?>

                                    <?php if (!empty($productDetails['full_name'])): ?>
                                        <form action="view_user_products.php" method="POST" class="">
                                            <!-- Add a hidden input to send the user ID -->
                                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($productDetails['ID']); ?>">
                                            
                                            <div class="info-item text-center" style="cursor: pointer;" onclick="this.closest('form').submit();">
                                                <i class="material-icons">person</i>
                                                <span><?php echo htmlspecialchars($productDetails['full_name']); ?></span>
                                            </div>
                                        </form>
                                    <?php endif; ?>


                                    <!-- Product Weight -->
                                    <?php if (!empty($productDetails['weight'])): ?>
                                        <div class="col-lg-4 col-md-6 col-sm-12 info-item text-center">
                                            <i class="fas fa-weight-hanging"></i>
                                            <span> <b>Weight</b> <?php echo htmlspecialchars($productDetails['weight']); ?> ct</span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Product Shape -->
                                    <?php if (!empty($productDetails['shape'])): ?>
                                        <div class="col-lg-4 col-md-6 col-sm-12 info-item text-center">
                                            <i class="fas fa-shapes"></i>
                                            <span> <b>Shape</b> <?php echo htmlspecialchars($productDetails['shape']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Product Color -->
                                    <?php if (!empty($productDetails['color'])): ?>
                                        <div class="col-lg-4 col-md-6 col-sm-12 info-item text-center">
                                            <i class="fas fa-paint-brush"></i>
                                            <span> <b>Color</b> <?php echo htmlspecialchars($productDetails['color']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Product Clarity -->
                                    <?php if (!empty($productDetails['clarity'])): ?>
                                        <div class="col-lg-4 col-md-6 col-sm-12 info-item text-center">
                                            <i class="material-icons">visibility</i>
                                            <span> <b>Clarity</b> <?php echo htmlspecialchars($productDetails['clarity']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Product Cut -->
                                    <?php if (!empty($productDetails['cut'])): ?>
                                        <div class="col-lg-4 col-md-6 col-sm-12 info-item text-center">
                                            <i class="material-icons">diamond</i>
                                            <span> <b>Cut</b> <?php echo htmlspecialchars($productDetails['cut']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Product Discount Type -->
                                    <?php if (!empty($productDetails['discount_type'])): ?>
                                        <div class="col-lg-4 col-md-6 col-sm-12 info-item text-center">
                                            <i class="material-icons">local_offer</i>
                                            <span> <b>Discount</b> <?php echo htmlspecialchars($productDetails['discount_type']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Product Certificate -->
                                    <?php if (!empty($productDetails['certificate_type'])): ?>
                                        <div class="col-lg-4 col-md-6 col-sm-12 info-item text-center">
                                            <i class="material-icons">assignment</i>
                                            <span> <b>Certificate</b> <?php echo htmlspecialchars($productDetails['certificate_type']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Product Brand -->
                                    <?php if (!empty($productDetails['brand'])): ?>
                                        <div class="col-lg-4 col-md-6 col-sm-12 info-item text-center">
                                            <i class="material-icons">business</i>
                                            <span> <b>Brand</b> <?php echo htmlspecialchars($productDetails['brand']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Product Price -->
                                    <?php if (!empty($productDetails['price'])): ?>
                                        <div class="col-lg-4 col-md-6 col-sm-12 info-item text-center">
                                            <i class="material-icons">attach_money</i>
                                            <span> <b>Price</b> <?php echo htmlspecialchars($productDetails['price']); ?> </span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Product Type -->
                                    <?php if (!empty($productDetails['type_p'])): ?>
                                        <div class="col-lg-4 col-md-6 col-sm-12 info-item text-center">
                                            <i class="material-icons">label</i>
                                            <span> <b>Type</b> <?php echo htmlspecialchars($productDetails['type_p']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Product Fluorescence -->
                                    <?php if (!empty($productDetails['fluorescence_type'])): ?>
                                        <div class="col-lg-4 col-md-6 col-sm-12 info-item text-center">
                                            <i class="material-icons">lightbulb</i>
                                            <span> <b>Fluorescence</b> <?php echo htmlspecialchars($productDetails['fluorescence_type']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Comment -->
                                    <?php if (!empty($productDetails['comment'])): ?>
                                        <div class="col-lg-4 col-md-6 col-sm-12 info-item text-center">
                                            <i class="material-icons">chat</i>
                                            <span> <b>Comment</b> <?php echo htmlspecialchars($productDetails['comment']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Product Boost -->
                                    <?php if (!empty($productDetails['boost'])): ?>
                                        <div class="col-lg-4 col-md-6 col-sm-12 info-item text-center">
                                            <i class="material-icons">flash_on</i>
                                            <span> <b>Boost</b> <?php echo htmlspecialchars($productDetails['boost']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Seller Info: Phone Number -->
                                    <?php if (!empty($productDetails['phone_number'])): ?>
                                        <div class="col-lg-4 col-md-6 col-sm-12 info-item text-center">
                                            <i class="material-icons">phone</i>
                                            <span><?php echo htmlspecialchars($productDetails['phone_number']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Seller Info: Email -->
                                    <?php if (!empty($productDetails['email'])): ?>
                                        <div class="col-lg-4 col-md-6 col-sm-12 info-item text-center">
                                            <i class="material-icons">email</i>
                                            <span><?php echo htmlspecialchars($productDetails['email']); ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Description -->
                                    <?php if (!empty($productDetails['description'])): ?>
                                        <div class="product-description">
                                            <h3 class="description-title"><i class="material-icons">description</i> Description</h3>
                                            <p class="description-text"><?php echo htmlspecialchars($productDetails['description']); ?></p>
                                        </div>
                                    <?php endif; ?>


                                    <!-- Contact Button -->
                                    <div class="col-lg-12 col-md-6 col-sm-12 text-center">
                                        <button class="contact-button">
                                            <i class="material-icons">mail_outline</i>
                                            Contact Seller
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        <?php else: ?>
            <p class="text-center">Product details are not available.</p>
        <?php endif; ?>
    </div>
    

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</body>
</html>
