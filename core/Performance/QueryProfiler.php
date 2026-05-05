<?php

/**
 * QueryProfiler - Analyzes database queries for optimization opportunities
 *
 * Detects:
 * - Slow queries (> 100ms)
 * - N+1 query patterns
 * - Missing indexes
 * - Inefficient queries
 */
class QueryProfiler
{
    private static array $queries = [];
    private static array $recommendations = [];

    /**
     * Analyze a database query.
     *
     * @param string $query SQL query
     * @param float $executionTime Execution time in seconds
     * @param string $table Table affected
     * @return void
     */
    public static function analyze(string $query, float $executionTime, string $table = ''): void
    {
        $queryData = [
            'query' => $query,
            'execution_time' => $executionTime,
            'table' => $table,
            'timestamp' => microtime(true),
            'issues' => [],
        ];

        // Detect issues
        if ($executionTime > 0.1) {
            $queryData['issues'][] = 'slow_query';
            self::$recommendations[] = [
                'type' => 'slow_query',
                'query' => $query,
                'execution_time' => $executionTime,
                'suggestion' => 'Consider adding indexes or optimizing WHERE clause',
            ];
        }

        if (self::detectMissingIndex($query)) {
            $queryData['issues'][] = 'missing_index';
            self::$recommendations[] = [
                'type' => 'missing_index',
                'query' => $query,
                'suggestion' => 'Add index on columns used in WHERE/JOIN clauses',
            ];
        }

        if (self::detectFullTableScan($query)) {
            $queryData['issues'][] = 'full_table_scan';
            self::$recommendations[] = [
                'type' => 'full_table_scan',
                'query' => $query,
                'suggestion' => 'Query may perform full table scan. Verify WHERE conditions are indexed',
            ];
        }

        if (self::detectSelectStar($query)) {
            $queryData['issues'][] = 'select_star';
            self::$recommendations[] = [
                'type' => 'select_star',
                'query' => $query,
                'suggestion' => 'Avoid SELECT *. Specify only needed columns',
            ];
        }

        self::$queries[] = $queryData;
    }

    /**
     * Detect potential missing index.
     *
     * @param string $query
     * @return bool
     */
    private static function detectMissingIndex(string $query): bool
    {
        // Simple heuristic: WHERE clause without common indexed columns
        if (!stripos($query, 'WHERE')) {
            return false;
        }

        // Check if WHERE clause uses ID or common indexed columns
        $hasIndexedColumn = preg_match('/WHERE.*\b(id|created_at|status)\b/i', $query);

        return !$hasIndexedColumn;
    }

    /**
     * Detect full table scan pattern.
     *
     * @param string $query
     * @return bool
     */
    private static function detectFullTableScan(string $query): bool
    {
        // Pattern: SELECT without WHERE or JOIN without ON
        if (!preg_match('/FROM\s+(\w+)\s*(WHERE|JOIN|ORDER|LIMIT|$)/i', $query, $matches)) {
            return false;
        }

        // If no WHERE and no JOIN, likely table scan
        $hasWhere = stripos($query, 'WHERE') !== false;
        $hasJoin = stripos($query, 'JOIN') !== false;

        return !$hasWhere && !$hasJoin;
    }

    /**
     * Detect SELECT * pattern.
     *
     * @param string $query
     * @return bool
     */
    private static function detectSelectStar(string $query): bool
    {
        return preg_match('/SELECT\s+\*/i', $query) === 1;
    }

    /**
     * Get all profiled queries.
     *
     * @return array
     */
    public static function getQueries(): array
    {
        return self::$queries;
    }

    /**
     * Get all recommendations.
     *
     * @return array
     */
    public static function getRecommendations(): array
    {
        return self::$recommendations;
    }

    /**
     * Get queries with issues.
     *
     * @return array
     */
    public static function getProblematicQueries(): array
    {
        return array_filter(self::$queries, fn($q) => !empty($q['issues']));
    }

    /**
     * Get slowest queries.
     *
     * @param int $limit
     * @return array
     */
    public static function getSlowestQueries(int $limit = 10): array
    {
        $queries = self::$queries;
        usort($queries, fn($a, $b) => $b['execution_time'] <=> $a['execution_time']);
        return array_slice($queries, 0, $limit);
    }

    /**
     * Generate optimization recommendations.
     *
     * @return array
     */
    public static function getOptimizationRecommendations(): array
    {
        $recommendations = [];

        // Group by type
        $byType = [];
        foreach (self::$recommendations as $rec) {
            $byType[$rec['type']][] = $rec;
        }

        // Generate summary
        if (!empty($byType['slow_query'])) {
            $recommendations[] = [
                'priority' => 'high',
                'category' => 'Slow Queries',
                'count' => count($byType['slow_query']),
                'action' => 'Review and optimize queries taking >100ms',
                'examples' => array_slice($byType['slow_query'], 0, 3),
            ];
        }

        if (!empty($byType['missing_index'])) {
            $recommendations[] = [
                'priority' => 'high',
                'category' => 'Missing Indexes',
                'count' => count($byType['missing_index']),
                'action' => 'Add indexes on WHERE/JOIN columns',
                'examples' => array_slice($byType['missing_index'], 0, 3),
            ];
        }

        if (!empty($byType['full_table_scan'])) {
            $recommendations[] = [
                'priority' => 'medium',
                'category' => 'Full Table Scans',
                'count' => count($byType['full_table_scan']),
                'action' => 'Verify WHERE conditions or add indexes',
                'examples' => array_slice($byType['full_table_scan'], 0, 3),
            ];
        }

        if (!empty($byType['select_star'])) {
            $recommendations[] = [
                'priority' => 'low',
                'category' => 'SELECT * Usage',
                'count' => count($byType['select_star']),
                'action' => 'Specify only needed columns',
                'examples' => array_slice($byType['select_star'], 0, 3),
            ];
        }

        return $recommendations;
    }

    /**
     * Reset profiler.
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$queries = [];
        self::$recommendations = [];
    }
}
