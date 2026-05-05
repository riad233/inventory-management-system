<?php

/**
 * PerformanceMetrics - Track application performance metrics
 *
 * Collects metrics about:
 * - Request execution time
 * - Memory usage
 * - Database query count
 * - Cache hit/miss rates
 * - API response times
 */
class PerformanceMetrics
{
    private static array $metrics = [
        'start_time' => null,
        'start_memory' => null,
        'query_count' => 0,
        'query_time' => 0,
        'cache_hits' => 0,
        'cache_misses' => 0,
        'queries' => [],
    ];

    private static bool $isInitialized = false;

    /**
     * Initialize metrics collection.
     */
    public static function init(): void
    {
        if (self::$isInitialized) {
            return;
        }

        self::$metrics['start_time'] = microtime(true);
        self::$metrics['start_memory'] = memory_get_usage();
        self::$isInitialized = true;

        // Register shutdown handler to log final metrics
        register_shutdown_function([self::class, 'finalize']);
    }

    /**
     * Record a database query.
     *
     * @param string $query SQL query
     * @param float $executionTime Query execution time in seconds
     * @param int $rowCount Affected/selected row count
     * @return void
     */
    public static function recordQuery(string $query, float $executionTime, int $rowCount = 0): void
    {
        self::$metrics['query_count']++;
        self::$metrics['query_time'] += $executionTime;

        self::$metrics['queries'][] = [
            'query' => $query,
            'time' => $executionTime,
            'rows' => $rowCount,
            'timestamp' => microtime(true),
        ];
    }

    /**
     * Record cache operation.
     *
     * @param bool $isHit True for hit, false for miss
     * @return void
     */
    public static function recordCacheOperation(bool $isHit): void
    {
        if ($isHit) {
            self::$metrics['cache_hits']++;
        } else {
            self::$metrics['cache_misses']++;
        }
    }

    /**
     * Get current execution time in milliseconds.
     *
     * @return float
     */
    public static function getExecutionTime(): float
    {
        if (self::$metrics['start_time'] === null) {
            return 0;
        }
        return (microtime(true) - self::$metrics['start_time']) * 1000;
    }

    /**
     * Get current memory usage in MB.
     *
     * @return float
     */
    public static function getCurrentMemory(): float
    {
        return memory_get_usage() / 1024 / 1024;
    }

    /**
     * Get peak memory usage in MB.
     *
     * @return float
     */
    public static function getPeakMemory(): float
    {
        return memory_get_peak_usage() / 1024 / 1024;
    }

    /**
     * Get memory used since start in MB.
     *
     * @return float
     */
    public static function getMemoryUsed(): float
    {
        if (self::$metrics['start_memory'] === null) {
            return 0;
        }
        return (memory_get_usage() - self::$metrics['start_memory']) / 1024 / 1024;
    }

    /**
     * Get database query count.
     *
     * @return int
     */
    public static function getQueryCount(): int
    {
        return self::$metrics['query_count'];
    }

    /**
     * Get total database query time in milliseconds.
     *
     * @return float
     */
    public static function getQueryTime(): float
    {
        return self::$metrics['query_time'] * 1000;
    }

    /**
     * Get average query time in milliseconds.
     *
     * @return float
     */
    public static function getAverageQueryTime(): float
    {
        if (self::$metrics['query_count'] === 0) {
            return 0;
        }
        return self::getQueryTime() / self::$metrics['query_count'];
    }

    /**
     * Get cache hit count.
     *
     * @return int
     */
    public static function getCacheHits(): int
    {
        return self::$metrics['cache_hits'];
    }

    /**
     * Get cache miss count.
     *
     * @return int
     */
    public static function getCacheMisses(): int
    {
        return self::$metrics['cache_misses'];
    }

    /**
     * Get cache hit rate percentage.
     *
     * @return float
     */
    public static function getCacheHitRate(): float
    {
        $total = self::$metrics['cache_hits'] + self::$metrics['cache_misses'];
        if ($total === 0) {
            return 0;
        }
        return (self::$metrics['cache_hits'] / $total) * 100;
    }

