<?php

/**
 * ApiResponse - Standard JSON response formatter for all API endpoints
 *
 * Ensures consistent response format across all API endpoints.
 * All responses follow standard JSON structure with meta information.
 *
 * Success Response:
 * {
 *   "status": "success",
 *   "data": {...},
 *   "message": "Operation successful",
 *   "timestamp": "2026-05-05T10:30:00Z"
 * }
 *
 * Error Response:
 * {
 *   "status": "error",
 *   "error": "error_code",
 *   "message": "Error description",
 *   "details": {...},
 *   "timestamp": "2026-05-05T10:30:00Z"
 * }
 */
class ApiResponse
{
    /**
     * Send success response.
     *
     * @param mixed $data Response data
     * @param string $message Success message
     * @param int $statusCode HTTP status code
     * @return void
     */
    public static function success($data = null, string $message = 'Operation successful', int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');

        $response = [
            'status' => 'success',
            'message' => $message,
            'timestamp' => gmdate('c'),
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Send created response (201).
     *
     * @param mixed $data Created resource data
     * @param string $message Success message
     * @return void
     */
    public static function created($data = null, string $message = 'Resource created successfully'): void
    {
        self::success($data, $message, 201);
    }

    /**
     * Send error response.
     *
     * @param string $error Error code/type
     * @param string $message Error message
     * @param int $statusCode HTTP status code
     * @param array $details Additional error details
     * @return void
     */
    public static function error(string $error, string $message, int $statusCode = 400, array $details = []): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');

        $response = [
            'status' => 'error',
            'error' => $error,
            'message' => $message,
            'timestamp' => gmdate('c'),
        ];

        if (!empty($details)) {
            $response['details'] = $details;
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Send validation error response (422).
     *
     * @param array $errors Field => error message array
     * @param string $message Error message
     * @return void
     */
    public static function validationError(array $errors, string $message = 'Validation failed'): void
    {
        self::error('validation_error', $message, 422, ['errors' => $errors]);
    }

    /**
     * Send not found error response (404).
     *
     * @param string $resourceType Type of resource
     * @param mixed $identifier Resource identifier
     * @return void
     */
    public static function notFound(string $resourceType, $identifier = null): void
    {
        $message = $resourceType . ' not found';
        if ($identifier !== null) {
            $message .= ' (ID: ' . $identifier . ')';
        }
        self::error('not_found', $message, 404);
    }

    /**
     * Send conflict error response (409).
     *
     * @param string $resource Resource name
     * @param string $reason Conflict reason
     * @return void
     */
    public static function conflict(string $resource, string $reason): void
    {
        self::error('conflict', $resource . ': ' . $reason, 409);
    }

    /**
     * Send unauthorized error response (401).
     *
     * @param string $message Error message
     * @return void
     */
    public static function unauthorized(string $message = 'Unauthorized'): void
    {
        self::error('unauthorized', $message, 401);
    }

    /**
     * Send forbidden error response (403).
     *
     * @param string $message Error message
     * @return void
     */
    public static function forbidden(string $message = 'Forbidden'): void
    {
        self::error('forbidden', $message, 403);
    }

    /**
     * Send paginated response.
     *
     * @param array $items Data items
     * @param int $total Total count
     * @param int $page Current page
     * @param int $perPage Items per page
     * @param string $message Success message
     * @return void
     */
    public static function paginated(array $items, int $total, int $page = 1, int $perPage = 10, string $message = 'Success'): void
    {
        $totalPages = ceil($total / $perPage);

        $data = [
            'items' => $items,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'totalPages' => $totalPages,
                'hasMore' => $page < $totalPages,
            ],
        ];

        self::success($data, $message, 200);
    }
}
