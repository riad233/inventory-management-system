<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

require_once ROOT_PATH . "/core/Router.php";
require_once ROOT_PATH . "/config/config.php";

// Check if user is logged in for all routes except auth
$url = isset($_GET['url']) ? $_GET['url'] : '';

// Allow access to auth routes without login
if(strpos($url, 'auth') === false && empty($_SESSION['username']) && !empty($url)) {
    header("Location: auth/login");
    exit;
}

// Route requests
if(empty($url)) {
    $url = 'dashboard/index';
}

Router::route($url);
?>
