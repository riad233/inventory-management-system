<?php
/**
 * Database Exception
 * 
 * Thrown when database operations fail
 * HTTP 500 Internal Server Error
 */

class DatabaseException extends AppException
{
    protected $statusCode = 500;

    /**
     * Database error message
     * 
     * @var string
     */
    private $dbError;

    /**
     * SQL query (if available, not exposed to users)
     * 
     * @var string
     */
    private $query;

    /**
     * Create database exception
     * 
     * @param string $dbError Database error message
     * @param string $query   SQL query that failed (optional)
     */
    public function __construct(string $dbError = '', string $query = '')
    {
        $this->dbError = $dbError;
        $this->query = $query;

        $message = "Database error: {$dbError}";
        if ($query) {
            $message .= " (Query: {$query})";
        }
        $userMessage = 'A database error occurred. Please try again later';

        parent::__construct(
            $message,
            0,
            $userMessage,
            [
                'db_error' => $dbError,
                'query' => $query, // Only in logs, never to user
            ]
        );
    }

    /**
     * Get database error message
     * 
     * @return string
     */
    public function getDbError(): string
    {
        return $this->dbError;
    }

    /**
     * Get SQL query (for logging only)
     * 
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }
}
