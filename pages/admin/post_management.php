<?php

// Include database connection
include '../../config/db.php';

// Check if the user is logged in and has the role 'admin'
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redirect if the user is not an admin or not logged in
    header("Location: login.php");
    exit(); // Ensure the script stops executing after the redirect
}


// Function to fetch posts by status
function getPostsByStatus($conn, $status) {
    try {
        $stmt = $conn->prepare('
            (SELECT id, "diamond" AS type, nature AS name, photo_diamond AS photo, video_diamond AS video, weight, clarity, color, user_id, is_approved, boost
            FROM diamond WHERE is_approved = :status)
            UNION ALL
            (SELECT id, "gemstone" AS type, gemstone_name AS name, photo_gemstone AS photo, video_gemstone AS video, weight, cut, color, user_id, is_approved, boost
            FROM gemstone WHERE is_approved = :status)
            UNION ALL
            (SELECT id, "gadgets" AS type, title AS name, photo_gadget AS photo, video_gadget AS video, NULL AS weight, NULL AS clarity, NULL AS color, user_id, is_approved, boost
            FROM gadgets WHERE is_approved = :status)
            UNION ALL
            (SELECT id, "jewelry" AS type, title AS name, photo_jewelry AS photo, NULL AS video, NULL AS weight, NULL AS clarity, NULL AS color, user_id, is_approved, boost
            FROM jewelry WHERE is_approved = :status)
            UNION ALL
            (SELECT id, "watches" AS type, title AS name, photo_watch AS photo, NULL AS video, NULL AS weight, NULL AS clarity, NULL AS color, user_id, is_approved, boost
            FROM watches WHERE is_approved = :status)
            UNION ALL
            (SELECT id, "black_diamonds" AS type, name AS name, photo_diamond AS photo, video_diamond AS video, weight, NULL AS clarity, NULL AS color, user_id, is_approved, boost
            FROM black_diamonds WHERE is_approved = :status)
            ORDER BY id DESC
        ');
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

// Function to fetch boosted posts
function getBoostedPosts($conn) {
    try {
        $stmt = $conn->prepare('
            SELECT id, type, name, photo, video, user_id FROM (
                SELECT id, "diamond" AS type, nature AS name, photo_diamond AS photo, video_diamond AS video, user_id FROM diamond WHERE boost = 1
                UNION ALL
                SELECT id, "gemstone" AS type, gemstone_name AS name, photo_gemstone AS photo, video_gemstone AS video, user_id FROM gemstone WHERE boost = 1
                UNION ALL
                SELECT id, "gadgets" AS type, title AS name, photo_gadget AS photo, video_gadget AS video, user_id FROM gadgets WHERE boost = 1
                UNION ALL
                SELECT id, "jewelry" AS type, title AS name, photo_jewelry AS photo, NULL AS video, user_id FROM jewelry WHERE boost = 1
                UNION ALL
                SELECT id, "watches" AS type, title AS name, photo_watch AS photo, NULL AS video, user_id FROM watches WHERE boost = 1
                UNION ALL
                SELECT id, "black_diamonds" AS type, name AS name, photo_diamond AS photo, video_diamond AS video, user_id FROM black_diamonds WHERE boost = 1
            ) AS boosted_posts
        ');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

// Handle post actions (approve, decline, boost, unboost, delete)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? null;
    $postId = $_POST['post_id'] ?? null;
    $postType = $_POST['post_type'] ?? null;

    if ($action && $postId) {
        try {
            // Handle approve action
            if ($action === 'approve') {
                updatePostStatus($conn, $postId, $postType, 'Accept');
                notifyUser($conn, $postId, $postType, 'approved');

            // Handle decline action
            } elseif ($action === 'decline') {
                // First, decline the post
                updatePostStatus($conn, $postId, $postType, 'Decline');
                // Then, unboost the post
                updatePostBoost($conn, $postId, $postType, 0);
                notifyUser($conn, $postId, $postType, 'declined');
            
            // Handle boost action
            } elseif ($action === 'boost') {
                updatePostBoost($conn, $postId, $postType, 1);
                notifyUser($conn, $postId, $postType, 'boosted');
            
            // Handle unboost action
            } elseif ($action === 'unboost') {
                updatePostBoost($conn, $postId, $postType, 0);
                
            // Handle delete action
            } elseif ($action === 'delete') {
                deletePost($conn, $postId, $postType);
            }

            // Redirect after the action
            header('Location: post_management.php');
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

// Function to update post approval status (for decline/approve)
function updatePostStatus($conn, $postId, $postType, $status) {
    $stmt = $conn->prepare("UPDATE $postType SET is_approved = :status WHERE id = :id");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $postId);
    $stmt->execute();
}

// Function to update post boost status (boost/unboost)
function updatePostBoost($conn, $postId, $postType, $boostStatus) {
    $stmt = $conn->prepare("UPDATE $postType SET boost = :boost WHERE id = :id");
    $stmt->bindParam(':boost', $boostStatus);
    $stmt->bindParam(':id', $postId);
    $stmt->execute();
}

// Function to delete a post
function deletePost($conn, $postId, $postType) {
    $stmt = $conn->prepare("DELETE FROM $postType WHERE id = :id");
    $stmt->bindParam(':id', $postId);
    $stmt->execute();
}

// Function to notify user
function notifyUser($conn, $postId, $postType, $action) {
    $message = "Your $postType post with ID $postId has been $action.";
    $senderId = $_SESSION['user_id'];
    $receiverId = getPostOwner($conn, $postId, $postType);
    insertNotification($conn, $senderId, $receiverId, $message);
}

// Function to get post owner's user_id
function getPostOwner($conn, $postId, $postType) {
    $stmt = $conn->prepare("SELECT user_id FROM $postType WHERE id = :id");
    $stmt->bindParam(':id', $postId);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['user_id'];
}

// Function to insert notification
function insertNotification($conn, $senderId, $receiverId, $message) {
    $stmt = $conn->prepare('INSERT INTO notifications (Sender, Receiver, message) VALUES (:sender_id, :receiver_id, :message)');
    $stmt->bindParam(':sender_id', $senderId);
    $stmt->bindParam(':receiver_id', $receiverId);
    $stmt->bindParam(':message', $message);
    $stmt->execute();
}

// Handle section display based on the URL parameter
$section = $_GET['section'] ?? 'pending'; // Default to 'pending' if no section is specified

// Fetch posts based on the current section
$pendingPosts = getPostsByStatus($conn, 'Pending');
$acceptedPosts = getPostsByStatus($conn, 'Accept');
$declinedPosts = getPostsByStatus($conn, 'Decline');
$boostedPosts = getBoostedPosts($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <style>
        .main-content {
            margin-left: 100px; /* Adjusted to accommodate sidebar */
            padding: 20px;
            width: calc(100% - 220px); /* Subtract sidebar width */
            transition: margin-left 0.5s ease; /* Smooth transition for responsiveness */
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
        /* Adjust for smaller screen sizes (mobile responsiveness) */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                margin: 0;
            }
            .main-content {
                margin-left: 0;
                width: 100%;
            }
        }

        /* Additional CSS if you want to add extra styling */
        .navbar {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .navbar-nav {
            flex-direction: row !important; /* Ensure the items are in a row */
        }

        .nav-item {
            padding-left: 15px;
            padding-right: 15px;
        }

        .backbuttonContainer{
            display: flex;
            justify-content: center;
            align-items: center;
            color: #1887FF !important;
        }

        .backbuttonContainer i{
            width: 30px;
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


@media (max-width: 768px) { /* Media query for larger screens */
    .navbar-nav {
        flex-direction: column !important; /* Change to row layout on larger screens */
    }
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
                            <a class="nav-link active" href="dashboard.php">
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
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

    <!-- Navbar -->
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">

    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">            
    <a class="navbar-brand backbuttonContainer" href="javascript:history.back();">
        <i class="material-icons" style="vertical-align: middle;">arrow_backward</i> Back
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
        <ul class="navbar-nav flex-column"> <!-- Added flex-column for mobile layout -->
            <li class="nav-item">
                <a class="nav-link" href="?section=pending">Pending</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?section=accepted">Accepted</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?section=boosted">Boosted</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?section=declined">Declined</a>
            </li>
        </ul>
    </div>
</nav>


    <!-- Main Content -->
    <div class="main-content">
        <div class="container mt-5">
            <!-- Conditional Rendering of Sections -->
            <div id="pendingPosts" style="<?= $section === 'pending' ? 'display:block;' : 'display:none;' ?>" >
                <h4>Pending Posts</h4>
                <div class="row">
                    <?php foreach ($pendingPosts as $post): ?>
                    <div class="col-md-4">
                        <div class="post-card">
                            <?php if (!empty($post['photo'])): ?>
                                <img src="../../uploads/<?= ($post['type'] === 'black_diamonds' ? 'black_diamond' : ($post['type'] === 'gemstone' ? 'gemstones' : $post['type'])) ?>/photo/<?= htmlspecialchars($post['photo']) ?>" alt="<?= htmlspecialchars($post['name']) ?>" class="post-image">
                            <?php endif; ?>
                            <h5><?= $post['name'] ?></h5>
                            <p>Status: Pending</p>
                            <div class="post-buttons">
                                <form method="POST" action="">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <input type="hidden" name="post_type" value="<?= $post['type'] ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
                                    <button type="submit" name="action" value="decline" class="btn btn-danger">Decline</button>
                                    <button type="submit" name="action" value="delete" class="btn btn-danger">Delete</button>
                                </form>
                                <form method="POST" action="../view_products_info.php">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($post['id']) ?>">
                                    <input type="hidden" name="type" value="<?= htmlspecialchars($post['type']) ?>">
                                    <button type="submit" class="btn btn-info">View Product</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>


            <div id="acceptedPosts" style="<?= $section === 'accepted' ? 'display:block;' : 'display:none;' ?>">
                <h4>Accepted Posts</h4>
                <div class="row">
                    <?php foreach ($acceptedPosts as $post): ?>
                    <div class="col-md-4">
                        <div class="post-card">
                            <img src="../../uploads/<?= ($post['type'] === 'black_diamonds' ? 'black_diamond' : ($post['type'] === 'gemstone' ? 'gemstones' : $post['type'])) ?>/photo/<?= htmlspecialchars($post['photo']) ?>" alt="<?= htmlspecialchars($post['name']) ?>" class="post-image">
                            <h5><?= $post['name'] ?></h5>
                            <p>Status: Accepted</p>
                            <div class="post-buttons">
                                <form method="POST" action="">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <input type="hidden" name="post_type" value="<?= $post['type'] ?>">
                                    <?php if ($post['boost'] == 0): ?>
                                        <button type="submit" name="action" value="boost" class="btn btn-warning">Boost</button>
                                    <?php else: ?>
                                        <button type="submit" name="action" value="unboost" class="btn btn-secondary">Unboost</button>
                                    <?php endif; ?>
                                    <button type="submit" name="action" value="decline" class="btn btn-danger">Decline</button>
                                    <button type="submit" name="action" value="delete" class="btn btn-danger">Delete</button>
                                </form>
                                <form method="POST" action="../view_products_info.php">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($post['id']) ?>">
                                    <input type="hidden" name="type" value="<?= htmlspecialchars($post['type']) ?>">
                                    <button type="submit" class="btn btn-info">View Product</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>


            <div id="boostedPosts" style="<?= $section === 'boosted' ? 'display:block;' : 'display:none;' ?>">
                <h4>Boosted Posts</h4>
                <div class="row">
                    <?php foreach ($boostedPosts as $post): ?>
                    <div class="col-md-4">
                        <div class="post-card">
                            <img src="../../uploads/<?= ($post['type'] === 'black_diamonds' ? 'black_diamond' : ($post['type'] === 'gemstone' ? 'gemstones' : $post['type'])) ?>/photo/<?= htmlspecialchars($post['photo']) ?>" alt="<?= htmlspecialchars($post['name']) ?>" class="post-image">
                            <h5><?= $post['name'] ?></h5>
                            <p>Status: Boosted</p>
                            <div class="post-buttons">
                                <form method="POST" action="">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <input type="hidden" name="post_type" value="<?= $post['type'] ?>">
                                    <button type="submit" name="action" value="unboost" class="btn btn-secondary">Unboost</button>
                                    <button type="submit" name="action" value="delete" class="btn btn-danger">Delete</button>
                                </form>
                                <form method="POST" action="../view_products_info.php">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($post['id']) ?>">
                                    <input type="hidden" name="type" value="<?= htmlspecialchars($post['type']) ?>">
                                    <button type="submit" class="btn btn-info">View Product</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>


            <div id="declinedPosts" style="<?= $section === 'declined' ? 'display:block;' : 'display:none;' ?>">
                <h4>Declined Posts</h4>
                <div class="row">
                    <?php foreach ($declinedPosts as $post): ?>
                    <div class="col-md-4">
                        <div class="post-card">
                            <img src="../../uploads/<?= ($post['type'] === 'black_diamonds' ? 'black_diamond' : ($post['type'] === 'gemstone' ? 'gemstones' : $post['type'])) ?>/photo/<?= htmlspecialchars($post['photo']) ?>" alt="<?= htmlspecialchars($post['name']) ?>" class="post-image">
                            <h5><?= $post['name'] ?></h5>
                            <p>Status: Declined</p>
                            <div class="post-buttons">
                                <form method="POST" action="">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <input type="hidden" name="post_type" value="<?= $post['type'] ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-success">Accept</button>
                                    <button type="submit" name="action" value="delete" class="btn btn-danger">Delete</button>
                                </form>
                                <form method="POST" action="../view_products_info.php">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($post['id']) ?>">
                                    <input type="hidden" name="type" value="<?= htmlspecialchars($post['type']) ?>">
                                    <button type="submit" class="btn btn-info">View Product</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>


        </div>
    </div>

    </main>
    </div>
</div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>