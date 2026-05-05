<?php

/**
 * Api/HealthCheckController - Health check and monitoring endpoints
 *
 * Endpoints:
 * - GET /api/health - System health status
 * - GET /api/health/metrics - Performance metrics
 * - GET /api/health/recommendations - Optimization recommendations
 */
class Api_HealthCheckController extends ApiController
{
    /**
     * Get system health status.
     */
    public function status(): void
    {
        try {
            // No auth required for health checks (typically)
            $health = HealthCheck::runAll();
            
            $statusCode = $health['overall_status'] === 'healthy' ? 200 : ($health['overall_status'] === 'degraded' ? 200 : 503);
            
            $this->respondSuccess($health, 'Health check complete', $statusCode);
        } catch (Throwable $e) {
            Logger::error('Health check error', ['exception' => $e->getMessage()]);
            $this->respondError('health_check_error', 'Health check failed', 500);
        }
    }

    /**
     * Get performance metrics.
     */
    public function metrics(): void
    {
        try {
            $this->requireRole('Admin');
            
            $metrics = PerformanceMetrics::getMetrics();
            $this->respondSuccess($metrics, 'Performance metrics retrieved');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    /**
     * Get optimization recommendations.
     */
    public function recommendations(): void
    {
        try {
            $this->requireRole('Admin');
            
            $report = PerformanceOptimizer::generateReport();
            $this->respondSuccess($report, 'Optimization report generated');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }

    /**
     * Get query profiling results.
     */
    public function queries(): void
    {
        try {
            $this->requireRole('Admin');
            
            $data = [
                'total_queries' => count(QueryProfiler::getQueries()),
                'problematic_queries' => QueryProfiler::getProblematicQueries(),
                'slowest_queries' => QueryProfiler::getSlowestQueries(10),
                'recommendations' => QueryProfiler::getOptimizationRecommendations(),
            ];
            
            $this->respondSuccess($data, 'Query profiling data');
        } catch (Throwable $e) {
            $this->handleException($e);
        }
    }
}
