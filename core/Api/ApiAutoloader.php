<?php

/**
 * API Autoloader
 *
 * Automatically loads API controller classes on demand.
 * Add to config.php: require_once ROOT_PATH . '/core/Api/ApiAutoloader.php';
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

spl_autoload_register(function ($class) {
    // Only handle Api_ prefixed classes
    if (strpos($class, 'Api_') !== 0) {
        return;
    }

    // Map class names to file paths
    $map = [
        'Api_AssetController' => 'AssetController.php',
        'Api_EmployeeController' => 'EmployeeController.php',
        'Api_VendorController' => 'VendorController.php',
        'Api_AssignmentController' => 'AssignmentController.php',
        'Api_MaintenanceController' => 'MaintenanceController.php',
        'Api_EquipmentRequestController' => 'EquipmentRequestController.php',
        'Api_HealthCheckController' => 'HealthCheckController.php',
    ];

    if (isset($map[$class])) {
        $path = ROOT_PATH . '/app/controllers/Api/' . $map[$class];
        if (file_exists($path)) {
            require_once $path;
        }
    }
});
