<?php

use core\Exceptions\DatabaseException;

/**
 * DepartmentRepository - Data access layer for Department model
 */
class DepartmentRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('department', Department::class);
    }

    /**
     * Get all departments.
     *
     * @return array
     * @throws DatabaseException
     */
    public function findAll(array $columns = ['*'], int $limit = 0, int $offset = 0): array
    {
        $conn = $this->getConnection();
        $cols = implode(', ', $columns);
        
        $query = "SELECT {$cols} FROM {$this->table} ORDER BY department_name ASC";

        if ($limit > 0) {
            $query .= " LIMIT {$limit}";
            if ($offset > 0) {
                $query .= " OFFSET {$offset}";
            }
        }

        try {
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
     * Count total departments.
     *
     * @return int
     * @throws DatabaseException
     */
    public function getCount(): int
    {
        return $this->count();
    }

    /**
     * Search departments by name.
     *
     * @param string $query
     * @return array
     * @throws DatabaseException
     */
    public function search(string $query): array
    {
        $conn = $this->getConnection();
        $searchTerm = '%' . $conn->real_escape_string($query) . '%';

        $sql = "
            SELECT * FROM {$this->table}
            WHERE department_name LIKE ?
            ORDER BY department_name ASC
        ";

        try {
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                throw new DatabaseException($conn->error, $sql);
            }

            $stmt->bind_param('s', $searchTerm);
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
            throw new DatabaseException($e->getMessage(), $sql);
        }
    }
}
