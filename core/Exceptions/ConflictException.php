<?php
/**
 * Conflict Exception
 * 
 * Thrown when operation conflicts with current state
 * HTTP 409 Conflict
 * Examples: Duplicate key, resource already exists, state conflict
 */

class ConflictException extends AppException
{
    protected $statusCode = 409;

    /**
     * Resource that caused the conflict
     * 
     * @var string
     */
    private $resource;

    /**
     * Conflict reason
     * 
     * @var string
     */
    private $reason;

    /**
     * Create conflict exception
     * 
     * @param string $resource Resource causing conflict
     * @param string $reason   Reason for the conflict
     */
    public function __construct(string $resource = '', string $reason = '')
    {
        $this->resource = $resource;
        $this->reason = $reason;

        $message = "Conflict: {$resource} - {$reason}";
        $userMessage = 'This operation cannot be completed. ' . $reason;

        parent::__construct(
            $message,
            0,
            $userMessage,
            [
                'resource' => $resource,
                'reason' => $reason,
            ]
        );
    }

    /**
     * Get conflicting resource
     * 
     * @return string
     */
    public function getResource(): string
    {
        return $this->resource;
    }

    /**
     * Get conflict reason
     * 
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }
}
