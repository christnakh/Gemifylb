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
$onlyFavorites = false;

// Prepare sorting based on user input
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
    case 'favorites':
        $orderBy = 'name ASC'; // Sort by name for favorites display
        $onlyFavorites = true; // Set flag to filter favorites
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
        SELECT 'diamond' AS type, id, nature AS name, weight, cut_type AS cut, shape, color, photo_diamond AS photo, photo_certificate AS certificate, NULL AS price, NULL AS type_p, NULL AS description, NULL AS brand
        FROM diamond WHERE is_approved = 'Accept' AND is_active = 1
        UNION ALL
        SELECT 'gemstone' AS type, id, gemstone_name AS name, weight, cut, shape, color, photo_gemstone AS photo, photo_certificate AS certificate, 'price/ct' AS price, NULL AS type_p, NULL AS description, NULL AS brand
        FROM gemstone WHERE is_approved = 'Accept' AND is_active = 1
        UNION ALL
        SELECT 'jewelry' AS type, id, title AS name, NULL AS weight, NULL AS cut, NULL AS shape, NULL AS color, photo_jewelry AS photo, photo_certificate AS certificate, price AS price, type AS type_p, description, NULL AS brand
        FROM jewelry WHERE is_approved = 'Accept' AND is_active = 1
        UNION ALL
        SELECT 'black_diamond' AS type, id, NULL AS name, weight, NULL AS cut, shape, NULL AS color, photo_diamond AS photo, photo_certificate AS certificate, 'price/ct' AS price, NULL AS type_p, NULL AS description, NULL AS brand
        FROM black_diamonds WHERE is_approved = 'Accept' AND is_active = 1
        UNION ALL
        SELECT 'gadget' AS type, id, title AS name, NULL AS weight, NULL AS cut, NULL AS shape, NULL AS color, photo_gadget AS photo, NULL AS certificate, price AS price, NULL AS type_p, description, NULL AS brand
        FROM gadgets WHERE is_approved = 'Accept' AND is_active = 1
        UNION ALL
        SELECT 'watch' AS type, id, title AS name, NULL AS weight, NULL AS cut, NULL AS shape, brand AS color, photo_watch AS photo, photo_certificate AS certificate, price AS price, NULL AS type_p, description, brand
        FROM watches WHERE is_approved = 'Accept' AND is_active = 1
    ) p
    INNER JOIN user_favorites uf ON p.id = uf.product_id AND uf.user_id = :user_id AND uf.product_type = p.type
    ORDER BY $orderBy
    LIMIT :offset, :perPage";

