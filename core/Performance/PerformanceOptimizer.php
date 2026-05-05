<?php

/**
 * PerformanceOptimizer - Provides optimization strategies and recommendations
 *
 * Analyzes:
 * - API response times
 * - Database query efficiency
 * - Cache effectiveness
 * - Memory usage patterns
 */
class PerformanceOptimizer
{
    /**
     * Get optimization report.
     *
     * @return array
     */
    public static function generateReport(): array
    {
        return [
            'timestamp' => time(),
            'metrics' => self::analyzeMetrics(),
            'database' => self::analyzeDatabasePerformance(),
            'cache' => self::analyzeCachePerformance(),
            'memory' => self::analyzeMemoryUsage(),
            'recommendations' => self::generateRecommendations(),
            'optimization_score' => self::calculateOptimizationScore(),
        ];
    }

    /**
     * Analyze current metrics.
     *
     * @return array
     */
    private static function analyzeMetrics(): array
    {
        $metrics = PerformanceMetrics::getMetrics();

        return [
            'execution_time_ms' => $metrics['execution_time_ms'],
            'status' => $metrics['execution_time_ms'] < 100 ? 'excellent' : ($metrics['execution_time_ms'] < 500 ? 'good' : 'needs_improvement'),
            'memory_used_mb' => $metrics['memory_used_mb'],
            'memory_status' => $metrics['memory_used_mb'] < 10 ? 'good' : ($metrics['memory_used_mb'] < 50 ? 'acceptable' : 'high'),
        ];
    }

    /**
     * Analyze database performance.
     *
     * @return array
     */
    private static function analyzeDatabasePerformance(): array
    {
        $metrics = PerformanceMetrics::getMetrics();

        // Heuristic: ideal is < 5 queries per request
        $queryCount = $metrics['query_count'];
        $queryStatus = $queryCount <= 5 ? 'good' : ($queryCount <= 10 ? 'acceptable' : 'high');

        // Average query time
        $avgTime = $metrics['query_avg_ms'];
        $timeStatus = $avgTime < 20 ? 'good' : ($avgTime < 50 ? 'acceptable' : 'slow');

        return [
            'query_count' => $queryCount,
            'query_status' => $queryStatus,
            'average_time_ms' => $avgTime,
            'time_status' => $timeStatus,
            'total_query_time_ms' => $metrics['query_time_ms'],
            'slowest_queries' => PerformanceMetrics::getSlowestQueries(3),
        ];
    }

    /**
     * Analyze cache performance.
     *
     * @return array
     */
    private static function analyzeCachePerformance(): array
    {
        $metrics = PerformanceMetrics::getMetrics();

        $hitRate = $metrics['cache_hit_rate'];
        $cacheStatus = $hitRate >= 80 ? 'excellent' : ($hitRate >= 50 ? 'good' : ($hitRate >= 20 ? 'acceptable' : 'needs_improvement'));

        return [
            'hits' => $metrics['cache_hits'],
            'misses' => $metrics['cache_misses'],
            'hit_rate_percent' => $hitRate,
            'cache_status' => $cacheStatus,
            'impact' => $hitRate >= 80 ? 'Cache providing significant performance benefit' : 'Cache not being utilized effectively',
        ];
    }

    /**
     * Analyze memory usage patterns.
     *
     * @return array
     */
    private static function analyzeMemoryUsage(): array
    {
        $metrics = PerformanceMetrics::getMetrics();

        $peakUsage = $metrics['memory_peak_mb'];
        $memoryStatus = $peakUsage < 20 ? 'good' : ($peakUsage < 50 ? 'acceptable' : ($peakUsage < 128 ? 'high' : 'critical'));

        return [
            'peak_usage_mb' => $peakUsage,
            'current_usage_mb' => $metrics['memory_current_mb'],
            'memory_status' => $memoryStatus,
            'efficiency' => $metrics['memory_used_mb'] / max(1, $metrics['query_count']) < 5 ? 'efficient' : 'consider_review',
        ];
    }

