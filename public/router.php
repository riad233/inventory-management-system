<?php
/**
 * Router for PHP Development Server
 * Usage: php -S localhost:8000 -r router.php
 */

error_log("Router: REQUEST_URI=" . $_SERVER['REQUEST_URI']);

// Get the request URI
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$public_dir = __DIR__;
$requested_file = $public_dir . $request_uri;

error_log("Router: request_uri=$request_uri, requested_file=$requested_file");

// Serve static files directly (css, js, images, etc)
if (preg_match('/\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/', $request_uri)) {
    if (file_exists($requested_file) && is_file($requested_file)) {
        error_log("Router: Serving static file: $requested_file");
        return false; // Let PHP serve it
    }
}

// For directories, check if they have index.php
if (is_dir($requested_file)) {
    $index_file = $requested_file . '/index.php';
    if (file_exists($index_file)) {
        error_log("Router: Serving directory index: $index_file");
        $_SERVER['REQUEST_URI'] = $request_uri;
        require $index_file;
        return true;
    }
}

// Route all other requests through index.php
error_log("Router: Routing through index.php, url=" . trim($request_uri, '/'));
$_GET['url'] = trim($request_uri, '/');
require $public_dir . '/index.php';
?>
