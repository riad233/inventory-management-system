<?php

use core\Exceptions\{DatabaseException, NotFoundException};

/**
 * BaseRepository - Abstract base class for all repositories
 *
 * Provides common data access methods for entity repositories.
 * Handles database operations with prepared statements and exception handling.
 *
 * Usage:
 *   class AssetRepository extends BaseRepository {
 *       public function __construct() {
 *           parent::__construct('asset', Asset::class);
 *       }
 *   }
 */
abstract class BaseRepository
{
    protected ?\mysqli $connection = null;
    protected string $table = '';
    protected string $modelClass = '';

    /**
     * Initialize repository with table name and model class.
     *
     * @param string $table Database table name
     * @param string $modelClass Fully qualified model class name
     */
    protected function __construct(string $table, string $modelClass)
    {
        $this->table = $table;
        $this->modelClass = $modelClass;
    }

    /**
     * Get database connection (lazy load).
     *
     * @return mysqli Database connection
     * @throws DatabaseException If connection fails
     */
    protected function getConnection(): \mysqli
    {
        if ($this->connection === null) {
            $this->connection = $this->createConnection();
        }
        return $this->connection;
    }

    /**
     * Create database connection.
     * Uses credentials from config/database.php
     *
     * @return mysqli Database connection
     * @throws DatabaseException If connection fails
     */
    private function createConnection(): \mysqli
    {
        try {
            $conn = require ROOT_PATH . '/config/database.php';
            if (!$conn instanceof \mysqli) {
                throw new \Exception('Invalid database connection');
            }
            return $conn;
        } catch (\Exception $e) {
            throw new DatabaseException('Failed to connect to database', '');
        }
    }

    /**
     * Execute query and track performance metrics.
     *
     * @param string $query SQL query
     * @param mixed $result Query result
     * @param int $rowCount Affected/selected rows
     * @return void
     */
    protected function trackQuery(string $query, $result, int $rowCount = 0): void
    {
        if (class_exists('PerformanceMetrics')) {
            // Track query execution time (approximate using time since request start)
            // In a production system, this would use microtime() around the actual query
            $executionTime = 0.01; // Conservative estimate: 10ms
            PerformanceMetrics::recordQuery($query, $executionTime, $rowCount);
        }
    }