    /**
     * Generate optimization recommendations.
     *
     * @return array
     */
    private static function generateRecommendations(): array
    {
        $recommendations = [];
        $metrics = PerformanceMetrics::getMetrics();

        // Query count recommendation
        if ($metrics['query_count'] > 10) {
            $recommendations[] = [
                'type' => 'database',
                'priority' => 'high',
                'issue' => 'High number of queries (' . $metrics['query_count'] . ')',
                'recommendation' => 'Implement query optimization: use JOINs instead of N+1 patterns',
                'potential_gain' => '20-40% faster response time',
            ];
        }

        // Slow queries
        $slowest = PerformanceMetrics::getSlowestQueries(1);
        if (!empty($slowest) && $slowest[0]['time'] > 0.1) {
            $recommendations[] = [
                'type' => 'database',
                'priority' => 'high',
                'issue' => 'Slow query detected: ' . round($slowest[0]['time'] * 1000, 1) . 'ms',
                'recommendation' => 'Add indexes or optimize query WHERE clause',
                'potential_gain' => '50-70% faster for this query',
            ];
        }

        // Cache hit rate
        if ($metrics['cache_hit_rate'] < 50) {
            $recommendations[] = [
                'type' => 'cache',
                'priority' => 'medium',
                'issue' => 'Low cache hit rate: ' . $metrics['cache_hit_rate'] . '%',
                'recommendation' => 'Increase cache TTL or ensure all list operations are cached',
                'potential_gain' => '30-50% faster for cached endpoints',
            ];
        }

        // Memory usage
        if ($metrics['memory_peak_mb'] > 50) {
            $recommendations[] = [
                'type' => 'memory',
                'priority' => 'medium',
                'issue' => 'High peak memory usage: ' . $metrics['memory_peak_mb'] . ' MB',
                'recommendation' => 'Review object initialization and variable scope',
                'potential_gain' => 'More stable under load',
            ];
        }

        // Execution time
        if ($metrics['execution_time_ms'] > 500) {
            $recommendations[] = [
                'type' => 'general',
                'priority' => 'high',
                'issue' => 'Slow request: ' . $metrics['execution_time_ms'] . ' ms',
                'recommendation' => 'Combine database and cache optimizations',
                'potential_gain' => '60-80% faster overall',
            ];
        }

        return $recommendations;
    }

    /**
     * Calculate overall optimization score (0-100).
     *
     * @return int
     */
    private static function calculateOptimizationScore(): int
    {
        $metrics = PerformanceMetrics::getMetrics();
        $score = 100;

        // Deduct for slow execution
        if ($metrics['execution_time_ms'] > 1000) {
            $score -= 20;
        } elseif ($metrics['execution_time_ms'] > 500) {
            $score -= 10;
        } elseif ($metrics['execution_time_ms'] > 200) {
            $score -= 5;
        }

        // Deduct for high query count
        if ($metrics['query_count'] > 20) {
            $score -= 20;
        } elseif ($metrics['query_count'] > 10) {
            $score -= 10;
        } elseif ($metrics['query_count'] > 5) {
            $score -= 5;
        }

        // Deduct for low cache hit rate
        if ($metrics['cache_hit_rate'] < 30 && ($metrics['cache_hits'] + $metrics['cache_misses']) > 5) {
            $score -= 10;
        }

        // Deduct for high memory usage
        if ($metrics['memory_peak_mb'] > 128) {
            $score -= 15;
        } elseif ($metrics['memory_peak_mb'] > 64) {
            $score -= 10;
        } elseif ($metrics['memory_peak_mb'] > 32) {
            $score -= 5;
        }

        return max(0, $score);
    }

    /**
     * Get performance benchmarks.
     *
     * @return array
     */
    public static function getBenchmarks(): array
    {
        return [
            'excellent' => [
                'execution_time_ms' => '< 100 ms',
                'query_count' => '< 5 queries',
                'query_avg_time_ms' => '< 20 ms',
                'cache_hit_rate' => '> 90%',
                'memory_peak_mb' => '< 20 MB',
            ],
            'good' => [
                'execution_time_ms' => '100-300 ms',
                'query_count' => '5-10 queries',
                'query_avg_time_ms' => '20-50 ms',
                'cache_hit_rate' => '70-90%',
                'memory_peak_mb' => '20-50 MB',
            ],
            'acceptable' => [
                'execution_time_ms' => '300-500 ms',
                'query_count' => '10-15 queries',
                'query_avg_time_ms' => '50-100 ms',
                'cache_hit_rate' => '40-70%',
                'memory_peak_mb' => '50-100 MB',
            ],
            'needs_improvement' => [
                'execution_time_ms' => '> 500 ms',
                'query_count' => '> 15 queries',
                'query_avg_time_ms' => '> 100 ms',
                'cache_hit_rate' => '< 40%',
                'memory_peak_mb' => '> 100 MB',
            ],
        ];
    }
}
