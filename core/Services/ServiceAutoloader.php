<?php

/**
 * Service Autoloader
 *
 * Automatically registers all service classes on demand.
 * Add to config.php after repository autoloader: require_once ROOT_PATH . '/core/Services/ServiceAutoloader.php';
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

spl_autoload_register(function ($class) {
    // Only handle service classes
    if (strpos($class, 'Service') === false) {
        return;
    }

    // Map class names to file paths
    $map = [
        'BaseService' => 'BaseService.php',
        'AssetService' => 'AssetService.php',
        'EmployeeService' => 'EmployeeService.php',
        'VendorService' => 'VendorService.php',
        'AssignmentService' => 'AssignmentService.php',
        'MaintenanceService' => 'MaintenanceService.php',
        'EquipmentRequestService' => 'EquipmentRequestService.php',
        'ServiceFactory' => 'ServiceFactory.php',
    ];

    if (isset($map[$class])) {
        $path = ROOT_PATH . '/core/Services/' . $map[$class];
        if (file_exists($path)) {
            require_once $path;
        }
    }
});
