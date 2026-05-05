<?php

use core\Exceptions\DatabaseException;

/**
 * EquipmentRequestRepository - Data access layer for EquipmentRequest model
 */
class EquipmentRequestRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('request', EquipmentRequest::class);
    }

    /**
     * Get recent requests.
     *
     * @param int $limit
     * @return array
     * @throws DatabaseException
     */
    public function getRecent(int $limit = 10): array
    {
        return $this->findAll(limit: $limit);
    }

    /**
     * Count total requests.
     *
     * @return int
     * @throws DatabaseException
     */
    public function getCount(): int
    {
        return $this->count();
    }

    /**
     * Get pending requests (not yet approved/rejected).
     *
     * @return array
     * @throws DatabaseException
     */
    public function getPending(): array
    {
        $conn = $this->getConnection();
        $query = "SELECT * FROM {$this->table} WHERE status = 'pending' ORDER BY request_date DESC";

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
     * Get requests by status.
     *
     * @param string $status (pending, approved, rejected, etc.)
     * @return array
     * @throws DatabaseException
     */
    public function findByStatus(string $status): array
    {
        return $this->findWhere(['status' => $status]);
    }

    /**
     * Get requests by employee.
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
     * Count pending requests.
     *
     * @return int
     * @throws DatabaseException
     */
    public function countPending(): int
    {
        $conn = $this->getConnection();
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE status = 'pending'";

        try {
            $result = $conn->query($query);

            if (!$result) {
                throw new DatabaseException($conn->error, $query);
            }

            $row = $result->fetch_assoc();
            return (int)$row['count'];
        } catch (DatabaseException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new DatabaseException($e->getMessage(), $query);
        }
    }
}
