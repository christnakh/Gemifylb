<?php

session_start();

// $host = 'localhost';
// $user = 'root';
// $pass = 'root';
// $db = 'Gemify';


$host = 'localhost';
$user = 'u853504453_gemifylb';
$pass = '~Y5N:KFQWt';
$db = 'u853504453_Gemify';


try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- other meta tags and elements -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <!-- Android -->
    <meta name="mobile-web-app-capable" content="yes">
</head>
<body>
    
</body>
</html>
