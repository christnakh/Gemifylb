<?php

include '../config/db.php'; // Ensure the correct path to your database configuration

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to handle file upload
function uploadFile($fileInputName, $targetDir) {
    if (isset($_FILES[$fileInputName])) {
        $file = $_FILES[$fileInputName];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $filename = uniqid() . '-' . basename($file['name']);
            $filepath = $targetDir . $filename;
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                return $filename;
            } else {
                return "Error moving uploaded file.";
            }
        } else {
            return "File upload error: " . $file['error'];
        }
    }
    return 'No file uploaded.';
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input data
    $gemstone_name = $_POST['gemstone_name'];
    $type = $_POST['type'];
    $weight = $_POST['weight'];
    $cut = $_POST['cut'];
    $shape = $_POST['shape'];
    if ($shape === 'Other') {
        $shape = $_POST['other_shape_input']; // Use other_shape_input if shape is "Other"
    }
    $color = $_POST['color'];
    $certificate_type = $_POST['certificate_type'];
    if ($certificate_type === 'Other') {
        $certificate = $_POST['other_certificate_input']; // Use other_certificate_input if certificate_type is "Other"
    } else {
        $certificate = $certificate_type; // Use selected certificate_type
    }
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user_id'];

    // File upload handling for photos and video
    $photo_certificate = uploadFile('photo_certificate', '../uploads/gemstones/certificates/');
    $photo_gemstone = uploadFile('photo_gemstone', '../uploads/gemstones/photo/');
    $video_gemstone = uploadFile('video_gemstone', '../uploads/gemstones/video/');

    // Insert data into the database using named placeholders
    $stmt = $conn->prepare("INSERT INTO gemstone (gemstone_name, photo_certificate, photo_gemstone, video_gemstone, weight, cut, shape, color, type, certificate, comment, user_id) 
                        VALUES (:gemstone_name, :photo_certificate, :photo_gemstone, :video_gemstone, :weight, :cut, :shape, :color, :type, :certificate, :comment, :user_id)");
    if (!$stmt) {
        die('Prepare Error: ' . $conn->errorInfo()[2]);
    }
    $stmt->bindParam(':gemstone_name', $gemstone_name);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':photo_certificate', $photo_certificate);
    $stmt->bindParam(':photo_gemstone', $photo_gemstone);
    $stmt->bindParam(':video_gemstone', $video_gemstone);
    $stmt->bindParam(':weight', $weight);
    $stmt->bindParam(':cut', $cut);
    $stmt->bindParam(':shape', $shape);
    $stmt->bindParam(':color', $color);
    $stmt->bindParam(':certificate', $certificate);
    $stmt->bindParam(':comment', $comment);
    $stmt->bindParam(':user_id', $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('Your gemstone has been successfully posted. Please await admin confirmation.');</script>";
        header("Location: my_post.php");
        exit();
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }

    $stmt->closeCursor();
    $conn = null; // Close the connection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Post a Gemstone</title>
    <!-- Include any necessary CSS or JavaScript files -->
    <link rel="stylesheet" href="../css/styles.css">
    <script src="../js/post_gemstones.js"></script>
        <!-- Favicon -->
  <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
      <!-- other meta tags and elements -->
    <meta name="apple-mobile-web-app-capable" content="yes">
  <!-- Android -->
    <meta name="mobile-web-app-capable" content="yes">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container mt-4 p-4">
    <div class="row justify-content-center align-items-center signup-form-container">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <h2 class="h3 font-weight-normal">Post a Gemstone</h2>
                    </div>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="gemstone_name">Gemstone Name:</label>
                            <input type="text" id="gemstone_name" name="gemstone_name" class="form-control rounded-pill border-0 px-4" required><br>
                        </div>

                        <div class="form-group mb-3">
                            <label for="type">Type:</label>
                            <select id="type" name="type" required class="form-control rounded-pill border-0 px-4 custom-select" onchange="toggleCertificateType()">
                                <option value="Natural">Natural</option>
                                <option value="Synthetic">Synthetic</option>
                            </select>
                        </div>



                        <div class="form-group mb-3">
                            <label for="photo_certificate">Photo of Certificate:</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="photo_certificate" name="photo_certificate" required onchange="updateFileLabel('photo_certificate')">
                                <label class="custom-file-label form-control rounded-pill border-0 px-4" for="photo_certificate">Choose file</label>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="photo_gemstone">Photo of Gemstone:</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="photo_gemstone" name="photo_gemstone" required onchange="updateFileLabel('photo_gemstone')">
                                <label class="custom-file-label form-control rounded-pill border-0 px-4" for="photo_gemstone">Choose file</label>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="video_gemstone">Video of Gemstone:</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="video_gemstone" name="video_gemstone" onchange="updateFileLabel('video_gemstone')">
                                <label class="custom-file-label form-control rounded-pill border-0 px-4" for="video_gemstone">Choose file</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="weight">Weight (carats):</label>
                            <input type="number" step="0.01" id="weight" name="weight" class="form-control rounded-pill border-0 px-4" required><br>
                        </div>

                        <div class="form-group">
                            <label for="cut">Cut:</label>
                            <input type="text" id="cut" name="cut" class="form-control rounded-pill border-0 px-4" required><br>
                        </div>

                        <div class="form-group mb-3">
                            <label for="shape">Shape:</label>
                            <select id="shape" name="shape" required onchange="toggleShapeInput(this.value)" class="form-control rounded-pill border-0 px-4 custom-select">
                                <option value="Round">Round</option>
                                <option value="Oval">Oval</option>
                                <option value="Princess">Princess</option>
                                <option value="Emerald">Emerald</option>
                                <option value="Asscher">Asscher</option>
                                <option value="Marquise">Marquise</option>
                                <option value="Pear">Pear</option>
                                <option value="Cushion">Cushion</option>
                                <option value="Radiant">Radiant</option>
                                <option value="Heart">Heart</option>
                                <option value="Trillion">Trillion</option>
                                <option value="Baguette">Baguette</option>
                                <option value="Cabochon">Cabochon</option>
                                <option value="Other">Other</option>
                            </select>
                            <div id="other_shape" style="display: none;" class="form-group mt-3">
                                <label for="other_shape_input">Specify Shape:</label>
                                <input type="text" id="other_shape_input" name="other_shape_input" class="form-control rounded-pill border-0 px-4"><br>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="color">Color:</label>
                            <input type="text" id="color" name="color" class="form-control rounded-pill border-0 px-4" required><br>
                        </div>

                        <div class="form-group mb-3" id="certificate_type_section" style="display: none;">
                        <label for="certificate_type">Certificate Type:</label>
                            <select id="certificate_type" name="certificate_type" class="form-control rounded-pill border-0 px-4 custom-select" onchange="toggleCertificateInput(this.value)">
                                <option value="SSEF">SSEF</option>
                                <option value="Gübelin">Gübelin</option>
                                <option value="GRS">GRS</option>
                                <option value="Other">Other</option>
                            </select>
                            <div id="other_certificate" style="display: none;" class="form-group mt-3">
                                <label for="other_certificate_input">Specify Certificate Type:</label>
                                <input type="text" id="other_certificate_input" name="other_certificate_input" class="form-control rounded-pill border-0 px-4">
                            </div>
                        </div>



                        <div class="form-group">
                            <label for="comment">Additional Comments:</label>
                            <textarea id="comment" name="comment" class="form-control rounded-pill border-0 px-4" rows="4"></textarea><br>
                        </div>

                        <button type="submit" class="btn btn-submit rounded-pill">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
function toggleCertificateType() {
    var type = document.getElementById('type').value;
    var certificateSection = document.getElementById('certificate_type_section');
    
    if (type === 'Natural') {
        certificateSection.style.display = 'block';
    } else {
        certificateSection.style.display = 'none';
    }
}

function updateFileLabel(inputId) {
    var fileInput = document.getElementById(inputId);
    var fileLabel = fileInput.nextElementSibling;
    if (fileInput.files.length > 0) {
        fileLabel.textContent = fileInput.files[0].name;
    } else {
        fileLabel.textContent = 'Choose file';
    }
}

function toggleShapeInput(value) {
    document.getElementById('other_shape').style.display = (value === 'Other') ? 'block' : 'none';
}

function toggleCertificateInput(value) {
    document.getElementById('other_certificate').style.display = (value === 'Other') ? 'block' : 'none';
}


document.addEventListener("DOMContentLoaded", function() {
    toggleCertificateType();
});
</script>


</body>
</html>