    /**
     * Get all recorded queries.
     *
     * @return array
     */
    public static function getQueries(): array
    {
        return self::$metrics['queries'];
    }

    /**
     * Get slowest queries.
     *
     * @param int $limit Number of queries to return
     * @return array
     */
    public static function getSlowestQueries(int $limit = 10): array
    {
        $queries = self::$metrics['queries'];
        usort($queries, fn($a, $b) => $b['time'] <=> $a['time']);
        return array_slice($queries, 0, $limit);
    }

    /**
     * Get all collected metrics.
     *
     * @return array
     */
    public static function getMetrics(): array
    {
        return [
            'execution_time_ms' => round(self::getExecutionTime(), 2),
            'memory_current_mb' => round(self::getCurrentMemory(), 2),
            'memory_peak_mb' => round(self::getPeakMemory(), 2),
            'memory_used_mb' => round(self::getMemoryUsed(), 2),
            'query_count' => self::getQueryCount(),
            'query_time_ms' => round(self::getQueryTime(), 2),
            'query_avg_ms' => round(self::getAverageQueryTime(), 2),
            'cache_hits' => self::getCacheHits(),
            'cache_misses' => self::getCacheMisses(),
            'cache_hit_rate' => round(self::getCacheHitRate(), 1),
            'queries' => self::getQueries(),
        ];
    }

    /**
     * Log metrics to system.
     *
     * @return void
     */
    public static function finalize(): void
    {
        if (!self::$isInitialized) {
            return;
        }

        $metrics = self::getMetrics();

        // Log to file for analysis
        Logger::info('Performance Metrics', $metrics);

        // Check for performance issues
        if ($metrics['execution_time_ms'] > 1000) {
            Logger::warning('Slow Request', [
                'duration_ms' => $metrics['execution_time_ms'],
                'url' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            ]);
        }

        if ($metrics['query_count'] > 20) {
            Logger::warning('High Query Count', [
                'query_count' => $metrics['query_count'],
                'url' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            ]);
        }

        // Check for N+1 query patterns
        if ($metrics['query_count'] > 5 && self::detectN1Pattern()) {
            Logger::warning('Possible N+1 Query Pattern Detected', [
                'query_count' => $metrics['query_count'],
                'url' => $_SERVER['REQUEST_URI'] ?? 'unknown',
            ]);
        }
    }

    /**
     * Detect potential N+1 query patterns.
     *
     * @return bool
     */
    private static function detectN1Pattern(): bool
    {
        if (count(self::$metrics['queries']) < 5) {
            return false;
        }

        // Simple heuristic: many similar queries with small delay between them
        $queries = self::$metrics['queries'];
        $previousQuery = null;
        $similarCount = 0;

        foreach ($queries as $query) {
            if ($previousQuery && self::queriesAreSimilar($previousQuery['query'], $query['query'])) {
                $similarCount++;
                if ($similarCount >= 3) {
                    return true;
                }
            }
            $previousQuery = $query;
        }

        return false;
    }

    /**
     * Check if two queries are similar (potential N+1).
     *
     * @param string $query1
     * @param string $query2
     * @return bool
     */
    private static function queriesAreSimilar(string $query1, string $query2): bool
    {
        // Normalize queries (remove values, keep structure)
        $normalized1 = preg_replace('/\d+/', '?', $query1);
        $normalized2 = preg_replace('/\d+/', '?', $query2);

        // Check if similar after normalization
        return levenshtein($normalized1, $normalized2) < 10;
    }

    /**
     * Reset metrics.
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$metrics = [
            'start_time' => microtime(true),
            'start_memory' => memory_get_usage(),
            'query_count' => 0,
            'query_time' => 0,
            'cache_hits' => 0,
            'cache_misses' => 0,
            'queries' => [],
        ];
    }
}
