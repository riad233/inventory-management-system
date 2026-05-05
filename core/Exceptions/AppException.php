<?php
/**
 * Base Application Exception
 * 
 * All application exceptions should extend this class.
 * Provides consistent exception handling across the application.
 */

class AppException extends Exception
{
    /**
     * HTTP status code for this exception
     * 
     * @var int
     */
    protected $statusCode = 500;

    /**
     * User-friendly error message
     * 
     * @var string
     */
    protected $userMessage;

    /**
     * Additional context data for logging
     * 
     * @var array
     */
    protected $context = [];

    /**
     * Create a new exception
     * 
     * @param string $message     Technical message for logs
     * @param int    $code        Exception code
     * @param string $userMessage User-friendly message (optional)
     * @param array  $context     Additional context data
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        string $userMessage = '',
        array $context = []
    ) {
        parent::__construct($message, $code);
        $this->userMessage = $userMessage ?: $message;
        $this->context = $context;
    }

    /**
     * Get the HTTP status code
     * 
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Get user-friendly message (safe for production)
     * 
     * @return string
     */
    public function getUserMessage(): string
    {
        return $this->userMessage;
    }

    /**
     * Get context data for logging
     * 
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Add context data
     * 
     * @param string $key
     * @param mixed  $value
     * @return void
     */
    public function addContext(string $key, $value): void
    {
        $this->context[$key] = $value;
    }

    /**
     * Render error response (can be overridden)
     * 
     * @return string HTML response
     */
    public function render(): string
    {
        return '';
    }
}
