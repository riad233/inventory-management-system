<?php
/**
 * Global Exception Handler
 * 
 * Centralized exception handling for the entire application.
 * Catches all exceptions and renders appropriate responses.
 * Never exposes stack traces in production.
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

require_once ROOT_PATH . '/core/Exceptions/AppException.php';
require_once ROOT_PATH . '/core/Exceptions/ValidationException.php';
require_once ROOT_PATH . '/core/Exceptions/AuthorizationException.php';
require_once ROOT_PATH . '/core/Exceptions/NotFoundException.php';
require_once ROOT_PATH . '/core/Exceptions/DatabaseException.php';
require_once ROOT_PATH . '/core/Exceptions/ConflictException.php';

class ExceptionHandler
{
    /**
     * Register global exception handling
     * Call this early in application bootstrap
     * 
     * @return void
     */
    public static function register(): void
    {
        set_exception_handler([self::class, 'handle']);
        set_error_handler([self::class, 'handleError']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
     * Handle uncaught exceptions
     * 
     * @param Throwable $exception
     * @return void
     */
    public static function handle(Throwable $exception): void
    {
        // Log the exception
        self::log($exception);

        // Get HTTP status code
        $statusCode = 500;
        if ($exception instanceof AppException) {
            $statusCode = $exception->getStatusCode();
        }

        // Set HTTP header
        http_response_code($statusCode);

        // Determine if JSON response needed (API requests)
        $isJson = self::isJsonRequest();

        // Render response
        if ($isJson) {
            self::renderJsonError($exception, $statusCode);
        } else {
            self::renderHtmlError($exception, $statusCode);
        }

        exit;
    }

    /**
     * Handle PHP errors
     * Convert to exceptions
     * 
     * @param int    $severity
     * @param string $message
     * @param string $file
     * @param int    $line
     * @return bool
     */
    public static function handleError($severity, $message, $file, $line): bool
    {
        // Only handle errors we care about
        if (!(error_reporting() & $severity)) {
            return false;
        }

        throw new ErrorException($message, 0, $severity, $file, $line);
    }

    /**
     * Handle fatal errors on shutdown
     * 
     * @return void
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();
        
        if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE], true)) {
            self::handle(
                new ErrorException(
                    $error['message'],
                    0,
                    $error['type'],
                    $error['file'],
                    $error['line']
                )
            );
        }
    }

    /**
     * Log exception to file
     * 
     * @param Throwable $exception
     * @return void
     */
    private static function log(Throwable $exception): void
    {
        if (!class_exists('Logger')) {
            require_once ROOT_PATH . '/config/logger.php';
        }

        $context = [];
        
        if ($exception instanceof AppException) {
            $context = $exception->getContext();
        }

        $context['file'] = $exception->getFile();
        $context['line'] = $exception->getLine();
        $context['trace'] = self::getProductionSafeTrace($exception);

        // Log based on severity
        if ($exception instanceof AppException && $exception->getStatusCode() >= 500) {
            Logger::error(
                'Exception: ' . get_class($exception),
                array_merge(['message' => $exception->getMessage()], $context)
            );
        } else {
            Logger::warning(
                'Exception: ' . get_class($exception),
                array_merge(['message' => $exception->getMessage()], $context)
            );
        }
    }

    /**
     * Get production-safe stack trace
     * Never includes actual file contents or sensitive information
     * 
     * @param Throwable $exception
     * @return array
     */
    private static function getProductionSafeTrace(Throwable $exception): array
    {
        $trace = [];
        foreach ($exception->getTrace() as $item) {
            $trace[] = [
                'file' => $item['file'] ?? 'unknown',
                'line' => $item['line'] ?? 0,
                'function' => $item['function'] ?? 'unknown',
                'class' => $item['class'] ?? null,
            ];
        }
        return $trace;
    }

    /**
     * Check if this is a JSON API request
     * 
     * @return bool
     */
    private static function isJsonRequest(): bool
    {
        $path = $_GET['url'] ?? '';
        if (strpos($path, 'api/') === 0) {
            return true;
        }

        $acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? '';
        return strpos($acceptHeader, 'application/json') !== false;
    }

    /**
     * Render JSON error response (for API)
     * 
     * @param Throwable $exception
     * @param int       $statusCode
     * @return void
     */
    private static function renderJsonError(Throwable $exception, int $statusCode): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $response = [
            'success' => false,
            'status' => $statusCode,
            'message' => 'An error occurred',
        ];

        // Add user-friendly message
        if ($exception instanceof AppException) {
            $response['message'] = $exception->getUserMessage();
        } else {
            $response['message'] = self::getProductionMessage($statusCode);
        }

        // Add validation errors if available
        if ($exception instanceof ValidationException) {
            $response['errors'] = $exception->getErrors();
        }

        // In development, add more details (check APP_DEBUG environment)
        if (self::isDebugMode()) {
            $response['debug'] = [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
        }

        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Render HTML error response (for web)
     * 
     * @param Throwable $exception
     * @param int       $statusCode
     * @return void
     */
    private static function renderHtmlError(Throwable $exception, int $statusCode): void
    {
        // Get user-friendly message
        $message = 'An error occurred';
        if ($exception instanceof AppException) {
            $message = $exception->getUserMessage();
        } else {
            $message = self::getProductionMessage($statusCode);
        }

        // Load appropriate error view
        $viewPath = null;
        switch ($statusCode) {
            case 404:
                $viewPath = ROOT_PATH . '/app/views/errors/404.php';
                break;
            case 403:
                $viewPath = ROOT_PATH . '/app/views/errors/403.php';
                break;
            default:
                $viewPath = ROOT_PATH . '/app/views/errors/500.php';
        }

        // Check if user is authenticated (to determine which layout to use)
        $isAuthenticated = !empty($_SESSION['username'] ?? null);

        // Prepare data
        $data = [
            'title' => "{$statusCode} Error",
            'message' => $message,
            'status_code' => $statusCode,
        ];

        // If validation exception, add field errors
        if ($exception instanceof ValidationException) {
            $data['errors'] = $exception->getErrors();
        }

        // Include error view
        if (file_exists($viewPath)) {
            // Store exception in globals for error view access
            $GLOBALS['exception'] = $exception;
            include $viewPath;
        } else {
            // Fallback to generic error page
            self::renderFallbackError($statusCode, $message);
        }
    }

    /**
     * Render fallback error page (if no view file exists)
     * 
     * @param int    $statusCode
     * @param string $message
     * @return void
     */
    private static function renderFallbackError(int $statusCode, string $message): void
    {
        $title = match ($statusCode) {
            404 => 'Page Not Found',
            403 => 'Access Denied',
            default => 'Server Error',
        };

        $icon = match ($statusCode) {
            404 => '🔍',
            403 => '🔒',
            default => '⚠️',
        };

        echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$statusCode - $title</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { text-align: center; color: white; padding: 20px; }
        .error-code { font-size: 120px; font-weight: 300; margin: 0; }
        .error-title { font-size: 32px; font-weight: 600; margin: 20px 0; }
        .error-message { font-size: 18px; opacity: 0.9; margin: 20px 0 40px; }
        .error-icon { font-size: 80px; margin: 20px 0; }
        a { color: white; text-decoration: none; padding: 12px 30px; border: 2px solid white; border-radius: 4px; display: inline-block; transition: all 0.3s; margin: 10px; }
        a:hover { background: white; color: #667eea; }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">$icon</div>
        <h1 class="error-code">$statusCode</h1>
        <h2 class="error-title">$title</h2>
        <p class="error-message">$message</p>
        <a href="?url=home/index">Go Home</a>
        <a href="javascript:history.back()">Go Back</a>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Get production-safe error message by HTTP status code
     * 
     * @param int $statusCode
     * @return string
     */
    private static function getProductionMessage(int $statusCode): string
    {
        return match ($statusCode) {
            400 => 'The request could not be processed',
            403 => 'You do not have permission to access this resource',
            404 => 'The requested resource could not be found',
            409 => 'The request conflicts with the current state',
            500 => 'An internal server error occurred',
            503 => 'The server is temporarily unavailable',
            default => 'An error occurred while processing your request',
        };
    }

    /**
     * Check if debug mode is enabled
     * 
     * @return bool
     */
    private static function isDebugMode(): bool
    {
        return defined('APP_DEBUG') && APP_DEBUG === true;
    }
}

// Register handler when this file is included
ExceptionHandler::register();
