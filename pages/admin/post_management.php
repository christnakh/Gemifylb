<?php
session_start();

// Database connection
include '../../config/db.php';

// Fetch posts based on status (pending, accepted, declined)
function fetchPostsByStatus($conn, $status) {
    try {
        // Query to fetch posts based on status
        $stmt = $conn->prepare('
            (SELECT id, "diamond" AS type, nature AS name, photo_diamond AS photo1, video_diamond AS video, weight, clarity, color, user_id, is_approved
            FROM diamond WHERE is_approved = :status)
            UNION ALL
            (SELECT id, "gemstone" AS type, gemstone_name AS name, photo_gemstone AS photo1, video_gemstone AS video, weight, cut, color, user_id, is_approved
            FROM gemstone WHERE is_approved = :status)
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

// Fetch pending, accepted, and declined posts
$pendingPosts = fetchPostsByStatus($conn, 'pending');
$acceptedPosts = fetchPostsByStatus($conn, 'accept');
$declinedPosts = fetchPostsByStatus($conn, 'decline');

// Handle post approval or decline
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && isset($_POST['post_id'])) {
        $action = $_POST['action'];
        $post_id = $_POST['post_id'];

        try {
            if ($action === 'approve') {
                // Update diamond or gemstone table based on post type
                $table = ($_POST['post_type'] === 'diamond') ? 'diamond' : 'gemstone';
                $stmt = $conn->prepare("UPDATE $table SET is_approved = 'accept' WHERE id = :id");
                $stmt->bindParam(':id', $post_id);
                $stmt->execute();

                // Notify user of approval
                $message = "Your " . ucfirst($_POST['post_type']) . " post with ID $post_id has been approved.";
                $sender_id = $_SESSION['user_id'];
                $receiver_id = fetchPostUserId($conn, $post_id); // Function to fetch post owner's user_id
                insertNotification($conn, $sender_id, $receiver_id, $message);

            } elseif ($action === 'decline') {
                // Update diamond or gemstone table based on post type
                $table = ($_POST['post_type'] === 'diamond') ? 'diamond' : 'gemstone';
                $stmt = $conn->prepare("UPDATE $table SET is_approved = 'decline', decline_reason = :reason WHERE id = :id");
                $stmt->bindParam(':reason', $_POST['decline_reason']);
                $stmt->bindParam(':id', $post_id);
                $stmt->execute();

                // Notify user of decline
                $message = "Your " . ucfirst($_POST['post_type']) . " post with ID $post_id has been declined. Reason: " . $_POST['decline_reason'];
                $sender_id = $_SESSION['user_id'];
                $receiver_id = fetchPostUserId($conn, $post_id); // Function to fetch post owner's user_id
                insertNotification($conn, $sender_id, $receiver_id, $message);
            }

            // Redirect to avoid form resubmission on refresh
            header('Location: post_management.php');
            exit();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

// Function to fetch user_id of post owner
function fetchPostUserId($conn, $post_id) {
    // Implement your logic to fetch the user_id based on post_id
    // Example:
    try {
        // Determine table based on post_id (diamond or gemstone)
        $table = (strpos($post_id, 'D') === 0) ? 'diamond' : 'gemstone';
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
            background-color: #f8f9fa;
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
            padding: 10px 15px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #f8f9fa;
        }
        .table-warning {
            background-color: #fff3cd;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .post-details {
            margin-top: 20px;
        }
        .post-details img {
            max-width: 100%;
            height: auto;
        }
        .video-container {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio (divide 9 by 16 = 0.5625) */
            overflow: hidden;
            margin-top: 20px;
        }
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
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
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="header pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Post Management</h1>
                </div>

                <!-- Pending Posts -->
                <h2 class="mt-3 mb-3">Pending Posts</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Photo</th>
                                <th>Video</th>
                                <th>Weight</th>
                                <th>Clarity / Cut</th>
                                <th>Color</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingPosts as $post): ?>
                            <tr>
                                <td><?= ucfirst($post['type']) ?></td>
                                <td><?= $post['name'] ?></td>
                                <td><img src="<?= $post['photo1'] ?>" alt="<?= $post['name'] ?>" width="100"></td>
                                <td>
                                    <?php if (!empty($post['video'])): ?>
                                    <div class="video-container">
                                        <iframe src="<?= $post['video'] ?>" frameborder="0" allowfullscreen></iframe>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= $post['weight'] ?></td>
                                <td><?= isset($post['clarity']) ? $post['clarity'] : $post['cut'] ?></td>
                                <td><?= $post['color'] ?></td>
                                <td>
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                        <input type="hidden" name="post_type" value="<?= $post['type'] ?>">
                                        <button type="submit" name="action" value="approve" class="btn btn-success mr-2">Approve</button>
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#declineModal<?= $post['id'] ?>">Decline</button>
                                    </form>
                                    <!-- Decline Modal -->
                                    <div class="modal fade" id="declineModal<?= $post['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="declineModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="declineModalLabel">Decline Post</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                                        <input type="hidden" name="post_type" value="<?= $post['type'] ?>">
                                                        <div class="form-group">
                                                            <label for="declineReason">Reason for Decline</label>
                                                            <textarea class="form-control" id="declineReason" name="decline_reason" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        <button type="submit" name="action" value="decline" class="btn btn-danger">Decline</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Accepted Posts -->
                <h2 class="mt-5 mb-3">Accepted Posts</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Photo</th>
                                <th>Video</th>
                                <th>Weight</th>
                                <th>Clarity / Cut</th>
                                <th>Color</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($acceptedPosts as $post): ?>
                            <tr>
                                <td><?= ucfirst($post['type']) ?></td>
                                <td><?= $post['name'] ?></td>
                                <td><img src="<?= $post['photo1'] ?>" alt="<?= $post['name'] ?>" width="100"></td>
                                <td>
                                    <?php if (!empty($post['video'])): ?>
                                    <div class="video-container">
                                        <iframe src="<?= $post['video'] ?>" frameborder="0" allowfullscreen></iframe>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= $post['weight'] ?></td>
                                <td><?= isset($post['clarity']) ? $post['clarity'] : $post['cut'] ?></td>
                                <td><?= $post['color'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Declined Posts -->
                <h2 class="mt-5 mb-3">Declined Posts</h2>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Photo</th>
                                <th>Video</th>
                                <th>Weight</th>
                                <th>Clarity / Cut</th>
                                <th>Color</th>
                                <th>Reason for Decline</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($declinedPosts as $post): ?>
                            <tr>
                                <td><?= ucfirst($post['type']) ?></td>
                                <td><?= $post['name'] ?></td>
                                <td><img src="<?= $post['photo1'] ?>" alt="<?= $post['name'] ?>" width="100"></td>
                                <td>
                                    <?php if (!empty($post['video'])): ?>
                                    <div class="video-container">
                                        <iframe src="<?= $post['video'] ?>" frameborder="0" allowfullscreen></iframe>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td><?= $post['weight'] ?></td>
                                <td><?= isset($post['clarity']) ? $post['clarity'] : $post['cut'] ?></td>
                                <td><?= $post['color'] ?></td>
                                <td><?= $post['decline_reason'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
