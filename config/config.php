<?php
// Define root path for absolute path resolution
define('ROOT_PATH', dirname(dirname(__FILE__)));

// Debug mode - set to true for development
define('DEBUG_MODE', false);

// Include helper classes
require_once ROOT_PATH . '/config/logger.php';
require_once ROOT_PATH . '/config/validator.php';

session_start();
date_default_timezone_set('Asia/Dhaka');

function e($value) {
	return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function csrf_token() {
	if (empty($_SESSION['csrf_token'])) {
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	}
	return $_SESSION['csrf_token'];
}

function csrf_field() {
	return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function require_csrf() {
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		return;
	}
	$token = $_POST['csrf_token'] ?? '';
	if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
		http_response_code(403);
		die('Invalid CSRF token');
	}
}
?>