<?php
include '../config/db.php'; // Assuming this file contains your PDO connection code


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
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
    $nature = htmlspecialchars($_POST['nature']);
    $shape = htmlspecialchars($_POST['shape']);
    if ($shape === 'other') {
        $shape = htmlspecialchars($_POST['other_shape_input']);
    }
    $certificate_type = htmlspecialchars($_POST['certificate_type']);
    if ($certificate_type === 'other') {
        $certificate = htmlspecialchars($_POST['other_certificate_input']);
    } else {
        $certificate = $certificate_type;
    }
    $weight = htmlspecialchars($_POST['weight']);
    $clarity = htmlspecialchars($_POST['clarity']);
    $color_type = htmlspecialchars($_POST['color_type']);
    if ($color_type === 'white') {
        $color = "White " . htmlspecialchars($_POST['color_white_select']);
    } else {
        $color = "Fancy " . htmlspecialchars($_POST['color_fancy_input']);
    }
    $cut_type = htmlspecialchars($_POST['cut_type']);
    $fluorescence_type = htmlspecialchars($_POST['fluorescence_type']);
    $discount_type = htmlspecialchars($_POST['discount_type']);
    $user_id = $_SESSION['user_id'];

    // File upload handling for photos and video
    $allowedImageTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $allowedVideoTypes = ['mp4', 'avi', 'mov'];

    $photo_certificate = uploadFile('photo_certificate', '../uploads/diamond/certificates/', $allowedImageTypes);
    $photo_diamond = uploadFile('photo_diamond', '../uploads/diamond/photo/', $allowedImageTypes);
    $video_diamond = uploadFile('video_diamond', '../uploads/diamond/video/', $allowedVideoTypes);

    if (strpos($photo_certificate, 'Error') !== false || 
        strpos($photo_diamond, 'Error') !== false || 
        strpos($video_diamond, 'Error') !== false) {
        echo "File upload failed: $photo_certificate, $photo_diamond, $video_diamond";
    } elseif (strpos($photo_certificate, 'Invalid') !== false || 
              strpos($photo_diamond, 'Invalid') !== false || 
              strpos($video_diamond, 'Invalid') !== false) {
        echo "Invalid file type: $photo_certificate, $photo_diamond, $video_diamond";
    } else {
        // Insert data into diamond table
        $stmt = $conn->prepare("INSERT INTO diamond (nature, photo_certificate, photo_diamond, video_diamond, shape, certificate, weight, clarity, color, cut_type, fluorescence_type, discount_type, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bindParam(1, $nature);
        $stmt->bindParam(2, $photo_certificate);
        $stmt->bindParam(3, $photo_diamond);
        $stmt->bindParam(4, $video_diamond);
        $stmt->bindParam(5, $shape);
        $stmt->bindParam(6, $certificate);
        $stmt->bindParam(7, $weight);
        $stmt->bindParam(8, $clarity);
        $stmt->bindParam(9, $color);
        $stmt->bindParam(10, $cut_type);
        $stmt->bindParam(11, $fluorescence_type);
        $stmt->bindParam(12, $discount_type);
        $stmt->bindParam(13, $user_id);

        if ($stmt->execute()) {
            echo "<script>alert('Your diamond has been successfully posted. Please await admin confirmation.');</script>";
                header("Location: my_post.php");
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }
        $conn = null; // Close the connection
    }
}

?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Post a Diamond</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script>
    function toggleShapeInput(value) {
        if (value === 'fancy') {
            document.getElementById('fancy_shapes').style.display = 'block';
        } else {
            document.getElementById('fancy_shapes').style.display = 'none';
        }
    }

    function toggleCertificateInput(value) {
        if (value === 'other') {
            document.getElementById('other_certificate').style.display = 'block';
        } else {
            document.getElementById('other_certificate').style.display = 'none';
        }

        // Update label based on certificate type
        const discountTypeLabel = document.querySelector('label[for="discount_type"]');
        if (value === 'none') {
            discountTypeLabel.textContent = 'Price:';
        } else {
            discountTypeLabel.textContent = 'Discount:';
        }
    }

      function toggleColorInput(value) {
        if (value === 'white') {
            document.getElementById('color_white').style.display = 'block';
            document.getElementById('color_fancy').style.display = 'none';
        } else {
            document.getElementById('color_white').style.display = 'none';
            document.getElementById('color_fancy').style.display = 'block';
        }
    }

    // Trigger the function on page load based on the initial value
    document.addEventListener("DOMContentLoaded", function() {
        var colorType = document.getElementById('color_type').value;
        toggleColorInput(colorType);
    });
    function toggleShapeInput(value) {
    if (value === 'fancy') {
        document.getElementById('fancy_shapes').style.display = 'block';
    } else {
        document.getElementById('fancy_shapes').style.display = 'none';
        document.getElementById('other_shape').style.display = 'none';
    }
}

