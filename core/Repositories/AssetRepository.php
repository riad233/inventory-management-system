<?php

use core\Exceptions\DatabaseException;

/**
 * AssetRepository - Data access layer for Asset model
 * 
 * Extends BaseRepository to provide asset-specific queries:
 * - Find by status
 * - Count by status
 * - Recent assets
 * - Available assets for assignment
 */
class AssetRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('asset', Asset::class);
    }

    /**
     * Get recent assets (last N created).
     *
     * @param int $limit Number of records
     * @return array Asset instances
     * @throws DatabaseException
     */
    public function getRecent(int $limit = 10): array
    {
        return $this->findAll(limit: $limit);
    }

    /**
     * Count total assets.
     *
     * @return int Total count
     * @throws DatabaseException
     */
    public function getCount(): int
    {
        return $this->count();
    }

    /**
     * Get assets by status.
     *
     * @param string $status Asset status (active, inactive, retired, etc.)
     * @return array Asset instances
     * @throws DatabaseException
     */
    public function findByStatus(string $status): array
    {
        return $this->findWhere(['status' => $status]);
    }

    /**
     * Count assets by status.
     *
     * @param string $status
     * @return int
     * @throws DatabaseException
     */
    public function countByStatus(string $status): int
    {
        return $this->count(['status' => $status]);
    }

    /**
     * Get available assets (not yet assigned).
     *
     * @return array Asset instances
     * @throws DatabaseException
     */
    public function getAvailable(): array
    {
        $conn = $this->getConnection();
        $query = "
            SELECT a.* FROM {$this->table} a
            LEFT JOIN assignment asg ON a.id = asg.asset_id AND asg.return_date IS NULL
            WHERE asg.id IS NULL
            ORDER BY a.asset_tag ASC
        ";

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
}
