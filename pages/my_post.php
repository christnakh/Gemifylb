

<?php
include '../config/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$user_id = $_SESSION['user_id'];

// SQL queries to fetch all product types for the current user
$product_queries = [
    'black_diamonds' => "SELECT * FROM black_diamonds WHERE user_id = :user_id",
    'diamond' => "SELECT * FROM diamond WHERE user_id = :user_id",
    'gadgets' => "SELECT * FROM gadgets WHERE user_id = :user_id",
    'gemstone' => "SELECT * FROM gemstone WHERE user_id = :user_id",
    'jewelry' => "SELECT * FROM jewelry WHERE user_id = :user_id",
    'watches' => "SELECT * FROM watches WHERE user_id = :user_id"
];

// Fetch products for each type
$products = [];
foreach ($product_queries as $category => $query) {
    $stmt = $conn->prepare($query);
    $stmt->execute(['user_id' => $user_id]);
    $products[$category] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Posts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/my_post.css">
    <!-- Favicon -->
  <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
        <!-- other meta tags and elements -->
    <meta name="apple-mobile-web-app-capable" content="yes">
  <!-- Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 20px;
        }
        .product-container {
            margin-bottom: 20px;
        }
        .product-box {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            flex-direction: column;
            transition: box-shadow 0.3s ease;
        }
        .product-box:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .product-media {
            margin-bottom: 15px;
        }
        .image-slider {
            position: relative;
            max-width: 100%;
            margin: auto;
        }
        .slider-container {
            display: flex;
            overflow: hidden;
        }
        .slider-container img, .slider-container video {
            max-width: 100%;
            display: block;
            border-radius: 5px;
        }
        .slider-nav {
            text-align: center;
            margin-top: 10px;
        }
        .prev, .next {
            cursor: pointer;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            margin: 0 5px;
            border-radius: 5px;
        }
        .prev:hover, .next:hover {
            background-color: #0056b3;
        }
        .dot {
            height: 10px;
            width: 10px;
            margin: 0 2px;
            background-color: #bbb;
            border-radius: 50%;
            display: inline-block;
            transition: background-color 0.6s ease;
        }
        .dot.active {
            background-color: #717171;
        }
        .product-actions {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 10px;
        }
        .action-form {
            display: flex;
            justify-content: center;
        }
        @media (max-width: 1200px) {
            .product-actions {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 900px) {
            .product-actions {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 600px) {
            .product-actions {
                grid-template-columns: repeat(1, 1fr);
            }
        }
        .product-details p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>
<div class="container">
    <h1 class="text-center mb-4">My Products</h1>

    <?php foreach ($products as $category => $items): ?>
        <div class="product-container">
            <h2><?= str_replace('_', ' ', $category) ?> Products</h2>
            <div class="row">
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                            <div class="product-box">
                                <div class="product-media">
                                    <div class="image-slider">
                                        <div class="slider-container">
                                            <?php if (!empty($item['photo_certificate'])): ?>
                                                <img src="../uploads/<?= $category ?>/certificates/<?= htmlspecialchars($item['photo_certificate']) ?>" alt="Certificate Image">
                                            <?php endif; ?>
                                            <?php if (!empty($item['photo_diamond']) || !empty($item['photo_gadget']) || !empty($item['photo_gemstone']) || !empty($item['photo_jewelry']) || !empty($item['photo_watch'])): ?>
                                                <img src="../uploads/<?= $category ?>/photo/<?= htmlspecialchars($item['photo_diamond'] ?? $item['photo_gadget'] ?? $item['photo_gemstone'] ?? $item['photo_jewelry'] ?? $item['photo_watch']) ?>" alt="Product Image">
                                            <?php endif; ?>
                                            <?php if (!empty($item['video_diamond']) || !empty($item['video_gadget']) || !empty($item['video_gemstone']) || !empty($item['video']) || !empty($item['video_watch'])): ?>
                                                <video controls>
                                                    <source src="../uploads/<?= $category ?>/video/<?= htmlspecialchars($item['video_diamond'] ?? $item['video_gadget'] ?? $item['video_gemstone'] ?? $item['video'] ?? $item['video_watch']) ?>" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="product-details">
                                    <!-- Display product-specific details -->
                                    <?php if ($category === 'black_diamonds'): ?>
                                        <p><strong>Name:</strong> <?= htmlspecialchars($item['name']) ?></p>
                                        <p><strong>Shape:</strong> <?= htmlspecialchars($item['shape']) ?></p>
                                        <p><strong>Weight:</strong> <?= htmlspecialchars($item['weight']) ?> carats</p>
                                    <?php elseif ($category === 'diamond'): ?>
                                        <p><strong>Nature:</strong> <?= htmlspecialchars($item['nature']) ?></p>
                                        <p><strong>Shape:</strong> <?= htmlspecialchars($item['shape']) ?></p>
                                        <p><strong>Weight:</strong> <?= htmlspecialchars($item['weight']) ?> carats</p>
                                        <p><strong>Cut Type:</strong> <?= htmlspecialchars($item['cut_type']) ?></p>
                                    <?php elseif ($category === 'gadgets'): ?>
                                        <p><strong>Title:</strong> <?= htmlspecialchars($item['title']) ?></p>
                                        <p><strong>Description:</strong> <?= htmlspecialchars($item['description']) ?></p>
                                        <p><strong>Price:</strong> $<?= htmlspecialchars($item['price']) ?></p>
                                    <?php elseif ($category === 'gemstone'): ?>
                                        <p><strong>Name:</strong> <?= htmlspecialchars($item['gemstone_name']) ?></p>
                                        <p><strong>Shape:</strong> <?= htmlspecialchars($item['shape']) ?></p>
                                        <p><strong>Weight:</strong> <?= htmlspecialchars($item['weight']) ?> carats</p>
                                    <?php elseif ($category === 'jewelry'): ?>
                                        <p><strong>Title:</strong> <?= htmlspecialchars($item['title']) ?></p>
                                        <p><strong>Description:</strong> <?= htmlspecialchars($item['description']) ?></p>
                                        <p><strong>Price:</strong> $<?= htmlspecialchars($item['price']) ?></p>
                                    <?php elseif ($category === 'watches'): ?>
                                        <p><strong>Brand:</strong> <?= htmlspecialchars($item['brand']) ?></p>
                                        <p><strong>Description:</strong> <?= htmlspecialchars($item['description']) ?></p>
                                        <p><strong>Price:</strong> $<?= htmlspecialchars($item['price']) ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="product-actions">
                                    <form action="edit_<?= $category ?>.php" method="POST" class="action-form">
                                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($item['id']) ?>">
                                        <button type="submit" class="btn btn-primary btn-sm">Edit</button>
                                    </form>

                                    <form action="delete_post.php" method="post" class="action-form" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
                                        <input type="hidden" name="type" value="<?= htmlspecialchars($category) ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>

                                    <form action="toggle_status.php" method="post" class="action-form">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
                                    <input type="hidden" name="type" value="<?= htmlspecialchars($category) ?>">
                                    <input type="hidden" name="is_active" value="<?= htmlspecialchars($item['is_active']) ?>">
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <?= $item['is_active'] ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                </form>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No <?= str_replace('_', ' ', $category) ?> products found.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php include '../includes/footer.php'; ?>
</body>
</html>
