<?php

include '../config/db.php'; // Ensure this file contains the correct PDO connection code


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Check if user is approved
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT is_approved FROM users WHERE id = ?");
$stmt->bindParam(1, $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user['is_approved'] != 1) {
    // User is not approved, show a message and wait for OK before redirecting
    echo "<script>
            alert('You are not authorized to post. Please wait for admin approval.');
            window.location.href = '../index.php'; // Redirect after OK is clicked
          </script>";
    exit();
}

// Function to handle file upload
function uploadFile($fileInputName, $targetDir, $allowedTypes) {
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] == UPLOAD_ERR_OK) {
        $fileInfo = pathinfo($_FILES[$fileInputName]['name']);
        $fileExtension = strtolower($fileInfo['extension']);
        if (in_array($fileExtension, $allowedTypes)) {
            $filename = uniqid() . '-' . basename($_FILES[$fileInputName]['name']);
            $filepath = $targetDir . $filename;
            if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $filepath)) {
                return $filename;
            } else {
                return "Error moving uploaded file.";
            }
        } else {
            return "Invalid file type.";
        }
    } elseif (isset($_FILES[$fileInputName]['error'])) {
        return "Upload error code: " . $_FILES[$fileInputName]['error'];
    }
    return "No file uploaded.";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input data
    $name = htmlspecialchars($_POST['name']);
    $shape = htmlspecialchars($_POST['shape']);
    $weight = htmlspecialchars($_POST['weight']);
    $price_per_ct = htmlspecialchars($_POST['price_per_ct']);
    $user_id = $_SESSION['user_id'];

    // File upload handling for photos and video
    $allowedImageTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $allowedVideoTypes = ['mp4', 'avi', 'mov'];

    $photo_certificate = uploadFile('photo_certificate', '../uploads/black_diamond/certificates/', $allowedImageTypes);
    $photo_diamond = uploadFile('photo_diamond', '../uploads/black_diamond/photo/', $allowedImageTypes);
    $video_diamond = uploadFile('video_diamond', '../uploads/black_diamond/video/', $allowedVideoTypes);

    // Debugging file upload issues
    if (strpos($photo_certificate, 'Error') !== false || 
        strpos($photo_diamond, 'Error') !== false || 
        strpos($video_diamond, 'Error') !== false) {
        echo "File upload failed: $photo_certificate, $photo_diamond, $video_diamond";
    } elseif (strpos($photo_certificate, 'Invalid') !== false || 
              strpos($photo_diamond, 'Invalid') !== false || 
              strpos($video_diamond, 'Invalid') !== false) {
        echo "Invalid file type: $photo_certificate, $photo_diamond, $video_diamond";
    } else {
        // Prepare the SQL query
        try {
            $stmt = $conn->prepare("INSERT INTO black_diamonds (name, photo_certificate, photo_diamond, video_diamond, shape, weight, `price/ct`, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bindParam(1, $name);
            $stmt->bindParam(2, $photo_certificate);
            $stmt->bindParam(3, $photo_diamond);
            $stmt->bindParam(4, $video_diamond);
            $stmt->bindParam(5, $shape);
            $stmt->bindParam(6, $weight);
            $stmt->bindParam(7, $price_per_ct);
            $stmt->bindParam(8, $user_id);

            if ($stmt->execute()) {
                echo "<script>alert('Your black diamond has been successfully posted. Please await admin confirmation.');</script>";
                header("Location: my_post.php");
                exit();
            } else {
                echo "Error: " . $stmt->errorInfo()[2];
            }
        } catch (PDOException $e) {
            echo "Database error: " . $e->getMessage();
        }
        $conn = null; // Close the connection
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Post a Black Diamond</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- Favicon -->
  <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <script>
        function toggleShapeInput(value) {
            if (value === 'fancy') {
                document.getElementById('fancy_shapes').style.display = 'block';
            } else {
                document.getElementById('fancy_shapes').style.display = 'none';
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            var shape = document.getElementById('shape').value;
            toggleShapeInput(shape);
        });

        // Update custom file input label
        document.addEventListener('DOMContentLoaded', function() {
            var customFileInputs = document.querySelectorAll('.custom-file-input');
            customFileInputs.forEach(function(input) {
                input.addEventListener('change', function() {
                    var fileName = this.value.split('\\').pop();
                    this.nextElementSibling.classList.add('selected');
                    this.nextElementSibling.innerHTML = fileName;
                });
            });
        });
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
                            <h2 class="h3 font-weight-normal">Post a Black Diamond</h2>
                        </div>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input type="text" id="name" name="name" class="form-control rounded-pill border-0 px-4" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="photo_certificate">Photo of Certificate:</label>
                                <div class="custom-file">
                                    <input type="file" id="photo_certificate" name="photo_certificate" class="custom-file-input" accept=".jpg, .jpeg, .png, .gif" required>
                                    <label class="custom-file-label form-control rounded-pill border-0 px-4" for="photo_certificate">Choose file</label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="photo_diamond">Photo of Diamond:</label>
                                <div class="custom-file">
                                    <input type="file" id="photo_diamond" name="photo_diamond" class="custom-file-input" accept=".jpg, .jpeg, .png, .gif" required>
                                    <label class="custom-file-label form-control rounded-pill border-0 px-4" for="photo_diamond">Choose file</label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="video_diamond">Video of Diamond:</label>
                                <div class="custom-file">
                                    <input type="file" id="video_diamond" name="video_diamond" class="custom-file-input" accept=".mp4, .avi, .mov">
                                    <label class="custom-file-label form-control rounded-pill border-0 px-4" for="video_diamond">Choose file</label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="shape">Shape:</label>
                                <select id="shape" name="shape" required onchange="toggleShapeInput(this.value)" class="form-control rounded-pill border-0 px-4 custom-select">
                                    <option value="round">Round</option>
                                    <option value="fancy">Fancy</option>
                                </select>
                            </div>

                            <div id="fancy_shapes" style="display: none;" class="form-group mb-3">
                                <label for="fancy_shape_select">Select Fancy Shape:</label>
                                <select id="fancy_shape_select" name="fancy_shape_select" class="form-control rounded-pill border-0 px-4 custom-select">
                                    <option value="Oval">Oval</option>
                                    <option value="Princess">Princess</option>
                                    <option value="Emerald">Emerald</option>
                                    <option value="Asscher">Asscher</option>
                                    <option value="Cushion">Cushion</option>
                                    <option value="Radiant">Radiant</option>
                                    <option value="Pear">Pear</option>
                                    <option value="Marquise">Marquise</option>
                                    <option value="Heart">Heart</option>
                                    <option value="Trillion">Trillion</option>
                                    <option value="Baguette">Baguette</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="weight">Weight (in carats):</label>
                                <input type="number" id="weight" name="weight" step="0.01" class="form-control rounded-pill border-0 px-4" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="price_per_ct">Price per Carat:</label>
                                <input type="number" id="price_per_ct" name="price_per_ct" step="0.01" class="form-control rounded-pill border-0 px-4" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block rounded-pill border-0 px-4">Post Black Diamond</button>
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
</body>

</html>
