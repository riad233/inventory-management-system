<?php

/**
 * Repository Autoloader
 * 
 * Automatically registers all repository classes on demand.
 * Add to config.php after exception handler: require_once ROOT_PATH . '/core/Repositories/RepositoryAutoloader.php';
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

spl_autoload_register(function ($class) {
    // Only handle repository classes
    if (strpos($class, 'Repository') === false) {
        return;
    }

    // Map class names to file paths
    $map = [
        'BaseRepository' => 'BaseRepository.php',
        'AssetRepository' => 'AssetRepository.php',
        'EmployeeRepository' => 'EmployeeRepository.php',
        'VendorRepository' => 'VendorRepository.php',
        'AssignmentRepository' => 'AssignmentRepository.php',
        'MaintenanceRepository' => 'MaintenanceRepository.php',
        'DepartmentRepository' => 'DepartmentRepository.php',
        'UserRepository' => 'UserRepository.php',
        'EquipmentRequestRepository' => 'EquipmentRequestRepository.php',
        'PermissionRepository' => 'PermissionRepository.php',
        'RepositoryFactory' => 'RepositoryFactory.php',
    ];

    if (isset($map[$class])) {
        $path = ROOT_PATH . '/core/Repositories/' . $map[$class];
        if (file_exists($path)) {
            require_once $path;
        }
    }
});
