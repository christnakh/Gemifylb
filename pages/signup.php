<?php
include '../config/db.php';

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php"); // Redirect to index page
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_type = $_POST['id_type'];
    
    if ($id_type == 'passport') {
        if (isset($_FILES['passport_photo']) && $_FILES['passport_photo']['error'] == UPLOAD_ERR_OK) {
            $passportPhoto = $_FILES['passport_photo'];
            // Handle the passport photo upload
        }
    } elseif ($id_type == 'id_photo') {
        if (isset($_FILES['front_id_photo']) && $_FILES['front_id_photo']['error'] == UPLOAD_ERR_OK) {
            $frontIdPhoto = $_FILES['front_id_photo'];
            // Handle the front ID photo upload
        }
        if (isset($_FILES['back_id_photo']) && $_FILES['back_id_photo']['error'] == UPLOAD_ERR_OK) {
            $backIdPhoto = $_FILES['back_id_photo'];
            // Handle the back ID photo upload
        }
    }
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $phone_number = $_POST['phone_number'];
    $country_code = $_POST['country_code'];
    $role = $_POST['role'];

    // Directories for file uploads
    $profile_picture_dir = '../uploads/user/profile_picture/';
    $passport_photo_dir = '../uploads/user/passport_photo/';
    $business_certificate_dir = '../uploads/user/business_certificate/';
    $front_id_photo_dir = '../uploads/user/front_id_photo/';
    $back_id_photo_dir = '../uploads/user/back_id_photo/';

    // Ensure directories exist
    if (!is_dir($profile_picture_dir)) mkdir($profile_picture_dir, 0755, true);
    if (!is_dir($passport_photo_dir)) mkdir($passport_photo_dir, 0755, true);
    if (!is_dir($business_certificate_dir)) mkdir($business_certificate_dir, 0755, true);
    if (!is_dir($front_id_photo_dir)) mkdir($front_id_photo_dir, 0755, true);
    if (!is_dir($back_id_photo_dir)) mkdir($back_id_photo_dir, 0755, true);


    // Define the extensions you don't want to allow
$disallowed_extensions = ['exe', 'bat', 'sh', 'php', 'js', 'html'];

