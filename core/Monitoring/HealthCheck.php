<?php

/**
 * HealthCheck - System health status monitoring
 *
 * Checks:
 * - Database connectivity
 * - Cache system status (Redis/File)
 * - Disk space availability
 * - Memory usage
 * - File system permissions
 */
class HealthCheck
{
    private static array $checks = [
        'database' => ['status' => 'unknown', 'message' => '', 'timestamp' => null],
        'cache' => ['status' => 'unknown', 'message' => '', 'timestamp' => null],
        'disk' => ['status' => 'unknown', 'message' => '', 'timestamp' => null],
        'memory' => ['status' => 'unknown', 'message' => '', 'timestamp' => null],
        'files' => ['status' => 'unknown', 'message' => '', 'timestamp' => null],
    ];

    /**
     * Run all health checks.
     *
     * @return array Health check results
     */
    public static function runAll(): array
    {
        self::checkDatabase();
        self::checkCache();
        self::checkDisk();
        self::checkMemory();
        self::checkFilePermissions();

        return self::getStatus();
    }

    /**
     * Check database connectivity.
     *
     * @return void
     */
    private static function checkDatabase(): void
    {
        try {
            $db = require ROOT_PATH . '/config/database.php';

            // Simple query to verify connection
            $result = $db->query('SELECT 1');

            if ($result) {
                self::$checks['database'] = [
                    'status' => 'healthy',
                    'message' => 'Database connected',
                    'timestamp' => microtime(true),
                ];
            } else {
                self::$checks['database'] = [
                    'status' => 'unhealthy',
                    'message' => 'Database query failed',
                    'timestamp' => microtime(true),
                ];
            }
        } catch (Throwable $e) {
            self::$checks['database'] = [
                'status' => 'unhealthy',
                'message' => 'Database error: ' . $e->getMessage(),
                'timestamp' => microtime(true),
            ];
        }
    }

    /**
     * Check cache system status.
     *
     * @return void
     */
    private static function checkCache(): void
    {
        try {
            if (!class_exists('CacheManager')) {
                self::$checks['cache'] = [
                    'status' => 'unavailable',
                    'message' => 'Cache not configured',
                    'timestamp' => microtime(true),
                ];
                return;
            }

            $cache = CacheManager::getInstance();

            // Test cache operation
            $testKey = 'health_check_' . time();
            $cache->set($testKey, 'test', 10);
            $value = $cache->get($testKey);

            if ($value === 'test') {
                $driver = $cache->driver === 'redis' ? 'Redis' : 'File';
                self::$checks['cache'] = [
                    'status' => 'healthy',
                    'message' => "Cache operational ($driver driver)",
                    'timestamp' => microtime(true),
                ];
                $cache->delete($testKey);
            } else {
                self::$checks['cache'] = [
                    'status' => 'unhealthy',
                    'message' => 'Cache read/write failed',
                    'timestamp' => microtime(true),
                ];
            }
        } catch (Throwable $e) {
            self::$checks['cache'] = [
                'status' => 'unhealthy',
                'message' => 'Cache error: ' . $e->getMessage(),
                'timestamp' => microtime(true),
            ];
        }
    }

    /**
     * Check disk space availability.
     *
     * @return void
     */
    private static function checkDisk(): void
    {
        try {
            $free = disk_free_space(ROOT_PATH);
            $total = disk_total_space(ROOT_PATH);

            if ($free === false || $total === false) {
                self::$checks['disk'] = [
                    'status' => 'warning',
                    'message' => 'Unable to determine disk space',
                    'timestamp' => microtime(true),
                ];
                return;
            }

            $percentFree = ($free / $total) * 100;
            $freeGB = $free / (1024 ** 3);

            if ($percentFree < 10) {
                self::$checks['disk'] = [
                    'status' => 'critical',
                    'message' => sprintf('Low disk space: %.2f GB free (%.1f%%)', $freeGB, $percentFree),
                    'timestamp' => microtime(true),
                ];
            } elseif ($percentFree < 20) {
                self::$checks['disk'] = [
                    'status' => 'warning',
                    'message' => sprintf('Low disk space: %.2f GB free (%.1f%%)', $freeGB, $percentFree),
                    'timestamp' => microtime(true),
                ];
            } else {
                self::$checks['disk'] = [
                    'status' => 'healthy',
                    'message' => sprintf('Disk space healthy: %.2f GB free (%.1f%%)', $freeGB, $percentFree),
                    'timestamp' => microtime(true),
                ];
            }
        } catch (Throwable $e) {
            self::$checks['disk'] = [
                'status' => 'warning',
                'message' => 'Disk check error: ' . $e->getMessage(),
                'timestamp' => microtime(true),
            ];
        }
    }

