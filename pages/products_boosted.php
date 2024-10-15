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

// Determine sorting order
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

// Prepare SQL query with sorting, pagination, and filtering for boosted products
// Prepare SQL query to fetch only boosted products with pagination and favorite status
$query = "
    SELECT p.*, 
           CASE WHEN uf.product_id IS NOT NULL THEN 1 ELSE 0 END AS favorite_status
    FROM (
        -- Diamonds
        SELECT 'diamond' AS type, id, nature AS name, weight, cut_type AS cut, shape, color, photo_diamond AS photo, 
               photo_certificate AS certificate, NULL AS price, NULL AS type_p, NULL AS description, NULL AS brand
        FROM diamond 
        WHERE is_approved = 'Accept' AND boost = 1
        
        UNION ALL
        
        -- Gemstones
        SELECT 'gemstone' AS type, id, gemstone_name AS name, weight, cut, shape, color, photo_gemstone AS photo, 
               photo_certificate AS certificate, 'price/ct' AS price, NULL AS type_p, NULL AS description, NULL AS brand
        FROM gemstone 
        WHERE is_approved = 'Accept' AND boost = 1
        
        UNION ALL
        
        -- Jewelry
        SELECT 'jewelry' AS type, id, title AS name, NULL AS weight, NULL AS cut, NULL AS shape, NULL AS color, 
               photo_jewelry AS photo, photo_certificate AS certificate, price AS price, type AS type_p, description, NULL AS brand
        FROM jewelry 
        WHERE is_approved = 'Accept' AND boost = 1
        
        UNION ALL
        
        -- Black Diamonds
        SELECT 'black_diamond' AS type, id, NULL AS name, weight, NULL AS cut, shape, NULL AS color, 
               photo_diamond AS photo, photo_certificate AS certificate, 'price/ct' AS price, NULL AS type_p, NULL AS description, NULL AS brand
        FROM black_diamonds 
        WHERE is_approved = 'Accept' AND boost = 1
        
        UNION ALL
        
        -- Gadgets
        SELECT 'gadget' AS type, id, title AS name, NULL AS weight, NULL AS cut, NULL AS shape, NULL AS color, 
               photo_gadget AS photo, NULL AS certificate, price AS price, NULL AS type_p, description, NULL AS brand
        FROM gadgets 
        WHERE is_approved = 'Accept' AND boost = 1
        
        UNION ALL
        
        -- Watches
        SELECT 'watch' AS type, id, title AS name, NULL AS weight, NULL AS cut, NULL AS shape, brand AS color, 
               photo_watch AS photo, photo_certificate AS certificate, price AS price, NULL AS type_p, description, brand
        FROM watches 
        WHERE is_approved = 'Accept' AND boost = 1
    ) p
    -- Join user favorites to check if the product is a favorite
    LEFT JOIN user_favorites uf ON p.id = uf.product_id AND uf.user_id = :user_id AND uf.product_type = p.type
    ORDER BY $orderBy
    LIMIT :offset, :perPage";

