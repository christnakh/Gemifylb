<?php
include_once '../config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Constants for pagination
$perPage = 15; // Products per page

// Determine current page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $perPage;

// Sorting will be default or shuffle only for this case
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';
$orderBy = 'name ASC'; // Default sorting

switch ($sort) {
    case 'name_asc':
        $orderBy = 'name ASC';
        break;
    case 'name_desc':
        $orderBy = 'name DESC';
        break;
    case 'weight_asc':
        $orderBy = 'weight ASC';
        break;
    case 'weight_desc':
        $orderBy = 'weight DESC';
        break;
    case 'shuffle':
        $orderBy = 'RAND()'; // Shuffle products
        break;
}

// Prepare SQL query for fetching user's favorite products
$query = "
    SELECT p.*, 
           CASE WHEN uf.product_id IS NOT NULL THEN 1 ELSE 0 END AS favorite_status
    FROM (
        SELECT 'diamond' AS type, id, nature AS name, weight, cut_type AS cut, shape, color, photo_diamond AS photo, photo_certificate AS certificate, NULL AS price
        FROM diamond WHERE is_approved = 'Accept'
        UNION ALL
        SELECT 'gemstone' AS type, id, gemstone_name AS name, weight, cut, shape, color, photo_gemstone AS photo, photo_certificate AS certificate, 'price/ct' AS price
        FROM gemstone WHERE is_approved = 'Accept'
        UNION ALL
        SELECT 'jewelry' AS type, id, title AS name, NULL AS weight, NULL AS cut, NULL AS shape, type AS color, photo_jewelry AS photo, photo_certificate AS certificate, price AS price
        FROM jewelry WHERE is_approved = 'Accept'
        UNION ALL
        SELECT 'black_diamond' AS type, id, NULL AS name, weight, NULL AS cut, shape, NULL AS color, photo_diamond AS photo, photo_certificate AS certificate, 'price/ct' AS price
        FROM black_diamonds WHERE is_approved = 'Accept'
        UNION ALL
        SELECT 'gadget' AS type, id, title AS name, NULL AS weight, NULL AS cut, NULL AS shape, NULL AS color, photo_gadget AS photo, NULL AS certificate, price AS price
        FROM gadgets WHERE is_approved = 'Accept'
        UNION ALL
        SELECT 'watch' AS type, id, title AS name, NULL AS weight, NULL AS cut, NULL AS shape, brand AS color, photo_watch AS photo, photo_certificate AS certificate, price AS price
        FROM watches WHERE is_approved = 'Accept'
    ) p
    INNER JOIN user_favorites uf ON p.id = uf.product_id AND uf.user_id = :user_id AND uf.product_type = p.type
    ORDER BY $orderBy
    LIMIT :offset, :perPage";

try {
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
    if (isset($_SESSION['user_id'])) {
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    }
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count total favorite products
    $queryCount = "
    SELECT COUNT(*) AS total FROM (
        SELECT id FROM diamond 
        INNER JOIN user_favorites uf ON diamond.id = uf.product_id AND uf.product_type = 'diamond'
        WHERE diamond.is_approved = 'Accept' AND uf.user_id = :user_id
        UNION ALL
        SELECT id FROM gemstone 
        INNER JOIN user_favorites uf ON gemstone.id = uf.product_id AND uf.product_type = 'gemstone'
        WHERE gemstone.is_approved = 'Accept' AND uf.user_id = :user_id
        UNION ALL
        SELECT id FROM jewelry 
        INNER JOIN user_favorites uf ON jewelry.id = uf.product_id AND uf.product_type = 'jewelry'
        WHERE jewelry.is_approved = 'Accept' AND uf.user_id = :user_id
        UNION ALL
        SELECT id FROM black_diamonds 
        INNER JOIN user_favorites uf ON black_diamonds.id = uf.product_id AND uf.product_type = 'black_diamond'
        WHERE black_diamonds.is_approved = 'Accept' AND uf.user_id = :user_id
        UNION ALL
        SELECT id FROM gadgets 
        INNER JOIN user_favorites uf ON gadgets.id = uf.product_id AND uf.product_type = 'gadget'
        WHERE gadgets.is_approved = 'Accept' AND uf.user_id = :user_id
        UNION ALL
        SELECT id FROM watches 
        INNER JOIN user_favorites uf ON watches.id = uf.product_id AND uf.product_type = 'watch'
        WHERE watches.is_approved = 'Accept' AND uf.user_id = :user_id
    ) AS combined";
    $stmtCount = $conn->prepare($queryCount);
    $stmtCount->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmtCount->execute();
    $total = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($total / $perPage);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorite Products</title>
    <link rel="stylesheet" href="../css/products.css">
    <link rel="stylesheet" href="../css/global.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4 p-4">
        <h1>My Favorite Products</h1>

        <div class="products">
            <?php if ($products) : ?>
                <?php foreach ($products as $product) : ?>
                    <div class="product">
                        <h2 class="text-center"><?= htmlspecialchars($product['name']) ?></h2>
                        <div class="image-slider">
                        <div class="slider-container">
                            <?php
                            $folderMapping = [
                                'diamond' => 'diamond',
                                'gemstone' => 'gemstones',
                                'jewelry' => 'jewelry',
                                'black_diamond' => 'diamond',
                                'gadget' => 'gadgets',
                                'watch' => 'watches'
                            ];
                            $folder = isset($folderMapping[$product['type']]) ? $folderMapping[$product['type']] : $product['type'];
                            ?>
                            <?php if (!empty($product['photo'])) : ?>
                                <img src="../uploads/<?= htmlspecialchars($folder) ?>/photo/<?= htmlspecialchars($product['photo']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                            <?php endif; ?>
                            <?php if (!empty($product['certificate'])) : ?>
                                <img src="../uploads/<?= htmlspecialchars($folder) ?>/certificates/<?= htmlspecialchars($product['certificate']) ?>" alt="Certificate for <?= htmlspecialchars($product['name']) ?>">
                            <?php endif; ?>
                        </div>

                        <div class="slider-nav">
                            <button class="prev">&lt;</button>
                            <div class="slider-dots"></div>
                            <button class="next">&gt;</button>
                        </div>
                        </div>
                        <?php if (!empty($product['weight'])) : ?>
                            <p><strong>Weight:</strong> <?= htmlspecialchars($product['weight']) ?> carats</p>
                        <?php endif; ?>
                        <?php if (!empty($product['cut'])) : ?>
                            <p><strong>Cut:</strong> <?= htmlspecialchars($product['cut']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($product['shape'])) : ?>
                            <p><strong>Shape:</strong> <?= htmlspecialchars($product['shape']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($product['color'])) : ?>
                            <p><strong>Color:</strong> <?= htmlspecialchars($product['color']) ?></p>
                        <?php endif; ?>
                        <p><strong>Price:</strong> 
                            <?= !empty($product['price']) && $product['price'] > 0 ? '$' . number_format($product['price'], 2) : 'Unavailable' ?>
                        </p>

                        <form class="view-details-form" action="view_products_info.php" method="post">
                            <input type="hidden" name="type" value="<?= htmlspecialchars($product['type']) ?>">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">
                            <input type="submit" value="View Details" class="btn btn-primary">
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No favorite products found.</p>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&sort=<?= htmlspecialchars($sort) ?>" class="btn btn-primary">Previous</a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>&sort=<?= htmlspecialchars($sort) ?>" class="btn btn-primary">Next</a>
            <?php endif; ?>
        </div>
    </div>
    <script src="../js/slider.js"></script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
