<?php
/**
 * Authorization Exception
 * 
 * Thrown when user lacks required permissions
 * HTTP 403 Forbidden
 */

class AuthorizationException extends AppException
{
    protected $statusCode = 403;

    /**
     * Required permission or role
     * 
     * @var string
     */
    private $required;

    /**
     * User's current role
     * 
     * @var string
     */
    private $userRole;

    /**
     * Create authorization exception
     * 
     * @param string $required   Required permission/role
     * @param string $userRole   User's current role
     * @param string $userMessage User-friendly message (optional)
     */
    public function __construct(
        string $required = 'permission denied',
        string $userRole = 'guest',
        string $userMessage = ''
    ) {
        $this->required = $required;
        $this->userRole = $userRole;

        $message = "Authorization denied. Required: {$required}, User role: {$userRole}";
        $userMessage = $userMessage ?: 'You do not have permission to access this resource';

        parent::__construct(
            $message,
            0,
            $userMessage,
            [
                'required' => $required,
                'user_role' => $userRole,
            ]
        );
    }

    /**
     * Get required permission/role
     * 
     * @return string
     */
    public function getRequired(): string
    {
        return $this->required;
    }

    /**
     * Get user's role
     * 
     * @return string
     */
    public function getUserRole(): string
    {
        return $this->userRole;
    }
}
