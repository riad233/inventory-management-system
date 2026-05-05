<?php
/**
 * Not Found Exception
 * 
 * Thrown when a requested resource is not found
 * HTTP 404 Not Found
 */

class NotFoundException extends AppException
{
    protected $statusCode = 404;

    /**
     * Resource type that was not found
     * 
     * @var string
     */
    private $resourceType;

    /**
     * Resource identifier (ID, name, etc.)
     * 
     * @var mixed
     */
    private $resourceId;

    /**
     * Create not found exception
     * 
     * @param string $resourceType Type of resource (e.g., 'Asset', 'Employee')
     * @param mixed  $resourceId   ID or identifier of the resource
     */
    public function __construct(string $resourceType = 'Resource', $resourceId = null)
    {
        $this->resourceType = $resourceType;
        $this->resourceId = $resourceId;

        if ($resourceId !== null) {
            $message = "{$resourceType} with ID {$resourceId} not found";
        } else {
            $message = "{$resourceType} not found";
        }

        $userMessage = 'The requested resource could not be found';

        parent::__construct(
            $message,
            0,
            $userMessage,
            [
                'resource_type' => $resourceType,
                'resource_id' => $resourceId,
            ]
        );
    }

    /**
     * Get resource type
     * 
     * @return string
     */
    public function getResourceType(): string
    {
        return $this->resourceType;
    }

    /**
     * Get resource ID
     * 
     * @return mixed
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }
}
