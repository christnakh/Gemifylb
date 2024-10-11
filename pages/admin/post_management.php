<?php
session_start();

// Database connection
include '../../config/db.php';

// Fetch posts based on status (pending, accepted, declined)
function fetchPostsByStatus($conn, $status) {
    try {
        $stmt = $conn->prepare('
            (SELECT id, "diamond" AS type, nature AS name, photo_diamond AS photo1, video_diamond AS video, weight, clarity, color, user_id, is_approved
            FROM diamond WHERE is_approved = :status)
            UNION ALL
            (SELECT id, "gemstone" AS type, gemstone_name AS name, photo_gemstone AS photo1, video_gemstone AS video, weight, cut, color, user_id, is_approved
            FROM gemstone WHERE is_approved = :status)
            UNION ALL
            (SELECT id, "gadget" AS type, title AS name, photo_gadget AS photo1, video_gadget AS video, NULL AS weight, NULL AS clarity, NULL AS color, user_id, is_approved
            FROM gadgets WHERE is_approved = :status)
            UNION ALL
            (SELECT id, "jewelry" AS type, title AS name, photo_jewelry AS photo1, NULL AS video, NULL AS weight, NULL AS clarity, NULL AS color, user_id, is_approved
            FROM jewelry WHERE is_approved = :status)
            UNION ALL
            (SELECT id, "watch" AS type, title AS name, photo_watch AS photo1, NULL AS video, NULL AS weight, NULL AS clarity, NULL AS color, user_id, is_approved
            FROM watches WHERE is_approved = :status)
            UNION ALL
            (SELECT id, "black_diamond" AS type, name AS name, photo_diamond AS photo1, video_diamond AS video, weight, NULL AS clarity, NULL AS color, user_id, is_approved
            FROM black_diamonds WHERE is_approved = :status)
            ORDER BY id DESC
        ');
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $posts;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

// Fetch boosted posts
function fetchBoostedPosts($conn) {
    try {
        $stmt = $conn->prepare('
            SELECT id, type, name, photo1, video, user_id FROM (
                SELECT id, "diamond" AS type, nature AS name, photo_diamond AS photo1, video_diamond AS video, user_id FROM diamond WHERE boost = 1
                UNION ALL
                SELECT id, "gemstone" AS type, gemstone_name AS name, photo_gemstone AS photo1, video_gemstone AS video, user_id FROM gemstone WHERE boost = 1
                UNION ALL
                SELECT id, "gadget" AS type, title AS name, photo_gadget AS photo1, video_gadget AS video, user_id FROM gadgets WHERE boost = 1
                UNION ALL
                SELECT id, "jewelry" AS type, title AS name, photo_jewelry AS photo1, NULL AS video, user_id FROM jewelry WHERE boost = 1
                UNION ALL
                SELECT id, "watch" AS type, title AS name, photo_watch AS photo1, NULL AS video, user_id FROM watches WHERE boost = 1
                UNION ALL
                SELECT id, "black_diamond" AS type, name AS name, photo_diamond AS photo1, video_diamond AS video, user_id FROM black_diamonds WHERE boost = 1
            ) AS boosted_products
        ');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

// Handle post approval, decline, boosting, and unboosting
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && isset($_POST['post_id'])) {
        $action = $_POST['action'];
        $post_id = $_POST['post_id'];

        try {
            if ($action === 'approve') {
                $table = $_POST['post_type'];
                $stmt = $conn->prepare("UPDATE $table SET is_approved = 'Accept' WHERE id = :id");
                $stmt->bindParam(':id', $post_id);
                $stmt->execute();

                // Notify user of approval
                $message = "Your " . ucfirst($table) . " post with ID $post_id has been approved.";
                $sender_id = $_SESSION['user_id'];
                $receiver_id = fetchPostUserId($conn, $post_id);
                insertNotification($conn, $sender_id, $receiver_id, $message);

            } elseif ($action === 'decline') {
                $table = $_POST['post_type'];
                $stmt = $conn->prepare("UPDATE $table SET is_approved = 'Decline' WHERE id = :id");
                $stmt->bindParam(':id', $post_id);
                $stmt->execute();

                // Notify user of decline
                $message = "Your " . ucfirst($table) . " post with ID $post_id has been declined.";
                $sender_id = $_SESSION['user_id'];
                $receiver_id = fetchPostUserId($conn, $post_id);
                insertNotification($conn, $sender_id, $receiver_id, $message);

            } elseif ($action === 'boost') {
                // Logic to boost the post
                $table = $_POST['post_type'];
                $stmt = $conn->prepare("UPDATE $table SET boost = 1 WHERE id = :id");
                $stmt->bindParam(':id', $post_id);
                $stmt->execute();

                // Notify user of boosting
                $message = "Your " . ucfirst($table) . " post with ID $post_id has been boosted.";
                $sender_id = $_SESSION['user_id'];
                $receiver_id = fetchPostUserId($conn, $post_id);
                insertNotification($conn, $sender_id, $receiver_id, $message);
            } elseif ($action === 'unboost') {
                // Logic to unboost the post
                $table = $_POST['post_type'];
                $stmt = $conn->prepare("UPDATE $table SET boost = 0 WHERE id = :id");
                $stmt->bindParam(':id', $post_id);
                $stmt->execute();
            }

            header('Location: post_management.php');
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

// Function to fetch user_id of post owner
function fetchPostUserId($conn, $post_id) {
    try {
        // Determine table based on post_id (implement your logic)
        $table = (strpos($post_id, 'D') === 0) ? 'diamond' : (strpos($post_id, 'G') === 0 ? 'gemstone' : (strpos($post_id, 'B') === 0 ? 'black_diamonds' : 'gadgets'));
        $stmt = $conn->prepare("SELECT user_id FROM $table WHERE id = :id");
        $stmt->bindParam(':id', $post_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['user_id'];
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return null;
    }
}

// Function to insert notification
function insertNotification($conn, $sender_id, $receiver_id, $message) {
    try {
        $stmt = $conn->prepare('INSERT INTO notifications (Sender, Receiver, message) VALUES (:sender_id, :receiver_id, :message)');
        $stmt->bindParam(':sender_id', $sender_id);
        $stmt->bindParam(':receiver_id', $receiver_id);
        $stmt->bindParam(':message', $message);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetching posts
$pendingPosts = fetchPostsByStatus($conn, 'Pending');
$acceptedPosts = fetchPostsByStatus($conn, 'Accept');
$declinedPosts = fetchPostsByStatus($conn, 'Decline');
$boostedPosts = fetchBoostedPosts($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
        }
        .sidebar {
            width: 200px;
            background-color: #f8f9fa;
            padding: 15px;
            position: fixed;
            height: 100%;
        }
        .sidebar a {
            display: block;
            padding: 10px;
            color: #333;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #007bff;
            color: white;
        }
        .main-content {
            margin-left: 220px;
            padding: 20px;
            width: 100%;
        }
        .post-card {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }
        .post-image {
            width: 100%;
            height: auto;
            max-height: 200px;
            object-fit: cover;
            margin-bottom: 15px;
        }
        .post-buttons {
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <!-- Left-side Navbar -->
    <div class="sidebar">
        <h3>Manage Posts</h3>
        <a href="#">Home</a>
        <a href="#">Posts</a>
        <a href="#">Users</a>
        <a href="#">Settings</a>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <div class="container">
            <!-- Top Navbar with Boosted, Pending, Accepted, Declined -->
            <div class="d-flex justify-content-between mb-4">
                <button class="btn btn-primary" onclick="showSection('pending')">Pending</button>
                <button class="btn btn-primary" onclick="showSection('accepted')">Accepted</button>
                <button class="btn btn-primary" onclick="showSection('declined')">Declined</button>
                <button class="btn btn-primary" onclick="showSection('boosted')">Boosted</button>
            </div>

            <!-- Pending Posts Section -->
            <div id="pendingPosts">
                <h4>Pending Posts</h4>
                <div class="row">
                    <?php foreach ($pendingPosts as $post): ?>
                    <div class="col-md-4">
                        <div class="post-card">
                            <img src="<?= $post['photo1'] ?>" alt="<?= $post['name'] ?>" class="post-image">
                            <h5><?= $post['name'] ?></h5>
                            <p>Status: Pending</p>
                            <div class="post-buttons">
                                <form method="POST" action="">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
                                    <button type="submit" name="action" value="decline" class="btn btn-danger">Decline</button>
                                    <a href="view_product.php?post_id=<?= $post['id'] ?>" class="btn btn-info">View Product</a>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Accepted Posts Section -->
            <div id="acceptedPosts" style="display:none;">
                <h4>Accepted Posts</h4>
                <div class="row">
                    <?php foreach ($acceptedPosts as $post): ?>
                    <div class="col-md-4">
                        <div class="post-card">
                            <img src="<?= $post['photo1'] ?>" alt="<?= $post['name'] ?>" class="post-image">
                            <h5><?= $post['name'] ?></h5>
                            <p>Status: Accepted</p>
                            <div class="post-buttons">
                                <form method="POST" action="">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <button type="submit" name="action" value="decline" class="btn btn-danger">Decline</button>
                                    <button type="submit" name="action" value="unboost" class="btn btn-secondary">Unboost</button>
                                    <a href="view_product.php?post_id=<?= $post['id'] ?>" class="btn btn-info">View Product</a>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Declined Posts Section -->
            <div id="declinedPosts" style="display:none;">
                <h4>Declined Posts</h4>
                <div class="row">
                    <?php foreach ($declinedPosts as $post): ?>
                    <div class="col-md-4">
                        <div class="post-card">
                            <img src="<?= $post['photo1'] ?>" alt="<?= $post['name'] ?>" class="post-image">
                            <h5><?= $post['name'] ?></h5>
                            <p>Status: Declined</p>
                            <a href="view_product.php?post_id=<?= $post['id'] ?>" class="btn btn-info">View Product</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Boosted Posts Section -->
            <div id="boostedPosts" style="display:none;">
                <h4>Boosted Posts</h4>
                <div class="row">
                    <?php foreach ($boostedPosts as $post): ?>
                    <div class="col-md-4">
                        <div class="post-card">
                            <img src="<?= $post['photo1'] ?>" alt="<?= $post['name'] ?>" class="post-image">
                            <h5><?= $post['name'] ?></h5>
                            <p>Type: <?= $post['type'] ?></p>
                            <a href="view_product.php?post_id=<?= $post['id'] ?>" class="btn btn-info">View Product</a>
                            <div class="post-buttons">
                                <form method="POST" action="">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <button type="submit" name="action" value="unboost" class="btn btn-secondary">Unboost</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Toggle Sections Script -->
    <script>
        function showSection(section) {
            document.getElementById('pendingPosts').style.display = 'none';
            document.getElementById('acceptedPosts').style.display = 'none';
            document.getElementById('declinedPosts').style.display = 'none';
            document.getElementById('boostedPosts').style.display = 'none';

            if (section === 'pending') {
                document.getElementById('pendingPosts').style.display = 'block';
            } else if (section === 'accepted') {
                document.getElementById('acceptedPosts').style.display = 'block';
            } else if (section === 'declined') {
                document.getElementById('declinedPosts').style.display = 'block';
            } else if (section === 'boosted') {
                document.getElementById('boostedPosts').style.display = 'block';
            }
        }
    </script>

</body>
</html>
