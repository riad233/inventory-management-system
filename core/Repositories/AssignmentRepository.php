<?php

use core\Exceptions\DatabaseException;

/**
 * AssignmentRepository - Data access layer for Assignment model
 */
class AssignmentRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('assignment', Assignment::class);
    }

    /**
     * Get recent assignments.
     *
     * @param int $limit
     * @return array Assignment instances
     * @throws DatabaseException
     */
    public function getRecent(int $limit = 10): array
    {
        return $this->findAll(limit: $limit);
    }

    /**
     * Count total assignments.
     *
     * @return int
     * @throws DatabaseException
     */
    public function getCount(): int
    {
        return $this->count();
    }

    /**
     * Count pending assignments (not yet returned).
     *
     * @return int
     * @throws DatabaseException
     */
    public function getPendingCount(): int
    {
        return $this->count(['return_date' => null]);
    }

    /**
     * Get pending assignments (not yet returned).
     *
     * @return array
     * @throws DatabaseException
     */
    public function getPending(): array
    {
        $conn = $this->getConnection();
        $query = "SELECT * FROM {$this->table} WHERE return_date IS NULL ORDER BY assignment_date DESC";

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
     * Get assignments for an employee.
     *
     * @param int $employeeId
     * @return array
     * @throws DatabaseException
     */
    public function findByEmployee(int $employeeId): array
    {
        return $this->findWhere(['employee_id' => $employeeId]);
    }

    /**
     * Get assignments for an asset.
     *
     * @param int $assetId
     * @return array
     * @throws DatabaseException
     */
    public function findByAsset(int $assetId): array
    {
        return $this->findWhere(['asset_id' => $assetId]);
    }
}
