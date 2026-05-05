<?php

/**
 * PerformanceAutoloader - Register performance monitoring classes
 */
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

spl_autoload_register(function ($class) {
    $performanceClasses = [
        'PerformanceMetrics' => '/core/Performance/PerformanceMetrics.php',
        'QueryProfiler' => '/core/Performance/QueryProfiler.php',
        'PerformanceOptimizer' => '/core/Performance/PerformanceOptimizer.php',
    ];

    if (isset($performanceClasses[$class])) {
        require_once ROOT_PATH . $performanceClasses[$class];
    }
});
