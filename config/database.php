<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

// Load environment configuration
require_once ROOT_PATH . '/config/env.php';

// Make connection global
global $conn;

// Get database credentials from environment variables
$host = EnvLoader::get('DB_HOST', 'localhost');
$user = EnvLoader::get('DB_USER', 'root');
$password = EnvLoader::get('DB_PASSWORD', '');
$database = EnvLoader::get('DB_NAME', 'ims_db');
$port = EnvLoader::get('DB_PORT', 3306);

// Establish database connection
$conn = mysqli_connect($host, $user, $password, $database, $port);
if (!$conn) {
    error_log("Database Connection Failed: " . mysqli_connect_error());
    if (EnvLoader::get('DEBUG_MODE') === true || EnvLoader::get('DEBUG_MODE') === 'true') {
        die("Connection Failed: " . mysqli_connect_error());
    } else {
        die("Connection Failed: Unable to connect to database.");
    }
}

// Set charset to utf8mb4
mysqli_set_charset($conn, "utf8mb4");

// Set error mode for mysqli (optional, for better error handling)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>

