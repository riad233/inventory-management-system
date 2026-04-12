<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

require_once ROOT_PATH . "/core/Router.php";
require_once ROOT_PATH . "/config/config.php";


// Get the URL
$url = isset($_GET['url']) ? $_GET['url'] : '';

// Route requests
if(empty($url)) {
    $url = 'home/index';
}

Router::route($url);
?>
