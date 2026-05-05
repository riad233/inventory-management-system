<?php

/**
 * CacheAutoloader - Automatically load cache classes
 *
 * Add to config.php: require_once ROOT_PATH . '/core/Cache/CacheAutoloader.php';
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

spl_autoload_register(function ($class) {
    // Only handle cache classes
    if (strpos($class, 'Cache') === false && $class !== 'CacheKeyGenerator' && $class !== 'CacheInvalidator') {
        return;
    }

    // Map class names to file paths
    $map = [
        'CacheBase' => 'CacheBase.php',
        'CacheKeyGenerator' => 'CacheKeyGenerator.php',
        'CacheManager' => 'CacheManager.php',
        'CacheInvalidator' => 'CacheInvalidator.php',
        'RedisCache' => 'RedisCache.php',
        'FileCache' => 'FileCache.php',
    ];

    if (isset($map[$class])) {
        $path = ROOT_PATH . '/core/Cache/' . $map[$class];
        if (file_exists($path)) {
            require_once $path;
        }
    }
});