try {
    // Prepare the query for execution
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
    
    // Bind user_id if available in the session
    if (isset($_SESSION['user_id'])) {
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    }
    
    // Execute the query and fetch the results
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Count total boosted products across all categories
    $queryCount = "
    SELECT COUNT(*) AS total FROM (
        SELECT id FROM diamond WHERE is_approved = 'Accept' AND boost = 1 AND is_active = 1
        UNION ALL
        SELECT id FROM gemstone WHERE is_approved = 'Accept' AND boost = 1 AND is_active = 1
        UNION ALL
        SELECT id FROM jewelry WHERE is_approved = 'Accept' AND boost = 1 AND is_active = 1
        UNION ALL
        SELECT id FROM black_diamonds WHERE is_approved = 'Accept' AND boost = 1 AND is_active = 1
        UNION ALL
        SELECT id FROM gadgets WHERE is_approved = 'Accept' AND boost = 1 AND is_active = 1
        UNION ALL
        SELECT id FROM watches WHERE is_approved = 'Accept' AND boost = 1 AND is_active = 1
    ) AS combined";
    
    // Execute the count query
    $stmtCount = $conn->query($queryCount);
    $total = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
    $totalPages = ceil($total / $perPage);

    // Fetch favorite status for the logged-in user, if applicable
    $favorites = [];
    if (isset($_SESSION['user_id'])) {
        $user_id = intval($_SESSION['user_id']);
        $queryFavorites = "
        SELECT product_id, product_type 
        FROM user_favorites 
        WHERE user_id = :user_id";
        
        $stmtFavorites = $conn->prepare($queryFavorites);
        $stmtFavorites->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmtFavorites->execute();
        $favorites = $stmtFavorites->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    
} catch(PDOException $e) {
    // Handle any SQL or PDO exceptions
    echo "Error: " . $e->getMessage();
    die();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boosted Products</title>
    <link rel="stylesheet" href="../css/products.css">
    <link rel="stylesheet" href="../css/global.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <!-- Favicon -->
  <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <script>
        $(document).ready(function() {
            $('.favorite-btn').click(function() {
                var button = $(this);
                var productId = button.data('id');
                var productType = button.data('type');

                $.ajax({
                    url: 'toggle_favorite.php',
                    type: 'POST',
                    data: {
                        id: productId,
                        type: productType
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log(response); // Debugging output

                        if (response.status === 'added') {
                            button.text('Unfavorite').removeClass('btn-primary').addClass('btn-secondary');
                        } else if (response.status === 'removed') {
                            button.text('Favorite').removeClass('btn-secondary').addClass('btn-primary');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error: ', status, error); // Debugging output
                        alert('An error occurred while processing your request.');
                    }
                });
            });
        });
    </script>

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
        <h1>Boosted Products</h1>

        <!-- Sorting Bar -->
        <form method="GET" id="sorting-form">
            <label for="sort-by">Sort by:</label>
            <select name="sort" id="sort-by" onchange="document.getElementById('sorting-form').submit();">
                <option value="name_asc" <?= (isset($_GET['sort']) && $_GET['sort'] === 'name_asc') ? 'selected' : '' ?>>Name (A-Z)</option>
                <option value="name_desc" <?= (isset($_GET['sort']) && $_GET['sort'] === 'name_desc') ? 'selected' : '' ?>>Name (Z-A)</option>
                <option value="weight_asc" <?= (isset($_GET['sort']) && $_GET['sort'] === 'weight_asc') ? 'selected' : '' ?>>Weight (Low-High)</option>
                <option value="weight_desc" <?= (isset($_GET['sort']) && $_GET['sort'] === 'weight_desc') ? 'selected' : '' ?>>Weight (High-Low)</option>
                <option value="favorites" <?= (isset($_GET['sort']) && $_GET['sort'] === 'favorites') ? 'selected' : '' ?>>Favorites</option>
                <option value="shuffle" <?= (isset($_GET['sort']) && $_GET['sort'] === 'shuffle') ? 'selected' : '' ?>>Shuffle</option>
            </select>
        </form>

        <div class="products">
            <?php foreach ($products as $product) : ?>
                <div class="product">
                    <h2 class="text-center"><?= htmlspecialchars($product['name']) ?></h2>
                    <h6 class="text-center product_type"><?= htmlspecialchars($product['type']) ?></h6>
                    <div class="image-slider">
                        <div class="slider-container">
                            <?php
                            // Determine the correct folder name based on the product type
                            $folderMapping = [
                                'diamond' => 'diamond',
                                'gemstone' => 'gemstones', // Correct the folder name for gemstones
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
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1) : ?>
                <a href="?page=<?= ($page - 1) ?>&sort=<?= htmlspecialchars($sort) ?>">Previous</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <a href="?page=<?= $i ?>&sort=<?= htmlspecialchars($sort) ?>" <?= ($i === $page) ? 'class="active"' : '' ?>><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages) : ?>
                <a href="?page=<?= ($page + 1) ?>&sort=<?= htmlspecialchars($sort) ?>">Next</a>
            <?php endif; ?>
        </div>
    </div>

    <script src="../js/slider.js"></script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