    /**
     * Check memory usage.
     *
     * @return void
     */
    private static function checkMemory(): void
    {
        try {
            $limit = ini_get('memory_limit');
            $current = memory_get_usage();
            $peak = memory_get_peak_usage();

            // Convert limit to bytes
            $limitBytes = self::parseBytes($limit);
            $percentUsed = ($current / $limitBytes) * 100;

            if ($percentUsed > 90) {
                self::$checks['memory'] = [
                    'status' => 'critical',
                    'message' => sprintf('Memory usage critical: %.1f%% of limit', $percentUsed),
                    'timestamp' => microtime(true),
                ];
            } elseif ($percentUsed > 75) {
                self::$checks['memory'] = [
                    'status' => 'warning',
                    'message' => sprintf('Memory usage high: %.1f%% of limit', $percentUsed),
                    'timestamp' => microtime(true),
                ];
            } else {
                self::$checks['memory'] = [
                    'status' => 'healthy',
                    'message' => sprintf('Memory usage normal: %.1f%% of limit', $percentUsed),
                    'timestamp' => microtime(true),
                ];
            }
        } catch (Throwable $e) {
            self::$checks['memory'] = [
                'status' => 'warning',
                'message' => 'Memory check error: ' . $e->getMessage(),
                'timestamp' => microtime(true),
            ];
        }
    }

    /**
     * Check critical file permissions.
     *
     * @return void
     */
    private static function checkFilePermissions(): void
    {
        try {
            $dirs = [
                ROOT_PATH . '/storage' => 'writable',
                ROOT_PATH . '/storage/logs' => 'writable',
                ROOT_PATH . '/storage/cache' => 'writable',
                ROOT_PATH . '/public/uploads' => 'writable',
                ROOT_PATH . '/config' => 'readable',
            ];

            $issues = [];
            foreach ($dirs as $dir => $permission) {
                if (!is_dir($dir)) {
                    $issues[] = "Missing directory: $dir";
                    continue;
                }

                if ($permission === 'writable' && !is_writable($dir)) {
                    $issues[] = "Not writable: $dir";
                } elseif ($permission === 'readable' && !is_readable($dir)) {
                    $issues[] = "Not readable: $dir";
                }
            }

            if (!empty($issues)) {
                self::$checks['files'] = [
                    'status' => 'warning',
                    'message' => 'Permission issues: ' . implode(', ', $issues),
                    'timestamp' => microtime(true),
                ];
            } else {
                self::$checks['files'] = [
                    'status' => 'healthy',
                    'message' => 'File permissions correct',
                    'timestamp' => microtime(true),
                ];
            }
        } catch (Throwable $e) {
            self::$checks['files'] = [
                'status' => 'warning',
                'message' => 'File check error: ' . $e->getMessage(),
                'timestamp' => microtime(true),
            ];
        }
    }

    /**
     * Get overall health status.
     *
     * @return array
     */
    public static function getStatus(): array
    {
        $overall = self::calculateOverallStatus();

        return [
            'overall_status' => $overall,
            'timestamp' => time(),
            'checks' => self::$checks,
        ];
    }

    /**
     * Calculate overall health status.
     *
     * @return string
     */
    private static function calculateOverallStatus(): string
    {
        $statuses = array_column(self::$checks, 'status');

        if (in_array('critical', $statuses)) {
            return 'critical';
        } elseif (in_array('unhealthy', $statuses)) {
            return 'unhealthy';
        } elseif (in_array('warning', $statuses)) {
            return 'degraded';
        }

        return 'healthy';
    }

    /**
     * Parse byte string (e.g., "256M" to bytes).
     *
     * @param string $value
     * @return int
     */
    private static function parseBytes(string $value): int
    {
        $value = trim($value);
        $last = strtoupper($value[strlen($value) - 1]);

        $num = (int) $value;

        switch ($last) {
            case 'G':
                return $num * (1024 ** 3);
            case 'M':
                return $num * (1024 ** 2);
            case 'K':
                return $num * 1024;
            default:
                return $num;
        }
    }

    /**
     * Get health check for specific component.
     *
     * @param string $component Component name
     * @return array|null
     */
    public static function getCheck(string $component): ?array
    {
        return self::$checks[$component] ?? null;
    }

    /**
     * Is system healthy overall?
     *
     * @return bool
     */
    public static function isHealthy(): bool
    {
        return self::calculateOverallStatus() === 'healthy';
    }
}
