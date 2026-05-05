<?php

use core\Exceptions\{ValidationException, DatabaseException, ConflictException};

/**
 * BaseService - Abstract base class for all service classes
 *
 * Services encapsulate business logic and orchestrate repositories.
 * They validate input, enforce business rules, and coordinate data operations.
 *
 * Usage:
 *   class AssetService extends BaseService {
 *       public function __construct() {
 *           parent::__construct(RepositoryFactory::assets());
 *       }
 *       
 *       public function createAsset(array $data) {
 *           // Validate
 *           $this->validateAssetData($data);
 *           // Create
 *           return $this->repository->create($data);
 *       }
 *   }
 */
abstract class BaseService
{
    protected $repository = null;
    protected ?\mysqli $connection = null;

    /**
     * Initialize service with its repository.
     *
     * @param BaseRepository $repository The data access object
     */
    protected function __construct($repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get database connection for transactions.
     *
     * @return mysqli
     * @throws DatabaseException
     */
    protected function getConnection(): \mysqli
    {
        if ($this->connection === null) {
            $this->connection = require ROOT_PATH . '/config/database.php';
        }
        return $this->connection;
    }

    /**
     * Begin database transaction.
     * Used for multi-step operations that must succeed or fail together.
     *
     * @throws DatabaseException
     */
    protected function beginTransaction(): void
    {
        try {
            $conn = $this->getConnection();
            $conn->begin_transaction();
            Logger::debug('Transaction started');
        } catch (\Exception $e) {
            throw new DatabaseException('Failed to start transaction', '');
        }
    }

    /**
     * Commit database transaction.
     *
     * @throws DatabaseException
     */
    protected function commitTransaction(): void
    {
        try {
            $conn = $this->getConnection();
            $conn->commit();
            Logger::debug('Transaction committed');
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw new DatabaseException('Failed to commit transaction', '');
        }
    }

    /**
     * Rollback database transaction.
     *
     * @throws DatabaseException
     */
    protected function rollbackTransaction(): void
    {
        try {
            $conn = $this->getConnection();
            $conn->rollback();
            Logger::debug('Transaction rolled back');
        } catch (\Exception $e) {
            throw new DatabaseException('Failed to rollback transaction', '');
        }
    }

    /**
     * Execute a callback within a transaction.
     * Automatically commits on success, rolls back on exception.
     *
     * @param callable $callback Function to execute
     * @return mixed Result of callback
     * @throws DatabaseException|ValidationException|ConflictException
     */
    protected function transaction(callable $callback)
    {
        $this->beginTransaction();
        try {
            $result = $callback();
            $this->commitTransaction();
            return $result;
        } catch (\Exception $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * Validate required fields are present and non-empty.
     *
     * @param array $data Input data
     * @param array $required Required field names
     * @throws ValidationException If any required field is missing or empty
     */
    protected function validateRequired(array $data, array $required): void
    {
        $errors = [];

        foreach ($required as $field) {
            if (empty($data[$field] ?? null)) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    /**
     * Validate data against rules.
     *
     * @param array $data Input data
     * @param array $rules Field => rule name mappings
     * @throws ValidationException If validation fails
     *
     * Example: $this->validate($data, [
     *     'email' => 'email',
     *     'password' => 'min:8',
     *     'age' => 'integer|min:18'
     * ])
     */
    protected function validate(array $data, array $rules): void
    {
        $validator = new Validator();
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;

            // Parse rules (e.g., "min:8" -> ["min", "8"])
            $ruleList = explode('|', $fieldRules);

            foreach ($ruleList as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleParam = $ruleParts[1] ?? null;

                // Call validator method
                $method = 'validate' . ucfirst($ruleName);

                if (method_exists($validator, $method)) {
                    $isValid = $ruleParam
                        ? $validator->$method($value, $ruleParam)
                        : $validator->$method($value);

                    if (!$isValid) {
                        $errors[$field] = $this->getValidationMessage($field, $ruleName, $ruleParam);
                    }
                }
            }
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }
    }

    /**
     * Get a validation error message.
     *
     * @param string $field Field name
     * @param string $rule Rule name
     * @param mixed $param Rule parameter
     * @return string Error message
     */
    protected function getValidationMessage(string $field, string $rule, $param = null): string
    {
        $messages = [
            'required' => ucfirst($field) . ' is required',
            'email' => ucfirst($field) . ' must be a valid email',
            'min' => ucfirst($field) . ' must be at least ' . $param . ' characters',
            'max' => ucfirst($field) . ' must not exceed ' . $param . ' characters',
            'integer' => ucfirst($field) . ' must be an integer',
            'numeric' => ucfirst($field) . ' must be numeric',
            'unique' => ucfirst($field) . ' is already taken',
            'exists' => ucfirst($field) . ' does not exist',
            'date' => ucfirst($field) . ' must be a valid date',
            'future' => ucfirst($field) . ' must be in the future',
            'past' => ucfirst($field) . ' must be in the past',
        ];

        return $messages[$rule] ?? 'Invalid ' . $field;
    }

    /**
     * Check if a value already exists (for uniqueness validation).
     *
     * @param array $where WHERE conditions
     * @return bool True if exists
     * @throws DatabaseException
     */
    protected function exists(array $where): bool
    {
        try {
            return $this->repository->count($where) > 0;
        } catch (DatabaseException $e) {
            throw $e;
        }
    }

    /**
     * Log important business event.
     *
     * @param string $action Action description
     * @param array $context Additional context
     */
    protected function logAction(string $action, array $context = []): void
    {
        Logger::info($action, array_merge($context, [
            'user_id' => $_SESSION['user_id'] ?? 'unknown',
            'username' => $_SESSION['username'] ?? 'unknown',
        ]));
    }

    /**
     * Get cache key for this service.
     *
     * @param string $action Action name
     * @param array $params Parameters
     * @return string Cache key
     */
    protected function getCacheKey(string $action, array $params = []): string
    {
        $entity = strtolower(str_replace('Service', '', static::class));
        return CacheKeyGenerator::generate($entity, $action, $params);
    }

    /**
     * Cache a value with service-specific key.
     *
     * @param string $action Action name
     * @param mixed $value Value to cache
     * @param int|null $ttl Time to live
     * @param array $params Optional parameters
     * @return void
     */
    protected function cache(string $action, $value, ?int $ttl = null, array $params = []): void
    {
        $key = $this->getCacheKey($action, $params);
        CacheManager::getInstance()->set($key, $value, $ttl ?? 3600);
    }

    /**
     * Remember value from cache or generate.
     *
     * @param string $action Action name
     * @param callable $callback Generator function
     * @param int|null $ttl Time to live
     * @param array $params Optional parameters
     * @return mixed
     */
    protected function remember(string $action, callable $callback, ?int $ttl = null, array $params = [])
    {
        $key = $this->getCacheKey($action, $params);
        return CacheManager::getInstance()->remember($key, $callback, $ttl ?? 3600);
    }

    /**
     * Get entity name for this service.
     *
     * @return string
     */
    protected function getEntityName(): string
    {
        return strtolower(str_replace('Service', '', static::class));
    }

    /**
     * Invalidate cache on create.
     *
     * @return void
     */
    protected function invalidateOnCreate(): void
    {
        CacheInvalidator::onCreated($this->getEntityName());
    }

    /**
     * Invalidate cache on update.
     *
     * @param int $id Entity ID
     * @return void
     */
    protected function invalidateOnUpdate(int $id): void
    {
        CacheInvalidator::onUpdated($this->getEntityName(), $id);
    }

    /**
     * Invalidate cache on delete.
     *
     * @param int $id Entity ID
     * @return void
     */
    protected function invalidateOnDelete(int $id): void
    {
        CacheInvalidator::onDeleted($this->getEntityName(), $id);
    }

    /**
     * Get the repository instance.
     *
     * @return BaseRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Get repository for eager loading related data.
     * Useful for N+1 optimization.
     *
     * @param string $repositoryName Repository method name (e.g., 'assets', 'employees')
     * @return BaseRepository
     */
    protected function related(string $repositoryName)
    {
        return RepositoryFactory::$repositoryName();
    }
}
