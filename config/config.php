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
define('APP_DEBUG', DEBUG_MODE); // For exception handler

// ========== STEP 2: Register Global Exception Handler ==========
// Register BEFORE any other code that might throw exceptions
require_once ROOT_PATH . '/core/ExceptionHandler.php';
// ExceptionHandler::register() is called automatically in ExceptionHandler.php

// ========== STEP 3: Register Repository Autoloader ==========
// Automatically loads repositories on demand
require_once ROOT_PATH . '/core/Repositories/RepositoryAutoloader.php';

// ========== STEP 4: Register Service Autoloader ==========
// Automatically loads services on demand
require_once ROOT_PATH . '/core/Services/ServiceAutoloader.php';

// ========== STEP 5: Register API Autoloader ==========
// Automatically loads API controller classes on demand
require_once ROOT_PATH . '/core/Api/ApiAutoloader.php';
// Include API response helper
require_once ROOT_PATH . '/core/Api/ApiResponse.php';

// ========== Include helper classes (needed by other components) ==========
require_once ROOT_PATH . '/config/logger.php';
require_once ROOT_PATH . '/config/validator.php';

// ========== STEP 6: Register Cache Autoloader ==========
// Automatically loads cache classes on demand
require_once ROOT_PATH . '/core/Cache/CacheAutoloader.php';
// Initialize cache manager (creates storage directory)
if (!is_dir(ROOT_PATH . '/storage/cache')) {
    mkdir(ROOT_PATH . '/storage/cache', 0755, true);
}
// Safely initialize cache manager with error handling
try {
    $cacheManager = CacheManager::getInstance(); // Initialize cache
} catch (Exception $e) {
    // Cache initialization failed, continue with file cache as fallback
    error_log("Cache initialization warning: " . $e->getMessage());
}

// ========== STEP 7: Register Performance & Monitoring Autoloaders ==========
// Automatically loads monitoring classes on demand
require_once ROOT_PATH . '/core/Monitoring/MonitoringAutoloader.php';
// Automatically loads performance classes on demand
require_once ROOT_PATH . '/core/Performance/PerformanceAutoloader.php';

// Load PerformanceMetrics explicitly before calling init()
require_once ROOT_PATH . '/core/Performance/PerformanceMetrics.php';

// Safely initialize performance metrics tracking with error handling
try {
    PerformanceMetrics::init();
} catch (Exception $e) {
    // Performance metrics initialization failed, continue without it
    error_log("Performance metrics initialization warning: " . $e->getMessage());
}

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