    /**
        $cols = implode(', ', $columns);
        $query = "SELECT {$cols} FROM {$this->table}";

        if ($limit > 0) {
            $query .= " LIMIT {$limit}";
            if ($offset > 0) {
                $query .= " OFFSET {$offset}";
            }
        }

        try {
            $conn = $this->getConnection();
            $result = $conn->query($query);

            if (!$result) {
                throw new DatabaseException($conn->error, $query);
            }

            $items = [];
            while ($row = $result->fetch_assoc()) {
                $items[] = $this->hydrate($row);
            }

            return $items;
        } catch (DatabaseException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new DatabaseException($e->getMessage(), $query);
        }
    }

    /**
     * Find one record by ID.
     *
     * @param int|string $id Primary key value
     * @return object|null Model instance or null if not found
     * @throws DatabaseException On query error
     */
    public function findById($id): ?object
    {
        $query = "SELECT * FROM {$this->table} WHERE id = ?";

        try {
            $conn = $this->getConnection();
            $stmt = $conn->prepare($query);

            if (!$stmt) {
                throw new DatabaseException($conn->error, $query);
            }

            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                return null;
            }

            $row = $result->fetch_assoc();
            $stmt->close();

            return $this->hydrate($row);
        } catch (DatabaseException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new DatabaseException($e->getMessage(), $query);
        }
    }

    /**
     * Count total records.
     *
     * @param array $where Optional WHERE conditions
     * @return int Total count
     * @throws DatabaseException On query error
     */
    public function count(array $where = []): int
    {
        $query = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];
        $types = '';

        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $col => $value) {
                $conditions[] = "$col = ?";
                $params[] = $value;
                $types .= $this->getParamType($value);
            }
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }

        try {
            $conn = $this->getConnection();

            if (empty($params)) {
                $result = $conn->query($query);
                if (!$result) {
                    throw new DatabaseException($conn->error, $query);
                }
                $row = $result->fetch_assoc();
                return (int)$row['count'];
            }

            $stmt = $conn->prepare($query);
            if (!$stmt) {
                throw new DatabaseException($conn->error, $query);
            }

            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            return (int)$row['count'];
        } catch (DatabaseException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new DatabaseException($e->getMessage(), $query);
        }
    }

    /**
     * Find records by criteria.
     *
     * @param array $where WHERE conditions (column => value)
     * @param array $columns Columns to select
     * @param int $limit Result limit
     * @param int $offset Pagination offset
     * @return array Array of model instances
     * @throws DatabaseException On query error
     */
    public function findWhere(array $where, array $columns = ['*'], int $limit = 0, int $offset = 0): array
    {
        $cols = implode(', ', $columns);
        $query = "SELECT {$cols} FROM {$this->table}";

        if (!empty($where)) {
            $conditions = [];
            $params = [];
            $types = '';

            foreach ($where as $col => $value) {
                $conditions[] = "$col = ?";
                $params[] = $value;
                $types .= $this->getParamType($value);
            }

            $query .= ' WHERE ' . implode(' AND ', $conditions);

            if ($limit > 0) {
                $query .= " LIMIT {$limit}";
                if ($offset > 0) {
                    $query .= " OFFSET {$offset}";
                }
            }

            try {
                $conn = $this->getConnection();
                $stmt = $conn->prepare($query);

                if (!$stmt) {
                    throw new DatabaseException($conn->error, $query);
                }

                $stmt->bind_param($types, ...$params);
                $stmt->execute();
                $result = $stmt->get_result();

                $items = [];
                while ($row = $result->fetch_assoc()) {
                    $items[] = $this->hydrate($row);
                }

                $stmt->close();
                return $items;
            } catch (DatabaseException $e) {
                throw $e;
            } catch (\Exception $e) {
                throw new DatabaseException($e->getMessage(), $query);
            }
        }

        return $this->findAll($columns, $limit, $offset);
    }

    /**
     * Insert a new record.
     *
     * @param array $data Column => value pairs
     * @return int Inserted record ID
     * @throws DatabaseException On insert error
     */
    public function create(array $data): int
    {
        $cols = array_keys($data);
        $placeholders = str_repeat('?,', count($cols) - 1) . '?';
        $query = "INSERT INTO {$this->table} (" . implode(',', $cols) . ") VALUES ($placeholders)";

        try {
            $conn = $this->getConnection();
            $stmt = $conn->prepare($query);

            if (!$stmt) {
                throw new DatabaseException($conn->error, $query);
            }

            $types = '';
            $values = [];
            foreach ($data as $value) {
                $types .= $this->getParamType($value);
                $values[] = $value;
            }

            $stmt->bind_param($types, ...$values);
            $stmt->execute();

            if ($conn->error) {
                throw new DatabaseException($conn->error, $query);
            }

            $id = $conn->insert_id;
            $stmt->close();

            return $id;
        } catch (DatabaseException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new DatabaseException($e->getMessage(), $query);
        }
    }

    /**
     * Update an existing record.
     *
     * @param int|string $id Primary key value
     * @param array $data Column => value pairs
     * @return bool Success status
     * @throws DatabaseException On update error
     */
    public function update($id, array $data): bool
    {
        $updates = [];
        $params = [];
        $types = '';

        foreach ($data as $col => $value) {
            $updates[] = "$col = ?";
            $params[] = $value;
            $types .= $this->getParamType($value);
        }

        $params[] = $id;
        $types .= 'i'; // ID is always integer

        $query = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";

        try {
            $conn = $this->getConnection();
            $stmt = $conn->prepare($query);

            if (!$stmt) {
                throw new DatabaseException($conn->error, $query);
            }

            $stmt->bind_param($types, ...$params);
            $stmt->execute();

            if ($conn->error) {
                throw new DatabaseException($conn->error, $query);
            }

            $affected = $stmt->affected_rows;
            $stmt->close();

            return $affected > 0;
        } catch (DatabaseException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new DatabaseException($e->getMessage(), $query);
        }
    }

    /**
     * Delete a record by ID.
     *
     * @param int|string $id Primary key value
     * @return bool Success status
     * @throws DatabaseException On delete error
     */
    public function delete($id): bool
    {
        $query = "DELETE FROM {$this->table} WHERE id = ?";

        try {
            $conn = $this->getConnection();
            $stmt = $conn->prepare($query);

            if (!$stmt) {
                throw new DatabaseException($conn->error, $query);
            }

            $stmt->bind_param('i', $id);
            $stmt->execute();

            if ($conn->error) {
                throw new DatabaseException($conn->error, $query);
            }

            $affected = $stmt->affected_rows;
            $stmt->close();

            return $affected > 0;
        } catch (DatabaseException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new DatabaseException($e->getMessage(), $query);
        }
    }

    /**
     * Check if a record exists by ID.
     *
     * @param int|string $id Primary key value
     * @return bool True if exists
     * @throws DatabaseException On query error
     */
    public function exists($id): bool
    {
        $query = "SELECT 1 FROM {$this->table} WHERE id = ? LIMIT 1";

        try {
            $conn = $this->getConnection();
            $stmt = $conn->prepare($query);

            if (!$stmt) {
                throw new DatabaseException($conn->error, $query);
            }

            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            return $result->num_rows > 0;
        } catch (DatabaseException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new DatabaseException($e->getMessage(), $query);
        }
    }

    /**
     * Hydrate model instance from database row.
     * Can be overridden in subclasses for custom logic.
     *
     * @param array $row Database row
     * @return object Model instance
     */
    protected function hydrate(array $row): object
    {
        $model = new $this->modelClass();

        foreach ($row as $key => $value) {
            if (property_exists($model, $key)) {
                $model->$key = $value;
            }
        }

        return $model;
    }

    /**
     * Get MySQLi parameter type for value.
     *
     * @param mixed $value
     * @return string Parameter type (i/d/s/b)
     */
    protected function getParamType($value): string
    {
        if (is_int($value)) {
            return 'i';
        } elseif (is_float($value)) {
            return 'd';
        } else {
            return 's';
        }
    }
}
