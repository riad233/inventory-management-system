<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

// Make connection global
global $conn;

$host = "localhost";
$user = "root";
$password = "";
$database = "ims_db";

$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

// Set charset to utf8
mysqli_set_charset($conn, "utf8mb4");
?>
