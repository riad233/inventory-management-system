<?php

/**
 * Health Check Deployment Script
 * 
 * Verifies system health after deployment
 * Usage:
 *   php scripts/deployment/health-check.php
 */

require_once __DIR__ . '/../../config/config.php';

use Core\Monitoring\HealthCheck;
use Core\Performance\PerformanceMetrics;

$checks = [];
$allHealthy = true;

try {
    echo "Running deployment health checks..." . PHP_EOL . PHP_EOL;

    // Run health checks
    $healthCheck = new HealthCheck();
    $status = $healthCheck->runAll();

    echo "System Health Status: " . strtoupper($status['overall_status']) . PHP_EOL;
    echo str_repeat("=", 50) . PHP_EOL . PHP_EOL;

    // Check each component
    foreach ($status['checks'] as $component => $check) {
        $indicator = $check['status'] === 'healthy' ? '✓' : '✗';
        echo "$indicator $component: " . strtoupper($check['status']) . PHP_EOL;

        if (isset($check['details'])) {
            foreach ($check['details'] as $detail => $value) {
                echo "    $detail: $value" . PHP_EOL;
            }
        }

        if ($check['status'] !== 'healthy' && !in_array($check['status'], ['degraded', 'warning'])) {
            $allHealthy = false;
        }

        echo PHP_EOL;
    }

    // Show metrics summary
    echo "Performance Metrics:" . PHP_EOL;
    echo str_repeat("=", 50) . PHP_EOL;

    $metrics = PerformanceMetrics::getMetrics();
    $format = "%-30s: %s" . PHP_EOL;

    printf($format, "Execution Time", round($metrics['execution_time_ms'], 2) . " ms");
    printf($format, "Memory Usage", round($metrics['memory_current_mb'], 2) . " MB");
    printf($format, "Peak Memory", round($metrics['memory_peak_mb'], 2) . " MB");
    printf($format, "Queries Executed", $metrics['query_count']);
    printf($format, "Query Time", round($metrics['query_time_ms'], 2) . " ms");
    printf($format, "Cache Hit Rate", round($metrics['cache_hit_rate'], 1) . "%");

    echo PHP_EOL;

    if ($allHealthy) {
        echo "✓ All health checks passed! Deployment successful." . PHP_EOL;
        exit(0);
    } else {
        echo "⚠ Some health checks failed. Review details above." . PHP_EOL;
        exit(1);
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
