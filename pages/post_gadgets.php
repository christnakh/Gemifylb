<?php
include '../config/db.php'; // Ensure this file contains your PDO connection code


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}


// Function to handle file upload
function uploadFile($fileInputName, $targetDir, $allowedTypes) {
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] == 0) {
        $fileInfo = pathinfo($_FILES[$fileInputName]['name']);
        $fileExtension = strtolower($fileInfo['extension']);
        if (in_array($fileExtension, $allowedTypes)) {
            $filename = uniqid() . '-' . basename($_FILES[$fileInputName]['name']);
            $filepath = $targetDir . $filename;
            if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $filepath)) {
                return $filename;
            } else {
                return "Error uploading file.";
            }
        } else {
            return "Invalid file type.";
        }
    }
    return ''; // Return empty string if file was not uploaded
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input data
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $price = htmlspecialchars($_POST['price']);
    $user_id = $_SESSION['user_id']; // Ensure session is started and user_id is set

    // File upload handling for photos and video
    $allowedImageTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $allowedVideoTypes = ['mp4', 'avi', 'mov'];

    $photo_gadget = uploadFile('photo_gadget', '../uploads/gadgets/photo/', $allowedImageTypes);
    $video_gadget = uploadFile('video_gadget', '../uploads/gadgets/video/', $allowedVideoTypes);

    if ($photo_gadget == "Error uploading file." || $video_gadget == "Error uploading file.") {
        echo "File upload failed.";
    } elseif ($photo_gadget == "Invalid file type." || $video_gadget == "Invalid file type.") {
        echo "Invalid file type.";
    } else {
        // Insert data into the database using PDO prepared statements
        $stmt = $conn->prepare("INSERT INTO gadgets (title, photo_gadget, video_gadget, description, price, user_id) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die('Prepare Error: ' . implode(", ", $conn->errorInfo()));
        }
        $stmt->bindParam(1, $title);
        $stmt->bindParam(2, $photo_gadget);
        $stmt->bindParam(3, $video_gadget);
        $stmt->bindParam(4, $description);
        $stmt->bindParam(5, $price);
        $stmt->bindParam(6, $user_id);

        if ($stmt->execute()) {
            echo "<script>alert('Gadget posted successfully.');</script>";
            header("Location: post_gadgets.php");
            exit();
        } else {
            echo "Error: " . implode(", ", $stmt->errorInfo());
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post a Gadget</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
      <!-- other meta tags and elements -->
    <meta name="apple-mobile-web-app-capable" content="yes">
  <!-- Android -->
    <meta name="mobile-web-app-capable" content="yes">
    <script>
        // Optional: Add any JavaScript functions if needed
    </script>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4 p-3">
        <div class="row justify-content-center align-items-center signup-form-container">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <h2 class="h3 font-weight-normal">Post a Gadget</h2>
                        </div>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                            <div class="form-group mb-3">
                                <label for="title">Title:</label>
                                <input type="text" id="title" name="title" class="form-control rounded-pill border-0 px-4" placeholder="Enter title" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="photo_gadget">Photo of Gadget:</label>
                                <div class="custom-file">
                                    <input type="file" id="photo_gadget" name="photo_gadget" class="custom-file-input" accept=".jpg, .jpeg, .png, .gif" required>
                                    <label class="custom-file-label form-control rounded-pill border-0 px-4" for="photo_gadget">Choose file</label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="video_gadget">Video of Gadget:</label>
                                <div class="custom-file">
                                    <input type="file" id="video_gadget" name="video_gadget" class="custom-file-input" accept=".mp4, .avi, .mov">
                                    <label class="custom-file-label form-control rounded-pill border-0 px-4" for="video_gadget">Choose file</label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">Description:</label>
                                <textarea id="description" name="description" class="form-control rounded-pill border-0 px-4" rows="4" placeholder="Enter description" required></textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label for="price">Price:</label>
                                <input type="text" id="price" name="price" class="form-control rounded-pill border-0 px-4" placeholder="Enter price" required>
                            </div>

                            <button type="submit" class="btn btn-submit mt-4 rounded-pill">Submit</button>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <!-- Script to handle custom file input label -->
    <script>
        // Custom file input label update
   $(document).ready(function() {
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
});

    </script>
</body>
</html>