try {
    // Prepare the statement
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
    if (isset($_SESSION['user_id'])) {
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    }

    // Execute the query
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count total favorite products
    $queryCount = "
    SELECT COUNT(*) AS total FROM (
        SELECT id FROM diamond
        INNER JOIN user_favorites uf ON diamond.id = uf.product_id AND uf.product_type = 'diamond'
        WHERE diamond.is_approved = 'Accept' AND diamond.is_active = 1 AND uf.user_id = :user_id
        UNION ALL
        SELECT id FROM gemstone
        INNER JOIN user_favorites uf ON gemstone.id = uf.product_id AND uf.product_type = 'gemstone'
        WHERE gemstone.is_approved = 'Accept' AND gemstone.is_active = 1 AND uf.user_id = :user_id
        UNION ALL
        SELECT id FROM jewelry
        INNER JOIN user_favorites uf ON jewelry.id = uf.product_id AND uf.product_type = 'jewelry'
        WHERE jewelry.is_approved = 'Accept' AND jewelry.is_active = 1 AND uf.user_id = :user_id
        UNION ALL
        SELECT id FROM black_diamonds
        INNER JOIN user_favorites uf ON black_diamonds.id = uf.product_id AND uf.product_type = 'black_diamond'
        WHERE black_diamonds.is_approved = 'Accept' AND black_diamonds.is_active = 1 AND uf.user_id = :user_id
        UNION ALL
        SELECT id FROM gadgets
        INNER JOIN user_favorites uf ON gadgets.id = uf.product_id AND uf.product_type = 'gadget'
        WHERE gadgets.is_approved = 'Accept' AND gadgets.is_active = 1 AND uf.user_id = :user_id
        UNION ALL
        SELECT id FROM watches
        INNER JOIN user_favorites uf ON watches.id = uf.product_id AND uf.product_type = 'watch'
        WHERE watches.is_approved = 'Accept' AND watches.is_active = 1 AND uf.user_id = :user_id
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

        <!-- Favicon -->
  <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
        <!-- other meta tags and elements -->
    <meta name="apple-mobile-web-app-capable" content="yes">
  <!-- Android -->
    <meta name="mobile-web-app-capable" content="yes">

    <style>
    /* Reset some basic elements */
    .product{
    background-color: white !important;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.view-all-btn {
    width: 30% !important;
    background-color: #007bff; /* Bootstrap primary color */
    color: white;
    border-radius: 5px;
    text-decoration: none;
}

.view-all-btn:hover {
    background-color: #0056b3; /* Darker shade for hover */
    color: white;
}

.boostedTitle{
    width: 30% !important;
}

.BoostedContainer{
    width: 70% !important;
    display: flex;
    justify-content: flex-end;
    align-items: center;
}

.product_type{
    font-weight: 500;
    font-size: 15px;
    color: grey;
}


</style>
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
                        <h6 class="text-center product_type"><?= htmlspecialchars($product['type']) ?></h6>
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


                        <div class="product-info">
                            <?php if (!empty($product['weight'])) : ?>
                                <p><i class="fas fa-weight-hanging"></i> <?= htmlspecialchars($product['weight']) ?> carats</p>
                            <?php endif; ?>

                            <?php if (!empty($product['cut'])) : ?>
                                <p><i class="fas fa-gem"></i> <?= htmlspecialchars($product['cut']) ?></p>
                            <?php endif; ?>

                            <?php if (!empty($product['shape'])) : ?>
                                <p><i class="fas fa-shapes"></i><strong></strong> <?= htmlspecialchars($product['shape']) ?></p>
                            <?php endif; ?>

                            <?php if (!empty($product['color'])) : ?>
                                <p><i class="fas fa-paint-brush"></i> <?= htmlspecialchars($product['color']) ?></p>
                            <?php endif; ?>

                            <?php if (!empty($product['Brand'])) : ?>
                                <p><i class="fas fa-gem"></i> <?= htmlspecialchars($product['Brand']) ?></p>
                            <?php endif; ?>

                            <?php if (!empty($product['type_p'])) : ?>
                                <p><i class="fas fa-ring"></i> <?= htmlspecialchars($product['type_p']) ?></p>
                            <?php endif; ?>

                            <?php if (!empty($product['description'])) : ?>
                                <p><i class="fas fa-info-circle"></i> <?= htmlspecialchars($product['description']) ?></p>
                            <?php endif; ?>

                            <p>
                                <?= !empty(floatval($product['price'])) && floatval($product['price']) > 0 ? '<i class="fas fa-dollar-sign"></i> 
                                ' . number_format(floatval($product['price']), 2) : '' ?>
                            </p>
                        </div>

                        <div class="actions">
                            <button class="favorite-btn btn btn-primary" data-id="<?= htmlspecialchars($product['id']) ?>" data-type="<?= htmlspecialchars($product['type']) ?>">
                                <?php
                                $queryFavorite = "SELECT COUNT(*) FROM user_favorites WHERE user_id = :user_id AND product_id = :product_id AND product_type = :product_type";
                                $stmtFavorite = $conn->prepare($queryFavorite);
                                $stmtFavorite->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                                $stmtFavorite->bindParam(':product_id', $product['id'], PDO::PARAM_INT);
                                $stmtFavorite->bindParam(':product_type', $product['type'], PDO::PARAM_STR);
                                $stmtFavorite->execute();
                                $isFavorited = $stmtFavorite->fetchColumn() > 0;
                                echo $isFavorited ? 'Unfavorite' : 'Favorite';
                                ?>
                            </button>
                            <form class="view-details-form" action="view_products_info.php" method="post">
                                <input type="hidden" name="type" value="<?= htmlspecialchars($product['type']) ?>">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($product['id']) ?>">
                                <button type="submit" class="btn btn-submit">View Details</button>
                            </form>
                        </div>

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

</body>
</html>
