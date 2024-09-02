<?php

session_start();

// $host = 'localhost';
// $user = 'root';
// $pass = 'root';
// $db = 'Gemify';


$host = 'srv1589.hstgr.io';
$user = 'u853504453_gemifylb';
$pass = '~Y5N:KFQWtbhahah';
$db = 'u853504453_Gemify';


try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}

?>

