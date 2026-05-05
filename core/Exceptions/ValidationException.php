<?php
/**
 * Validation Exception
 * 
 * Thrown when user input validation fails
 * HTTP 400 Bad Request
 */

class ValidationException extends AppException
{
    protected $statusCode = 400;

    /**
     * Validation errors by field
     * 
     * @var array
     */
    private $errors = [];

    /**
     * Create validation exception
     * 
     * @param array $errors Field => Message map
     */
    public function __construct(array $errors = [])
    {
        $this->errors = $errors;
        $message = 'Validation failed: ' . implode(', ', array_keys($errors));
        $userMessage = 'Please check the form for errors';
        
        parent::__construct($message, 0, $userMessage, ['errors' => $errors]);
    }

    /**
     * Get validation errors
     * 
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if a field has an error
     * 
     * @param string $field
     * @return bool
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    /**
     * Get error for a field
     * 
     * @param string $field
     * @return string|null
     */
    public function getError(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }
}