// Validate and process profile picture upload
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $profile_picture_extension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
    
    // Check if the file extension is in the disallowed list
    if (!in_array($profile_picture_extension, $disallowed_extensions)) {
        $profile_picture_filename = uniqid() . '-' . basename($_FILES['profile_picture']['name']);
        $profile_picture_filepath = $profile_picture_dir . $profile_picture_filename;
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture_filepath);
    } else {
        $error = "Invalid file type for profile picture.";
    }
} else {
    $profile_picture_filename = null;
}

    // Validate and process personal ID photo upload
    if (isset($_FILES['passport_photo']) && $_FILES['passport_photo']['error'] == 0) {
        $passport_photo_extension = pathinfo($_FILES['passport_photo']['name'], PATHINFO_EXTENSION);
        if (in_array($passport_photo_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $passport_photo_filename = uniqid() . '-' . basename($_FILES['passport_photo']['name']);
            $passport_photo_filepath = $passport_photo_dir . $passport_photo_filename;
            move_uploaded_file($_FILES['passport_photo']['tmp_name'], $passport_photo_filepath);
        } else {
            $error = "Invalid file type for personal ID photo.";
        }
    } else {
        $passport_photo_filename = null;
    }

    // Validate and process business certificate upload if role is 'business'
    if ($role == 'business' && isset($_FILES['business_document']) && $_FILES['business_document']['error'] == 0) {
        $business_document_extension = pathinfo($_FILES['business_document']['name'], PATHINFO_EXTENSION);
        if (in_array($business_document_extension, ['jpg', 'jpeg', 'png', 'gif', 'pdf'])) {
            $business_document_filename = uniqid() . '-' . basename($_FILES['business_document']['name']);
            $business_document_filepath = $business_certificate_dir . $business_document_filename;
            move_uploaded_file($_FILES['business_document']['tmp_name'], $business_document_filepath);
        } else {
            $error = "Invalid file type for business document.";
        }
    } else {
        $business_document_filename = null;
    }

    // Validate and process front ID photo upload
    if (isset($_FILES['front_id_photo']) && $_FILES['front_id_photo']['error'] == 0) {
        $front_id_photo_extension = pathinfo($_FILES['front_id_photo']['name'], PATHINFO_EXTENSION);
        if (in_array($front_id_photo_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $front_id_photo_filename = uniqid() . '-' . basename($_FILES['front_id_photo']['name']);
            $front_id_photo_filepath = $front_id_photo_dir . $front_id_photo_filename;
            move_uploaded_file($_FILES['front_id_photo']['tmp_name'], $front_id_photo_filepath);
        } else {
            $error = "Invalid file type for front ID photo.";
        }
    } else {
        $front_id_photo_filename = null;
    }

    // Validate and process back ID photo upload
    if (isset($_FILES['back_id_photo']) && $_FILES['back_id_photo']['error'] == 0) {
        $back_id_photo_extension = pathinfo($_FILES['back_id_photo']['name'], PATHINFO_EXTENSION);
        if (in_array($back_id_photo_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $back_id_photo_filename = uniqid() . '-' . basename($_FILES['back_id_photo']['name']);
            $back_id_photo_filepath = $back_id_photo_dir . $back_id_photo_filename;
            move_uploaded_file($_FILES['back_id_photo']['tmp_name'], $back_id_photo_filepath);
        } else {
            $error = "Invalid file type for back ID photo.";
        }
    } else {
        $back_id_photo_filename = null;
    }

    // If no error, insert data into database
    if (empty($error)) {
        try {
            $stmt = $conn->prepare("INSERT INTO users (email, phone_number, full_name, username, password, profile_picture, passport_photo, role, business_certificate, front_id_photo, back_id_photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$email, $country_code . $phone_number, $full_name, $username, $password, $profile_picture_filename, $passport_photo_filename, $role, $business_document_filename, $front_id_photo_filename, $back_id_photo_filename]);

            // Redirect to login page after successful signup
            header("Location: login.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // integrity constraint violation (duplicate email)
                $error = "Email already in use!";
            } else {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
      <!-- Favicon -->
    <link rel="icon" href="../images/favicon.ico" type="image/x-icon">
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
      <!-- other meta tags and elements -->
    <meta name="apple-mobile-web-app-capable" content="yes">
  <!-- Android -->
    <meta name="mobile-web-app-capable" content="yes">

    <style>
        .imagecontainer{
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .logo-img{
            margin-left: 30%;
        }
    </style>
</head>

<body>
    <?php include '../includes/header.php'; ?>

    <div class="container form-content mt-4">
        <form method="POST" action="" enctype="multipart/form-data" onsubmit="return validatePassword();">
            <div class="row justify-content-center align-items-center signup-form-container">
                <div class="col-md-6 imagecontainer">
                    <img src="../images/logo.png" alt="Logo" class="logo-img">
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center">
                                <h1 class="h3 font-weight-normal">Create Your Account</h1>
                                <p class="text-muted">Please fill in the form to sign up</p>
                            </div>
                            <?php if ($error): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $error; ?>
                                </div>
                            <?php endif; ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="full_name">Full Name:</label>
                                        <input type="text" id="full_name" name="full_name" class="form-control rounded-pill border-0 px-4" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="username">Username:</label>
                                        <input type="text" id="username" name="username" class="form-control rounded-pill border-0 px-4" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="email">Email:</label>
                                        <input type="email" id="email" name="email" class="form-control rounded-pill border-0 px-4" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="password">Password:</label>
                                        <input type="password" id="password" name="password" class="form-control rounded-pill border-0 px-4 text-primary" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="confirm_password">Confirm Password:</label>
                                        <input type="password" id="confirm_password" name="confirm_password" class="form-control rounded-pill border-0 px-4 text-primary" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="phone_number">Phone Number:</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <select id="country_code" name="country_code" class="form-control rounded-pill border-0" style="width: 45px;">
                                                <option value="+93">+93 (Afghanistan)</option>
                                                    <option value="+355">+355 (Albania)</option>
                                                    <option value="+213">+213 (Algeria)</option>
                                                    <option value="+376">+376 (Andorra)</option>
                                                    <option value="+244">+244 (Angola)</option>
                                                    <option value="+54">+54 (Argentina)</option>
                                                    <option value="+374">+374 (Armenia)</option>
                                                    <option value="+61">+61 (Australia)</option>
                                                    <option value="+43">+43 (Austria)</option>
                                                    <option value="+994">+994 (Azerbaijan)</option>
                                                    <option value="+973">+973 (Bahrain)</option>
                                                    <option value="+880">+880 (Bangladesh)</option>
                                                    <option value="+375">+375 (Belarus)</option>
                                                    <option value="+32">+32 (Belgium)</option>
                                                    <option value="+501">+501 (Belize)</option>
                                                    <option value="+229">+229 (Benin)</option>
                                                    <option value="+975">+975 (Bhutan)</option>
                                                    <option value="+591">+591 (Bolivia)</option>
                                                    <option value="+387">+387 (Bosnia and Herzegovina)</option>
                                                    <option value="+267">+267 (Botswana)</option>
                                                    <option value="+55">+55 (Brazil)</option>
                                                    <option value="+673">+673 (Brunei)</option>
                                                    <option value="+359">+359 (Bulgaria)</option>
                                                    <option value="+226">+226 (Burkina Faso)</option>
                                                    <option value="+257">+257 (Burundi)</option>
                                                    <option value="+855">+855 (Cambodia)</option>
                                                    <option value="+237">+237 (Cameroon)</option>
                                                    <option value="+1">+1 (Canada)</option>
                                                    <option value="+238">+238 (Cape Verde)</option>
                                                    <option value="+236">+236 (Central African Republic)</option>
                                                    <option value="+235">+235 (Chad)</option>
                                                    <option value="+56">+56 (Chile)</option>
                                                    <option value="+86">+86 (China)</option>
                                                    <option value="+57">+57 (Colombia)</option>
                                                    <option value="+269">+269 (Comoros)</option>
                                                    <option value="+242">+242 (Congo)</option>
                                                    <option value="+682">+682 (Cook Islands)</option>
                                                    <option value="+506">+506 (Costa Rica)</option>
                                                    <option value="+385">+385 (Croatia)</option>
                                                    <option value="+53">+53 (Cuba)</option>
                                                    <option value="+357">+357 (Cyprus)</option>
                                                    <option value="+420">+420 (Czech Republic)</option>
                                                    <option value="+45">+45 (Denmark)</option>
                                                    <option value="+253">+253 (Djibouti)</option>
                                                    <option value="+670">+670 (East Timor)</option>
                                                    <option value="+593">+593 (Ecuador)</option>
                                                    <option value="+20">+20 (Egypt)</option>
                                                    <option value="+503">+503 (El Salvador)</option>
                                                    <option value="+240">+240 (Equatorial Guinea)</option>
                                                    <option value="+291">+291 (Eritrea)</option>
                                                    <option value="+372">+372 (Estonia)</option>
                                                    <option value="+251">+251 (Ethiopia)</option>
                                                    <option value="+679">+679 (Fiji)</option>
                                                    <option value="+358">+358 (Finland)</option>
                                                    <option value="+33">+33 (France)</option>
                                                    <option value="+241">+241 (Gabon)</option>
                                                    <option value="+220">+220 (Gambia)</option>
                                                    <option value="+995">+995 (Georgia)</option>
                                                    <option value="+49">+49 (Germany)</option>
                                                    <option value="+233">+233 (Ghana)</option>
                                                    <option value="+30">+30 (Greece)</option>
                                                    <option value="+299">+299 (Greenland)</option>
                                                    <option value="+502">+502 (Guatemala)</option>
                                                    <option value="+224">+224 (Guinea)</option>
                                                    <option value="+245">+245 (Guinea-Bissau)</option>
                                                    <option value="+592">+592 (Guyana)</option>
                                                    <option value="+509">+509 (Haiti)</option>
                                                    <option value="+504">+504 (Honduras)</option>
                                                    <option value="+852">+852 (Hong Kong)</option>
                                                    <option value="+36">+36 (Hungary)</option>
                                                    <option value="+354">+354 (Iceland)</option>
                                                    <option value="+91">+91 (India)</option>
                                                    <option value="+62">+62 (Indonesia)</option>
                                                    <option value="+98">+98 (Iran)</option>
                                                    <option value="+964">+964 (Iraq)</option>
                                                    <option value="+353">+353 (Ireland)</option>
                                                    <option value="+972">+972 (Israel)</option>
                                                    <option value="+39">+39 (Italy)</option>
                                                    <option value="+225">+225 (Ivory Coast)</option>
                                                    <option value="+81">+81 (Japan)</option>
                                                    <option value="+962">+962 (Jordan)</option>
                                                    <option value="+7">+7 (Kazakhstan)</option>
                                                    <option value="+254">+254 (Kenya)</option>
                                                    <option value="+686">+686 (Kiribati)</option>
                                                    <option value="+965">+965 (Kuwait)</option>
                                                    <option value="+996">+996 (Kyrgyzstan)</option>
                                                    <option value="+856">+856 (Laos)</option>
                                                    <option value="+371">+371 (Latvia)</option>
                                                    <option value="+961">+961 (Lebanon)</option>
                                                    <option value="+266">+266 (Lesotho)</option>
                                                    <option value="+231">+231 (Liberia)</option>
                                                    <option value="+218">+218 (Libya)</option>
                                                    <option value="+423">+423 (Liechtenstein)</option>
                                                    <option value="+370">+370 (Lithuania)</option>
                                                    <option value="+352">+352 (Luxembourg)</option>
                                                    <option value="+853">+853 (Macau)</option>
                                                    <option value="+389">+389 (North Macedonia)</option>
                                                    <option value="+261">+261 (Madagascar)</option>
                                                    <option value="+265">+265 (Malawi)</option>
                                                    <option value="+60">+60 (Malaysia)</option>
                                                    <option value="+960">+960 (Maldives)</option>
                                                    <option value="+223">+223 (Mali)</option>
                                                    <option value="+356">+356 (Malta)</option>
                                                    <option value="+692">+692 (Marshall Islands)</option>
                                                    <option value="+222">+222 (Mauritania)</option>
                                                    <option value="+230">+230 (Mauritius)</option>
                                                    <option value="+52">+52 (Mexico)</option>
                                                    <option value="+691">+691 (Micronesia)</option>
                                                    <option value="+373">+373 (Moldova)</option>
                                                    <option value="+377">+377 (Monaco)</option>
                                                    <option value="+976">+976 (Mongolia)</option>
                                                    <option value="+382">+382 (Montenegro)</option>
                                                    <option value="+212">+212 (Morocco)</option>
                                                    <option value="+258">+258 (Mozambique)</option>
                                                    <option value="+95">+95 (Myanmar)</option>
                                                    <option value="+264">+264 (Namibia)</option>
                                                    <option value="+674">+674 (Nauru)</option>
                                                    <option value="+977">+977 (Nepal)</option>
                                                    <option value="+31">+31 (Netherlands)</option>
                                                    <option value="+64">+64 (New Zealand)</option>
                                                    <option value="+505">+505 (Nicaragua)</option>
                                                    <option value="+227">+227 (Niger)</option>
                                                    <option value="+234">+234 (Nigeria)</option>
                                                    <option value="+850">+850 (North Korea)</option>
                                                    <option value="+47">+47 (Norway)</option>
                                                    <option value="+968">+968 (Oman)</option>
                                                    <option value="+92">+92 (Pakistan)</option>
                                                    <option value="+680">+680 (Palau)</option>
                                                    <option value="+970">+970 (Palestine)</option>
                                                    <option value="+507">+507 (Panama)</option>
                                                    <option value="+675">+675 (Papua New Guinea)</option>
                                                    <option value="+595">+595 (Paraguay)</option>
                                                    <option value="+51">+51 (Peru)</option>
                                                    <option value="+63">+63 (Philippines)</option>
                                                    <option value="+48">+48 (Poland)</option>
                                                    <option value="+351">+351 (Portugal)</option>
                                                    <option value="+1">+1 (Puerto Rico)</option>
                                                    <option value="+974">+974 (Qatar)</option>
                                                    <option value="+40">+40 (Romania)</option>
                                                    <option value="+7">+7 (Russia)</option>
                                                    <option value="+250">+250 (Rwanda)</option>
                                                    <option value="+590">+590 (Saint Barthélemy)</option>
                                                    <option value="+290">+290 (Saint Helena)</option>
                                                    <option value="+1869">+1869 (Saint Kitts and Nevis)</option>
                                                    <option value="+1758">+1758 (Saint Lucia)</option>
                                                    <option value="+590">+590 (Saint Martin)</option>
                                                    <option value="+508">+508 (Saint Pierre and Miquelon)</option>
                                                    <option value="+1784">+1784 (Saint Vincent and the Grenadines)</option>
                                                    <option value="+685">+685 (Samoa)</option>
                                                    <option value="+378">+378 (San Marino)</option>
                                                    <option value="+239">+239 (São Tomé and Príncipe)</option>
                                                    <option value="+966">+966 (Saudi Arabia)</option>
                                                    <option value="+221">+221 (Senegal)</option>
                                                    <option value="+381">+381 (Serbia)</option>
                                                    <option value="+248">+248 (Seychelles)</option>
                                                    <option value="+232">+232 (Sierra Leone)</option>
                                                    <option value="+65">+65 (Singapore)</option>
                                                    <option value="+721">+721 (Sint Maarten)</option>
                                                    <option value="+677">+677 (Solomon Islands)</option>
                                                    <option value="+252">+252 (Somalia)</option>
                                                    <option value="+27">+27 (South Africa)</option>
                                                    <option value="+34">+34 (Spain)</option>
                                                    <option value="+94">+94 (Sri Lanka)</option>
                                                    <option value="+249">+249 (Sudan)</option>
                                                    <option value="+597">+597 (Suriname)</option>
                                                    <option value="+268">+268 (Swaziland)</option>
                                                    <option value="+46">+46 (Sweden)</option>
                                                    <option value="+41">+41 (Switzerland)</option>
                                                    <option value="+963">+963 (Syria)</option>
                                                    <option value="+886">+886 (Taiwan)</option>
                                                    <option value="+992">+992 (Tajikistan)</option>
                                                    <option value="+255">+255 (Tanzania)</option>
                                                    <option value="+66">+66 (Thailand)</option>
                                                    <option value="+670">+670 (Timor-Leste)</option>
                                                    <option value="+228">+228 (Togo)</option>
                                                    <option value="+690">+690 (Tokelau)</option>
                                                    <option value="+676">+676 (Tonga)</option>
                                                    <option value="+1">+1 (Trinidad and Tobago)</option>
                                                    <option value="+216">+216 (Tunisia)</option>
                                                    <option value="+90">+90 (Turkey)</option>
                                                    <option value="+993">+993 (Turkmenistan)</option>
                                                    <option value="+1">+1 (Turks and Caicos Islands)</option>
                                                    <option value="+256">+256 (Uganda)</option>
                                                    <option value="+380">+380 (Ukraine)</option>
                                                    <option value="+971">+971 (United Arab Emirates)</option>
                                                    <option value="+44">+44 (United Kingdom)</option>
                                                    <option value="+1">+1 (United States)</option>
                                                    <option value="+598">+598 (Uruguay)</option>
                                                    <option value="+998">+998 (Uzbekistan)</option>
                                                    <option value="+678">+678 (Vanuatu)</option>
                                                    <option value="+379">+379 (Vatican City)</option>
                                                    <option value="+58">+58 (Venezuela)</option>
                                                    <option value="+84">+84 (Vietnam)</option>
                                                    <option value="+681">+681 (Wallis and Futuna)</option>
                                                    <option value="+967">+967 (Yemen)</option>
                                                    <option value="+260">+260 (Zambia)</option>
                                                    <option value="+263">+263 (Zimbabwe)</option>
                                                </select>
                                            
                                            <input type="tel" id="phone_number" name="phone_number" class="form-control rounded-pill border-0 px-4 text-primary" required>
                                            </div>
                                        </div>
                                    </div>
                               
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="role">Role:</label>
                                        <select id="roleSelect" name="role" class="form-control rounded-pill border-0 px-4 custom-select" required>
                                            <option value="personal" selected>Personal</option>
                                            <option value="business">Business</option>
                                            <option value="admin">Admin</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="profile_picture">Profile Picture:</label>
                                        <div class="custom-file">
                                            <input type="file" id="profile_picture" name="profile_picture" class="custom-file-input" required>
                                            <label class="custom-file-label" for="profile_picture">Choose file</label>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label>Identification Type:</label>
                                        <div class="form-check">
                                            <input type="radio" id="passport" name="id_type" value="passport" class="form-check-input" checked>
                                            <label class="form-check-label" for="passport">Passport Photo</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" id="id_photo" name="id_type" value="id_photo" class="form-check-input">
                                            <label class="form-check-label" for="id_photo">ID Photo (Front & Back)</label>
                                        </div>
                                    </div>
                                    <div id="passport_photo_group" class="form-group mb-3">
                                        <label for="passport_photo">Passport Photo:</label>
                                        <div class="custom-file">
                                            <input type="file" id="passport_photo" name="passport_photo" class="custom-file-input">
                                            <label class="custom-file-label" for="passport_photo">Choose file</label>
                                        </div>
                                    </div>
                                    <div id="id_photo_group" class="form-group mb-3" style="display: none;">
                                        <label for="front_id_photo">Front ID Photo:</label>
                                        <div class="custom-file">
                                            <input type="file" id="front_id_photo" name="front_id_photo" class="custom-file-input">
                                            <label class="custom-file-label" for="front_id_photo">Choose file</label>
                                        </div>
                                        <label for="back_id_photo">Back ID Photo:</label>
                                        <div class="custom-file">
                                            <input type="file" id="back_id_photo" name="back_id_photo" class="custom-file-input">
                                            <label class="custom-file-label" for="back_id_photo">Choose file</label>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3" id="businessDocumentGroup" style="display: none;">
                                        <label for="business_document">Commercial Certification Document:</label>
                                        <div class="custom-file">
                                            <input type="file" id="business_document" name="business_document" class="custom-file-input">
                                            <label class="custom-file-label" for="business_document">Choose file</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-submit btn-block rounded-pill shadow-sm mt-4" type="submit">Sign Up</button>
                            <div class="text-center mt-3">
                                <p class="text-muted">Already have an account? <a href="login.php" class="text-primary">Login</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Custom JavaScript -->
    <script>
        function validatePassword() {
            var password = document.getElementById("password").value;
            var confirm_password = document.getElementById("confirm_password").value;
            if (password !== confirm_password) {
                alert("Passwords do not match!");
                return false;
            }
            return true;
        }

        // Show/Hide business document upload field based on role selection
        document.getElementById('roleSelect').addEventListener('change', function() {
            var businessDocumentGroup = document.getElementById('businessDocumentGroup');
            if (this.value === 'business') {
                businessDocumentGroup.style.display = 'block';
            } else {
                businessDocumentGroup.style.display = 'none';
            }
        });

        // Show/Hide passport photo vs ID photo fields based on selection
        document.querySelectorAll('input[name="id_type"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                var passportPhotoGroup = document.getElementById('passport_photo_group');
                var idPhotoGroup = document.getElementById('id_photo_group');
                if (document.getElementById('passport').checked) {
                    passportPhotoGroup.style.display = 'block';
                    idPhotoGroup.style.display = 'none';
                } else if (document.getElementById('id_photo').checked) {
                    passportPhotoGroup.style.display = 'none';
                    idPhotoGroup.style.display = 'block';
                }
            });
        });
    </script>
    <!-- Include Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>

</html>