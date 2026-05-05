<?php

use core\Exceptions\{ValidationException, ConflictException, NotFoundException, AuthorizationException};

/**
 * ApiController - Base class for all REST API endpoints
 *
 * Extends the regular Controller class and adds JSON response methods.
 * All API controllers inherit from this base class.
 *
 * Features:
 * - JSON response handling
 * - Exception to JSON conversion
 * - Request data parsing (JSON input)
 * - Authentication validation
 * - Standard error responses
 */
class ApiController extends Controller
{
    /**
     * Get parsed JSON request body.
     *
     * @return array Request data
     */
    protected function getRequestData(): array
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true) ?? [];

        // Also include $_GET and $_POST for flexibility
        return array_merge($_GET, $_POST, $data);
    }

    /**
     * Get request parameter with default.
     *
     * @param string $key Parameter key
     * @param mixed $default Default value
     * @return mixed Parameter value
     */
    protected function getParam(string $key, $default = null)
    {
        $data = $this->getRequestData();
        return $data[$key] ?? $default;
    }

    /**
     * Send JSON success response.
     *
     * @param mixed $data Response data
     * @param string $message Success message
     * @param int $statusCode HTTP status code
     * @return void
     */
    protected function respondSuccess($data = null, string $message = 'Operation successful', int $statusCode = 200): void
    {
        ApiResponse::success($data, $message, $statusCode);
    }

    /**
     * Send JSON created response (201).
     *
     * @param mixed $data Created resource
     * @param string $message Success message
     * @return void
     */
    protected function respondCreated($data = null, string $message = 'Resource created successfully'): void
    {
        ApiResponse::created($data, $message);
    }

    /**
     * Send JSON error response.
     *
     * @param string $error Error code
     * @param string $message Error message
     * @param int $statusCode HTTP status code
     * @param array $details Additional details
     * @return void
     */
    protected function respondError(string $error, string $message, int $statusCode = 400, array $details = []): void
    {
        ApiResponse::error($error, $message, $statusCode, $details);
    }

    /**
     * Send validation error response.
     *
     * @param array $errors Field errors
     * @param string $message Error message
     * @return void
     */
    protected function respondValidationError(array $errors, string $message = 'Validation failed'): void
    {
        ApiResponse::validationError($errors, $message);
    }

    /**
     * Send not found response.
     *
     * @param string $resource Resource type
     * @param mixed $id Resource ID
     * @return void
     */
    protected function respondNotFound(string $resource, $id = null): void
    {
        ApiResponse::notFound($resource, $id);
    }

    /**
     * Send conflict response.
     *
     * @param string $resource Resource type
     * @param string $reason Conflict reason
     * @return void
     */
    protected function respondConflict(string $resource, string $reason): void
    {
        ApiResponse::conflict($resource, $reason);
    }

    /**
     * Send unauthorized response.
     *
     * @param string $message Error message
     * @return void
     */
    protected function respondUnauthorized(string $message = 'Unauthorized'): void
    {
        ApiResponse::unauthorized($message);
    }

    /**
     * Send forbidden response.
     *
     * @param string $message Error message
     * @return void
     */
    protected function respondForbidden(string $message = 'Forbidden'): void
    {
        ApiResponse::forbidden($message);
    }

    /**
     * Send paginated response.
     *
     * @param array $items Items
     * @param int $total Total count
     * @param int $page Current page
     * @param int $perPage Items per page
     * @param string $message Success message
     * @return void
     */
    protected function respondPaginated(array $items, int $total, int $page = 1, int $perPage = 10, string $message = 'Success'): void
    {
        ApiResponse::paginated($items, $total, $page, $perPage, $message);
    }

    /**
     * Handle exception and respond with JSON.
     *
     * @param Throwable $exception
     * @return void
     */
    protected function handleException(Throwable $exception): void
    {
        if ($exception instanceof ValidationException) {
            $this->respondValidationError($exception->getErrors(), $exception->getMessage());
        } elseif ($exception instanceof NotFoundException) {
            $this->respondNotFound($exception->getResourceType(), $exception->getResourceId());
        } elseif ($exception instanceof ConflictException) {
            $this->respondConflict($exception->getResource(), $exception->getReason());
        } elseif ($exception instanceof AuthorizationException) {
            $this->respondForbidden('Insufficient permissions');
        } else {
            // Generic error
            Logger::error('API Error', ['exception' => $exception->getMessage()]);
            $this->respondError('internal_error', 'An error occurred', 500);
        }
    }

    /**
     * Get API cache key for this endpoint.
     *
     * @param string $action Action name
     * @param array $params Optional parameters
     * @return string Cache key
     */
    protected function getApiCacheKey(string $action, array $params = []): string
    {
        // Extract endpoint from URL
        $pathParts = explode('/', trim($_SERVER['REQUEST_URI'] ?? '', '/'));
        $endpoint = $pathParts[array_search('api', $pathParts, true) + 1] ?? 'unknown';
        
        return CacheKeyGenerator::generateApi($endpoint . ':' . $action, $params);
    }

    /**
     * Remember API response with cache.
     *
     * @param string $action Action name
     * @param callable $callback Generator function
     * @param int|null $ttl Time to live (default 300s for list, 3600s for get)
     * @param array $params Optional parameters
     * @return mixed
     */
    protected function rememberApi(string $action, callable $callback, ?int $ttl = null, array $params = [])
    {
        $key = $this->getApiCacheKey($action, $params);
        $defaultTtl = in_array($action, ['list', 'index']) ? 300 : 3600;
        return CacheManager::getInstance()->remember($key, $callback, $ttl ?? $defaultTtl);
    }

    /**
     * Invalidate API cache for this endpoint.
     *
     * @return void
     */
    protected function invalidateApiCache(): void
    {
        CacheInvalidator::clearApi();
    }

    /**
     * Validate required fields in request data.
     *
     * @param array $data Request data
     * @param array $required Required field names
     * @throws ValidationException
     * @return void
     */
    protected function validateRequired(array $data, array $required): void
    {
        $errors = [];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        if (!empty($errors)) {
            throw new ValidationException('Validation failed', $errors);
        }
    }

    /**
     * Get current page from request (default 1).
     *
     * @return int
     */
    protected function getPage(): int
    {
        $page = (int) $this->getParam('page', 1);
        return max(1, $page);
    }

    /**
     * Get per-page count from request (default 10, max 100).
     *
     * @return int
     */
    protected function getPerPage(): int
    {
        $perPage = (int) $this->getParam('perPage', 10);
        $perPage = max(1, min(100, $perPage)); // Between 1-100
        return $perPage;
    }

    /**
     * Get search query from request.
     *
     * @return string|null
     */
    protected function getSearchQuery(): ?string
    {
        return $this->getParam('q') ?? $this->getParam('search');
    }

    /**
     * Require authentication (throw if not authenticated).
     *
     * @throws AuthorizationException
     * @return void
     */
    protected function requireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            throw new AuthorizationException('authentication_required', null);
        }
    }

    /**
     * Require specific role (throw if not authorized).
     *
     * @param string $role Required role
     * @throws AuthorizationException
     * @return void
     */
    protected function requireRole(string $role): void
    {
        $this->requireAuth();

        try {
            AuthorizationHelper::requireRole($role);
        } catch (AuthorizationException $e) {
            throw $e;
        }
    }
}
