<?php
session_start();
include '../config/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page
    exit();
}

// Get user information from the database
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found!";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $role = $_POST['role'];

    // Directories for file uploads
    $profile_picture_dir = '../uploads/user/profile_picture/';
    $passport_photo_dir = '../uploads/user/passport_photo/';
    $front_id_photo_dir = '../uploads/user/front_id_photo/';
    $back_id_photo_dir = '../uploads/user/back_id_photo/';
    $business_certificate_dir = '../uploads/user/business_certificate/';

    // Process profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $profile_picture_filename = uniqid() . '-' . basename($_FILES['profile_picture']['name']);
        $profile_picture_filepath = $profile_picture_dir . $profile_picture_filename;
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture_filepath);
    } else {
        $profile_picture_filename = $user['profile_picture'];
    }

    // Process passport photo upload
    if (isset($_FILES['passport_photo']) && $_FILES['passport_photo']['error'] == 0) {
        $passport_photo_filename = uniqid() . '-' . basename($_FILES['passport_photo']['name']);
        $passport_photo_filepath = $passport_photo_dir . $passport_photo_filename;
        move_uploaded_file($_FILES['passport_photo']['tmp_name'], $passport_photo_filepath);
    } else {
        $passport_photo_filename = $user['passport_photo'];
    }

    // Process front ID photo upload
    if (isset($_FILES['front_id_photo']) && $_FILES['front_id_photo']['error'] == 0) {
        $front_id_photo_filename = uniqid() . '-' . basename($_FILES['front_id_photo']['name']);
        $front_id_photo_filepath = $front_id_photo_dir . $front_id_photo_filename;
        move_uploaded_file($_FILES['front_id_photo']['tmp_name'], $front_id_photo_filepath);
    } else {
        $front_id_photo_filename = $user['front_id_photo'];
    }

    // Process back ID photo upload
    if (isset($_FILES['back_id_photo']) && $_FILES['back_id_photo']['error'] == 0) {
        $back_id_photo_filename = uniqid() . '-' . basename($_FILES['back_id_photo']['name']);
        $back_id_photo_filepath = $back_id_photo_dir . $back_id_photo_filename;
        move_uploaded_file($_FILES['back_id_photo']['tmp_name'], $back_id_photo_filepath);
    } else {
        $back_id_photo_filename = $user['back_id_photo'];
    }

    // Process business certificate upload if role is 'business'
    if ($role == 'business' && isset($_FILES['business_certificate']) && $_FILES['business_certificate']['error'] == 0) {
        $business_certificate_filename = uniqid() . '-' . basename($_FILES['business_certificate']['name']);
        $business_certificate_filepath = $business_certificate_dir . $business_certificate_filename;
        move_uploaded_file($_FILES['business_certificate']['tmp_name'], $business_certificate_filepath);
    } else {
        $business_certificate_filename = $user['business_certificate'];
    }

    try {
        $stmt = $conn->prepare("UPDATE users SET email = ?, full_name = ?, profile_picture = ?, passport_photo = ?, front_id_photo = ?, back_id_photo = ?, role = ?, business_certificate = ? WHERE id = ?");
        $stmt->execute([$email, $full_name, $profile_picture_filename, $passport_photo_filename, $front_id_photo_filename, $back_id_photo_filename, $role, $business_certificate_filename, $user_id]);

        echo "Profile updated successfully!";
        header("location:profile.php");
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // integrity constraint violation (duplicate email)
            echo "Email already in use!";
        } else {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" type="text/css" href="../css/global.css">
      <!-- other meta tags and elements -->
    <meta name="apple-mobile-web-app-capable" content="yes">
  <!-- Android -->
    <meta name="mobile-web-app-capable" content="yes">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4 p-3">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center">Profile</h2>
                        <form method="post" action="profile.php" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" name="email" id="email" class="form-control rounded-pill" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name:</label>
                                <input type="text" name="full_name" id="full_name" class="form-control rounded-pill" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="role" class="form-label">Role:</label>
                                <select name="role" id="role" class="form-select rounded-pill" required>
                                    <option value="personal" <?php if ($user['role'] == 'personal') echo 'selected'; ?>>Personal</option>
                                    <option value="business" <?php if ($user['role'] == 'business') echo 'selected'; ?>>Business</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="profile_picture" class="form-label">Profile Picture:</label>
                                <input type="file" name="profile_picture" id="profile_picture" class="form-control rounded-pill">
                                <?php if ($user['profile_picture']) : ?>
                                    <img src="../uploads/user/profile_picture/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="img-thumbnail mt-2" width="100">
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="passport_photo" class="form-label">Passport Photo:</label>
                                <input type="file" name="passport_photo" id="passport_photo" class="form-control rounded-pill">
                                <?php if ($user['passport_photo']) : ?>
                                    <img src="../uploads/user/passport_photo/<?php echo htmlspecialchars($user['passport_photo']); ?>" alt="Passport Photo" class="img-thumbnail mt-2" width="100">
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="front_id_photo" class="form-label">Front ID Photo:</label>
                                <input type="file" name="front_id_photo" id="front_id_photo" class="form-control rounded-pill">
                                <?php if ($user['front_id_photo']) : ?>
                                    <img src="../uploads/user/front_id_photo/<?php echo htmlspecialchars($user['front_id_photo']); ?>" alt="Front ID Photo" class="img-thumbnail mt-2" width="100">
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="back_id_photo" class="form-label">Back ID Photo:</label>
                                <input type="file" name="back_id_photo" id="back_id_photo" class="form-control rounded-pill">
                                <?php if ($user['back_id_photo']) : ?>
                                    <img src="../uploads/user/back_id_photo/<?php echo htmlspecialchars($user['back_id_photo']); ?>" alt="Back ID Photo" class="img-thumbnail mt-2" width="100">
                                <?php endif; ?>
                            </div>

                            <?php if ($user['role'] == 'business') : ?>
                                <div class="mb-3">
                                    <label for="business_certificate" class="form-label">Business Certificate:</label>
                                    <input type="file" name="business_certificate" id="business_certificate" class="form-control rounded-pill">
                                    <?php if ($user['business_certificate']) : ?>
                                        <img src="../uploads/user/business_certificate/<?php echo htmlspecialchars($user['business_certificate']); ?>" alt="Business Certificate" class="img-thumbnail mt-2" width="100">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <button type="submit" class="btn btn-submit">Update Profile</button>
                        </form>

                        <div class="mt-4">
                            <h3>Approval Status</h3>
                            <?php if ($user['is_approved']) : ?>
                                <p>Your account is approved.</p>
                            <?php else : ?>
                                <p>Your account is not approved yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
