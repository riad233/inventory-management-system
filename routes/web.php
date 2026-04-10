<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

require_once ROOT_PATH . "/core/Router.php";
require_once ROOT_PATH . "/config/config.php";

// Auto-login as default user if not already logged in
if (empty($_SESSION['username'])) {
    $_SESSION['username'] = 'user';
    $_SESSION['user_id'] = 1;
    $_SESSION['role'] = 'User';
}

// Clear any admin sessions
$_SESSION['is_admin'] = false;

// Get the URL
$url = isset($_GET['url']) ? $_GET['url'] : '';

// Route requests
if(empty($url)) {
    $url = 'home/index';
}

Router::route($url);
?>
