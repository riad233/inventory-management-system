<?php

/**
 * MonitoringAutoloader - Register monitoring classes
 */
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

spl_autoload_register(function ($class) {
    $monitoringClasses = [
        'HealthCheck' => '/core/Monitoring/HealthCheck.php',
    ];

    if (isset($monitoringClasses[$class])) {
        require_once ROOT_PATH . $monitoringClasses[$class];
    }
});
