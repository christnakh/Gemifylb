<?php
// Database connection
include '../../config/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);


// Check if the user is logged in and has the role 'admin'
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redirect if the user is not an admin or not logged in
    header("Location: login.php");
    exit(); // Ensure the script stops executing after the redirect
}

$allowed_tables = ['diamond', 'gemstone', 'gadgets', 'jewelry', 'watches', 'black_diamonds'];
if (!in_array($table, $allowed_tables)) {
    die("Invalid table name.");
}


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
            (SELECT id, "watches" AS type, title AS name, photo_watch AS photo1, NULL AS video, NULL AS weight, NULL AS clarity, NULL AS color, user_id, is_approved
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && isset($_POST['post_id']) && isset($_POST['post_type'])) {
        $action = $_POST['action'];
        $post_id = $_POST['post_id'];
        $table = $_POST['post_type']; // This line should be here to assign a value to $table

        if (!in_array($table, $allowed_tables)) {
            die("Invalid table name.");
        }

        try {
            if ($action === 'approve') {
                $query = "UPDATE $table SET is_approved = 'Accept' WHERE id = :id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':id', $post_id);
                $stmt->execute();

                // Notify user of approval
                $message = "Your " . ucfirst($table) . " post with ID $post_id has been approved.";
                $sender_id = $_SESSION['user_id'];
                $receiver_id = fetchPostUserId($conn, $post_id);
                insertNotification($conn, $sender_id, $receiver_id, $message);

            } elseif ($action === 'decline') {
                $query = "UPDATE $table SET is_approved = 'Decline' WHERE id = :id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':id', $post_id);
                $stmt->execute();

                // Notify user of decline
                $message = "Your " . ucfirst($table) . " post with ID $post_id has been declined.";
                $sender_id = $_SESSION['user_id'];
                $receiver_id = fetchPostUserId($conn, $post_id);
                insertNotification($conn, $sender_id, $receiver_id, $message);

            } elseif ($action === 'boost') {
                // Logic to boost the post
                $query = "UPDATE $table SET boost = 1 WHERE id = :id";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':id', $post_id);
                $stmt->execute();

                // Notify user of boosting
                $message = "Your " . ucfirst($table) . " post with ID $post_id has been boosted.";
                $sender_id = $_SESSION['user_id'];
                $receiver_id = fetchPostUserId($conn, $post_id);
                insertNotification($conn, $sender_id, $receiver_id, $message);
            } elseif ($action === 'unboost') {
                // Logic to unboost the post
                $query = "UPDATE $table SET boost = 0 WHERE id = :id";
                $stmt = $conn->prepare($query);
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
    font-family: Arial, sans-serif;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }

        .sidebar-sticky {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .nav-link.active {
            color: #007bff;
        }

        .nav-link {
            font-size: 1.1rem;
            padding: 15px;
        }

        .nav-link i {
            margin-right: 10px;
        }

        .header {
            margin-bottom: 20px;
        }

        .container .row .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            padding: 20px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #f8f9fa;
        }

    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <h4 class="text-center my-4">Admin Panel</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="user_management.php">
                                <i class="fas fa-users"></i> User Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="post_management.php">
                                <i class="fas fa-newspaper"></i> Post Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="rapaport_management.php">
                                <i class="fas fa-file-alt"></i> Rapaport Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact_us_management.php">
                                <i class="fas fa-envelope"></i> Contact Us Management
                            </a>
                        </li>
                          <li class="nav-item">
                            <a class="nav-link" href="../logout.php">
                                <i class="fas fa-envelope"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <header class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                </header>

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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
