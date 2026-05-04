<?php
// Define root path for absolute path resolution
define('ROOT_PATH', dirname(dirname(__FILE__)));

// Base URL pointing at the public folder (where CSS/JS live)
// Derived from SCRIPT_NAME so it works on any server path
if (!defined('BASE_URL')) {
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
    define('BASE_URL', rtrim(dirname($scriptName), '/'));   // e.g. /i_m_s/public
    define('APP_BASE', rtrim(dirname(BASE_URL), '/'));       // e.g. /i_m_s
}

// Debug mode - set to true for development
define('DEBUG_MODE', false);

// Include helper classes
require_once ROOT_PATH . '/config/logger.php';
require_once ROOT_PATH . '/config/validator.php';

// Secure session cookie: HttpOnly, SameSite=Strict, no JS access
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Strict',
]);
session_start();
date_default_timezone_set('Asia/Dhaka');

// ✅ ADD SESSION TIMEOUT CHECK (30 minutes)
$timeout_duration = 1800; // 30 minutes in seconds

if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    
    if ($elapsed_time > $timeout_duration) {
        // Session expired, destroy and redirect
        session_destroy();
        header('Location: ' . APP_BASE . '/auth/login?msg=Session+expired');
        exit;
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();

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