function toggleOtherShapeInput(value) {
    if (value === 'other') {
        document.getElementById('other_shape').style.display = 'block';
    } else {
        document.getElementById('other_shape').style.display = 'none';
    }
}

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
                            <h2 class="h3 font-weight-normal">Post a Diamond</h2>
                        </div>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="nature">Nature:</label>
                                <select id="nature" name="nature" required class="form-control rounded-pill border-0 px-4 custom-select">
                                    <option value="Natural">Natural</option>
                                    <option value="CVD / Lab-grown">CVD / Lab-grown</option>
                                </select><br>
                            </div>

                            <div class="form-group mb-3">
                                <label for="photo_certificate">Photo of Certificate:</label>
                                <div class="custom-file">
                                    <input type="file" id="photo_certificate" name="photo_certificate" class="custom-file-input"  required>
                                    <label class="custom-file-label form-control rounded-pill border-0 px-4" for="photo_certificate">Choose file</label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="photo_diamond">Photo of Diamond:</label>
                                <div class="custom-file">
                                    <input type="file" id="photo_diamond" name="photo_diamond" class="custom-file-input"  required>
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
                                </select><br>
                            </div>

                            <div id="fancy_shapes" style="display: none;" class="form-group mb-3">
                                <label for="fancy_shape_select">Select Fancy Shape:</label>
                                <select id="fancy_shape_select" name="fancy_shape_select" class="form-control rounded-pill border-0 px-4 custom-select" onchange="toggleOtherShapeInput(this.value)">
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
                                </select><br>
                            </div>

                            <div id="other_shape" style="display: none;" class="form-group mb-3">
                                <label for="other_shape_input">Other Shape:</label>
                                <input type="text" id="other_shape_input" name="other_shape_input" class="form-control rounded-pill border-0 px-4" placeholder="Enter other shape">
                            </div>


                            <div class="form-group mb-3">
                                <label for="certificate_type">Certificate Type:</label>
                                <select id="certificate_type" name="certificate_type" required onchange="toggleCertificateInput(this.value)" class="form-control rounded-pill border-0 px-4 custom-select">
                                    <option value="GIA">GIA</option>
                                    <option value="IGI">IGI</option>
                                    <option value="HRD">HRD</option>
                                    <option value="none">None</option>
                                    <option value="other">Other</option>
                                </select><br>
                            </div>

                            <div id="other_certificate" style="display: none;" class="form-group mb-3">
                                <label for="other_certificate_input">Other Certificate:</label>
                                <input type="text" id="other_certificate_input" name="other_certificate_input" class="form-control rounded-pill border-0 px-4" placeholder="Enter certificate type">
                            </div>

                            <div class="form-group mb-3">
                                <label for="weight">Weight (in carats):</label>
                                <input type="number" id="weight" name="weight" step="0.01" class="form-control rounded-pill border-0 px-4" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="clarity">Clarity:</label>
                                <select id="clarity" name="clarity" class="form-control rounded-pill border-0 px-4 custom-select" required>
                                    <option value="IF">IF - Internally Flawless</option>
                                    <option value="VVS1">VVS1 - Very Very Slightly Included 1</option>
                                    <option value="VVS2">VVS2 - Very Very Slightly Included 2</option>
                                    <option value="VS1">VS1 - Very Slightly Included 1</option>
                                    <option value="VS2">VS2 - Very Slightly Included 2</option>
                                    <option value="SI1">SI1 - Slightly Included 1</option>
                                    <option value="SI2">SI2 - Slightly Included 2</option>
                                    <option value="I1">I1 - Included 1</option>
                                    <option value="I2">I2 - Included 2</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="color_type">Color Type:</label>
                                <select id="color_type" name="color_type" required onchange="toggleColorInput(this.value)" class="form-control rounded-pill border-0 px-4 custom-select">
                                    <option value="white">White</option>
                                    <option value="fancy">Fancy</option>
                                </select><br>
                            </div>

                            <div id="color_white" style="display: none;" class="form-group mb-3">
                                <label for="color_white_select">Select White Color:</label>
                                  <select id="color_white_select" name="color_white_select" class="form-control rounded-pill border-0 px-4 custom-select">
                                        <option value="D">D</option>
                                        <option value="E">E</option>
                                        <option value="F">F</option>
                                        <option value="G">G</option>
                                        <option value="H">H</option>
                                        <option value="I">I</option>
                                        <option value="J">J</option>
                                        <option value="K">K</option>
                                        <option value="L">L</option>
                                        <option value="M">M</option>
                                    </select>
                            </div>

                            <div id="color_fancy" style="display: none;" class="form-group mb-3">
                                <label for="color_fancy_input">Fancy Color:</label>
                                <input type="text" id="color_fancy_input" name="color_fancy_input" class="form-control rounded-pill border-0 px-4">
                            </div>

                            <div class="form-group mb-3">
                                <label for="cut_type">Cut Type:</label>
                                <input type="text" id="cut_type" name="cut_type" class="form-control rounded-pill border-0 px-4" placeholder="Enter cut type">
                            </div>

                            <div class="form-group mb-3">
                                <label for="fluorescence_type">Fluorescence:</label>
                               <select id="fluorescence_type" name="fluorescence_type" class="form-control rounded-pill border-0 px-4 custom-select" required>
                                    <option value="None">None</option>
                                    <option value="Faint">Faint</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Strong">Strong</option>
                                    <option value="Very Strong">Very Strong</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="discount_type">Discount:</label>
                                <input type="text" id="discount_type" name="discount_type" class="form-control rounded-pill border-0 px-4">
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-submit">Submit</button>
                            </div>
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
