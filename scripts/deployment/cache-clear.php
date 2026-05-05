<?php

/**
 * Cache Clear Script
 * 
 * Clears application and system caches
 * Usage:
 *   php scripts/deployment/cache-clear.php        # Clear all caches
 *   php scripts/deployment/cache-clear.php app    # Clear app cache only
 *   php scripts/deployment/cache-clear.php file   # Clear file cache only
 *   php scripts/deployment/cache-clear.php redis  # Clear Redis cache only
 */

require_once __DIR__ . '/../../config/config.php';

use Core\Cache\CacheManager;
use Core\Cache\CacheInvalidator;

try {
    $type = $argv[1] ?? 'all';

    echo "Clearing cache..." . PHP_EOL;

    $manager = CacheManager::getInstance();
    $invalidator = new CacheInvalidator();

    switch ($type) {
        case 'all':
            // Clear all caches
            echo "  • Clearing file cache..." . PHP_EOL;
            $manager->clear();
            
            echo "  • Clearing Redis cache..." . PHP_EOL;
            try {
                $manager->flush();
            } catch (Exception $e) {
                echo "    (Redis not available)" . PHP_EOL;
            }

            echo "  • Clearing application cache..." . PHP_EOL;
            $invalidator->invalidateAll();

            echo "✓ All caches cleared successfully." . PHP_EOL;
            break;

        case 'app':
            echo "  • Clearing application cache..." . PHP_EOL;
            $invalidator->invalidateAll();
            echo "✓ Application cache cleared successfully." . PHP_EOL;
            break;

        case 'file':
            echo "  • Clearing file cache..." . PHP_EOL;
            $manager->clear();
            echo "✓ File cache cleared successfully." . PHP_EOL;
            break;

        case 'redis':
            echo "  • Clearing Redis cache..." . PHP_EOL;
            try {
                $manager->flush();
                echo "✓ Redis cache cleared successfully." . PHP_EOL;
            } catch (Exception $e) {
                echo "✗ Error: " . $e->getMessage() . PHP_EOL;
                exit(1);
            }
            break;

        default:
            echo "Unknown cache type: $type" . PHP_EOL;
            echo "Available options: all, app, file, redis" . PHP_EOL;
            exit(1);
    }

    exit(0);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
