<?php
session_start();
include '../config/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$user_id = $_SESSION['user_id'];

// Fetch posts from all tables
$query = $conn->prepare(
    "SELECT id, 'diamond' AS type, nature AS name, photo_certificate, photo_diamond, video_diamond, shape, weight, NULL AS clarity, NULL AS color, cut_type, fluorescence_type, discount_type, is_approved, is_active, boost 
     FROM diamond WHERE user_id = :user_id
    UNION ALL
    SELECT id, 'gemstone' AS type, gemstone_name AS name, photo_certificate, photo_gemstone AS photo_diamond, video_gemstone AS video_diamond, shape, weight, NULL AS clarity, color, cut AS cut_type, NULL AS fluorescence_type, NULL AS discount_type, is_approved, is_active, boost 
    FROM gemstone WHERE user_id = :user_id
    UNION ALL
    SELECT id, 'black_diamonds' AS type, name, photo_certificate, photo_diamond, video_diamond, shape, weight, NULL AS clarity, NULL AS color, NULL AS cut_type, NULL AS fluorescence_type, NULL AS discount_type, is_approved, is_active, boost 
    FROM black_diamonds WHERE user_id = :user_id
    UNION ALL
    SELECT id, 'gadgets' AS type, title AS name, NULL AS photo_certificate, photo_gadget AS photo_diamond, video_gadget AS video_diamond, NULL AS shape, NULL AS weight, NULL AS clarity, NULL AS color, NULL AS cut_type, NULL AS fluorescence_type, NULL AS discount_type, is_approved, is_active, boost 
    FROM gadgets WHERE user_id = :user_id
    UNION ALL
    SELECT id, 'jewelry' AS type, title AS name, photo_certificate, photo_jewelry AS photo_diamond, video AS video_diamond, NULL AS shape, NULL AS weight, NULL AS clarity, NULL AS color, NULL AS cut_type, NULL AS fluorescence_type, NULL AS discount_type, is_approved, is_active, boost 
    FROM jewelry WHERE user_id = :user_id
    UNION ALL
    SELECT id, 'watches' AS type, title AS name, photo_certificate, photo_watch AS photo_diamond, video AS video_diamond, NULL AS shape, NULL AS weight, NULL AS clarity, NULL AS color, NULL AS cut_type, NULL AS fluorescence_type, NULL AS discount_type, is_approved, is_active, boost 
    FROM watches WHERE user_id = :user_id"
);

$query->execute(['user_id' => $user_id]);
$posts = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Posts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link href="https://fonts.googleapis.com/css2?family=Morina:wght@400;700&display=swap" rel="stylesheet">
    <!-- <link rel="stylesheet" href="../css/global.css"> -->
    <link rel="stylesheet" href="../css/my_post.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container my-5">
    <h1 class="text-center mb-4">Your Posts</h1>
    <div class="row">
        <?php foreach ($posts as $index => $post): ?>
            <div class="col-md-6 mb-4">
                <div class="post p-3 border rounded shadow-sm">
                    <h2 class="text-center mb-3"><?php echo htmlspecialchars($post['name']); ?></h2>
                    <hr>
                    <div class="post-media">
                        <div class="image-slider">
                            <div class="slider-container">
                                <?php if (!empty($post['photo_certificate'])) : ?>
                                    <img src="<?php echo htmlspecialchars("../uploads/" . $post['type'] . "/certificates/" . $post['photo_certificate']); ?>" alt="Certificate Image">
                                <?php endif; ?>
                                <?php if (!empty($post['photo_diamond'])) : ?>
                                    <img src="<?php echo htmlspecialchars("../uploads/" . $post['type'] . "/photo/" . $post['photo_diamond']); ?>" alt="Item Image">
                                <?php endif; ?>
                                <?php if (!empty($post['video_diamond'])) : ?>
                                    <video controls>
                                        <source src="<?php echo htmlspecialchars("../uploads/" . $post['type'] . "/video/" . $post['video_diamond']); ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                <?php endif; ?>
                            </div>
                            <div class="slider-nav">
                                <button class="prev">&lt;</button>
                                <div class="slider-dots"></div>
                                <button class="next">&gt;</button>
                            </div>
                        </div>
                    </div>

                    <div class="row text-center">
                        <div class="col-12 col-md-6">
                            <p><strong>Type:</strong> <?php echo htmlspecialchars(ucfirst($post['type'])); ?></p>
                        </div>
                    </div>

                    <p class="text-center">
                        <strong>Status:</strong> 
                        <?php 
                        if ($post['is_approved'] === 'Pending') {
                            echo '<span class="badge bg-warning text-dark">Pending</span>';
                        } elseif ($post['is_approved'] === 'Accept') {
                            echo '<span class="badge bg-success">Posted</span>';
                        } else {
                            echo '<span class="badge bg-danger">Declined</span>';
                        }
                        ?>
                        <br>
                        <strong>Activation:</strong> 
                        <?php echo $post['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'; ?>
                        <br>
                        <strong>Boosted:</strong> 
                        <?php echo $post['boost'] ? '<span class="badge bg-primary">Yes</span>' : '<span class="badge bg-secondary">No</span>'; ?>
                    </p>

                    <div class="post-actions mb-3 d-flex justify-content-center">
                        <form action="edit_post.php" method="post" class="mx-1 mb-3">
                            <input type="hidden" name="product_id" value="<?php echo $post['id']; ?>">
                            <input type="hidden" name="product_type" value="<?php echo $post['type']; ?>">
                            <button type="submit" class="btn btn-primary btn-sm">Edit</button>
                        </form>

                        <form action="delete_post.php" method="post" class="mx-1 mb-3" onsubmit="return confirm('Are you sure you want to delete this post?');">
                            <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                            <input type="hidden" name="type" value="<?php echo $post['type']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>

                        <form action="toggle_status.php" method="post" class="mx-1 mb-3">
                            <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                            <input type="hidden" name="type" value="<?php echo $post['type']; ?>">
                            <input type="hidden" name="is_active" value="<?php echo $post['is_active']; ?>">
                            <button type="submit" class="btn btn-success btn-sm" style="color: white;">
                                <?php echo $post['is_active'] ? 'Deactivate' : 'Activate'; ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php if ($index % 2 != 0 && $index != count($posts) - 1): ?>
                </div><div class="row">
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sliders = document.querySelectorAll('.image-slider');
        sliders.forEach(slider => {
            const slides = slider.querySelectorAll('.slider-container img, .slider-container video');
            const prevButton = slider.querySelector('.prev');
            const nextButton = slider.querySelector('.next');
            const dotsContainer = slider.querySelector('.slider-dots');
            let currentIndex = 0;

            function showSlide(index) {
                slides.forEach(slide => slide.style.display = 'none');
                slides[index].style.display = 'block';
                dotsContainer.querySelectorAll('span').forEach(dot => dot.classList.remove('active'));
                dotsContainer.querySelectorAll('span')[index].classList.add('active');
            }

            slides.forEach((slide, i) => {
                const dot = document.createElement('span');
                dot.classList.add('dot');
                if (i === 0) dot.classList.add('active');
                dot.addEventListener('click', () => {
                    currentIndex = i;
                    showSlide(currentIndex);
                });
                dotsContainer.appendChild(dot);
            });

            showSlide(currentIndex);

            prevButton.addEventListener('click', () => {
                currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                showSlide(currentIndex);
            });

            nextButton.addEventListener('click', () => {
                currentIndex = (currentIndex + 1) % slides.length;
                showSlide(currentIndex);
            });
        });
    });
</script>
</body>
</html>
