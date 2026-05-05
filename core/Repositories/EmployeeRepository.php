<?php

use core\Exceptions\DatabaseException;

/**
 * EmployeeRepository - Data access layer for Employee model
 */
class EmployeeRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('employee', Employee::class);
    }

    /**
     * Get recent employees.
     *
     * @param int $limit Number of records
     * @return array Employee instances
     * @throws DatabaseException
     */
    public function getRecent(int $limit = 10): array
    {
        return $this->findAll(limit: $limit);
    }

    /**
     * Count total employees.
     *
     * @return int
     * @throws DatabaseException
     */
    public function getCount(): int
    {
        return $this->count();
    }

    /**
     * Get employees by department.
     *
     * @param int $departmentId
     * @return array Employee instances
     * @throws DatabaseException
     */
    public function findByDepartment(int $departmentId): array
    {
        return $this->findWhere(['department_id' => $departmentId]);
    }

    /**
     * Get employees by status.
     *
     * @param string $status (active, inactive, on_leave, etc.)
     * @return array
     * @throws DatabaseException
     */
    public function findByStatus(string $status): array
    {
        return $this->findWhere(['status' => $status]);
    }

    /**
     * Search employees by name or email.
     *
     * @param string $query Search term
     * @return array Employee instances
     * @throws DatabaseException
     */
    public function search(string $query): array
    {
        $conn = $this->getConnection();
        $searchTerm = '%' . $conn->real_escape_string($query) . '%';

        $sql = "
            SELECT * FROM {$this->table}
            WHERE first_name LIKE ? OR last_name LIKE ? OR email LIKE ?
            ORDER BY first_name ASC
        ";

        try {
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                throw new DatabaseException($conn->error, $sql);
            }

            $stmt->bind_param('sss', $searchTerm, $searchTerm, $searchTerm);
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
