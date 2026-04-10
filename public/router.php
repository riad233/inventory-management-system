<?php
/**
 * Router for PHP Development Server
 * Usage: php -S localhost:8000 -r router.php
 */

$requested_file = __DIR__ . $_SERVER['REQUEST_URI'];

// Serve static files directly
if (file_exists($requested_file) && is_file($requested_file)) {
    // Set correct MIME types
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $requested_file);
    finfo_close($finfo);
    header('Content-Type: ' . $mime_type);
    readfile($requested_file);
    return true;
}

// Route all other requests through index.php
$_GET['url'] = trim($_SERVER['REQUEST_URI'], '/');
require __DIR__ . '/index.php';
?>
