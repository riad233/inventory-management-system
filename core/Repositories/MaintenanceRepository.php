<?php

use core\Exceptions\DatabaseException;

/**
 * MaintenanceRepository - Data access layer for Maintenance model
 */
class MaintenanceRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('maintenance', Maintenance::class);
    }

    /**
     * Get recent maintenance records.
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
     * Count pending maintenance (not yet completed).
     *
     * @return int
     * @throws DatabaseException
     */
    public function getPendingCount(): int
    {
        $conn = $this->getConnection();
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE status != 'completed'";

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

    /**
     * Get pending maintenance records.
     *
     * @return array
     * @throws DatabaseException
     */
    public function getPending(): array
    {
        $conn = $this->getConnection();
        $query = "SELECT * FROM {$this->table} WHERE status != 'completed' ORDER BY scheduled_date ASC";

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
     * Get maintenance records for an asset.
     *
     * @param int $assetId
     * @return array
     * @throws DatabaseException
     */
    public function findByAsset(int $assetId): array
    {
        return $this->findWhere(['asset_id' => $assetId]);
    }

    /**
     * Get maintenance by status.
     *
     * @param string $status
     * @return array
     * @throws DatabaseException
     */
    public function findByStatus(string $status): array
    {
        return $this->findWhere(['status' => $status]);
    }
}
