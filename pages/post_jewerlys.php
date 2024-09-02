<?php

include '../config/db.php'; // Ensure you have the correct path to your database configuration

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to handle file upload
function uploadFile($fileInputName, $targetDir) {
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] == 0) {
        $filename = uniqid() . '-' . basename($_FILES[$fileInputName]['name']);
        $filepath = $targetDir . $filename;
        if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $filepath)) {
            return $filename;
        } else {
            return "Error uploading file.";
        }
    }
    return ''; // Return empty string if file was not uploaded
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input data
    $title = $_POST['title'];
    $type = $_POST['type'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $boost = 0; // Boost is always 0
    $is_approved = 'Pending';
    $user_id = $_SESSION['user_id'];

    // File upload handling for photos and video
    $photo_jewelry = uploadFile('photo_jewelry', '../uploads/jewelry/photo/');
    $photo_certificate = uploadFile('photo_certificate', '../uploads/jewelry/certificates/');
    $video = uploadFile('video', '../uploads/jewelry/video/');

    // Insert data into the database using named placeholders
    $stmt = $conn->prepare("INSERT INTO jewelry (user_id, title, photo_jewelry, photo_certificate, video, type, description, price, boost, is_approved) 
                            VALUES (:user_id, :title, :photo_jewelry, :photo_certificate, :video, :type, :description, :price, :boost, :is_approved)");
    if (!$stmt) {
        die('Prepare Error: ' . $conn->errorInfo()[2]);
    }
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':photo_jewelry', $photo_jewelry);
    $stmt->bindParam(':photo_certificate', $photo_certificate);
    $stmt->bindParam(':video', $video);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':boost', $boost);
    $stmt->bindParam(':is_approved', $is_approved);

    if ($stmt->execute()) {
        echo "<script>alert('Jewelry posted successfully.');</script>";
        header("Location: post_jewelry.php");
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
    <title>Post a Jewelry</title>
    <style>
        /* General input and textarea styling */
        input[type="text"], 
        input[type="file"], 
        textarea {
            border-radius: 8px; /* Slightly rounded corners */
            border: 1px solid #ccc; /* Light gray border */
            padding: 12px; /* More padding for better spacing */
            width: 100%; /* Full width for responsiveness */
            box-sizing: border-box; /* Include padding and border in element's total width and height */
            transition: border-color 0.3s ease-in-out; /* Smooth transition for border color */
        }

        input[type="text"]:hover, 
        textarea:hover {
            border-color: #bc9c48 !important; /* Main color for border on hover */
        }

        #description:hover{
            box-shadow: none !important;
            border: 1px solid #bc9c48 !important; /* Main color for border on hover */
        }

        textarea {
            resize: vertical; /* Allow vertical resizing only */
            min-height: 100px; /* Make the textarea a bit bigger */
        }

        /* Custom file input styling */
        .custom-file {
            position: relative;
            display: inline-block;
            width: 100%;
            height: 48px;
            margin-bottom: 10px; /* Margin between file inputs */
        }

        .custom-file input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0; /* Hide the default file input */
            cursor: pointer; /* Pointer cursor on hover */
        }

        .custom-file-label {
            display: block;
            padding: 12px;
            border-radius: 8px; /* Match with other inputs */
            border: 1px solid #ccc; /* Light gray border */
            background-color: #f8f9fa; /* Light gray background */
            color: #495057; /* Dark text */
            font-size: 16px; /* Font size for better readability */
            text-align: left; /* Center the text */
            cursor: pointer; /* Pointer cursor on hover */
        }      

        .file-name {
            display: block;
            margin-top: 8px; /* Space between label and file name */
            color: #6c757d; /* Light gray color */
            font-size: 14px; /* Slightly smaller font size */
            text-align: center; /* Center the text */
        }

        .btn-submit {
            background-color: #bc9c48; /* Main color from your palette */
            color: #fff; /* White text */
            border: none; /* Remove border */
            padding: 12px 20px; /* Padding for button */
            border-radius: 8px; /* Slightly rounded corners */
            font-size: 16px; /* Adjust font size */
            cursor: pointer; /* Pointer cursor on hover */
            text-transform: uppercase; /* Uppercase text */
        }

        .btn-submit:hover {
            background-color: #a87f3e; /* Darker shade on hover */
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-4 p-3">
        <div class="row justify-content-center align-items-center signup-form-container">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <h2 class="h3 font-weight-normal">Post a Jewelry</h2>
                        </div>
                        <form method="post" action="post_jewerlys.php" enctype="multipart/form-data">
                            <div class="form-group mb-3">
                                <label for="title">Title:</label>
                                <input type="text" id="title" name="title" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="photo_jewelry">Photo of Jewelry:</label>
                                <div class="custom-file">
                                    <input type="file" id="photo_jewelry" name="photo_jewelry" required>
                                    <label class="custom-file-label" for="photo_jewelry">Choose file</label>
                                    <span class="file-name" id="photo_jewelry_name"></span>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="photo_certificate">Photo of Certificate:</label>
                                <div class="custom-file">
                                    <input type="file" id="photo_certificate" name="photo_certificate" required>
                                    <label class="custom-file-label" for="photo_certificate">Choose file</label>
                                    <span class="file-name" id="photo_certificate_name"></span>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="video">Video of Jewelry:</label>
                                <div class="custom-file">
                                    <input type="file" id="video" name="video">
                                    <label class="custom-file-label" for="video">Choose file</label>
                                    <span class="file-name" id="video_name"></span>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="type">Type:</label>
                                <input type="text" id="type" name="type" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">Description:</label>
                                <textarea id="description" name="description" rows="4"></textarea>
                            </div>

                            <div class="form-group mb-3">
                                <label for="price">Price:</label>
                                <input type="text" id="price" name="price" required>
                            </div>

                            <input type="hidden" name="boost" value="0">

                            <div class="form-group mb-4 mt-4">
                                <button type="submit" class="btn-submit">Post Jewelry</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        // JavaScript to handle file selection and display file name
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                const fileName = this.files.length > 0 ? this.files[0].name : 'Choose file';
                document.getElementById(this.id + '_name').textContent = fileName;
                const label = this.nextElementSibling;
                label.textContent = fileName ? fileName : 'Choose file';
            });
        });
    </script>
</body>
</html>









