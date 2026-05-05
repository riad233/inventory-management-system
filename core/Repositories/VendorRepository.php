<?php

use core\Exceptions\DatabaseException;

/**
 * VendorRepository - Data access layer for Vendor model
 */
class VendorRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('vendor', Vendor::class);
    }

    /**
     * Get vendors with status information.
     *
     * @param int $limit
     * @param int $offset
     * @return array Vendor instances
     * @throws DatabaseException
     */
    public function findAll(array $columns = ['*'], int $limit = 0, int $offset = 0): array
    {
        $conn = $this->getConnection();
        $cols = implode(', ', $columns);
        $query = "
            SELECT {$cols} FROM {$this->table}
            LEFT JOIN vendor_status vs ON {$this->table}.vendor_status_id = vs.id
        ";

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
     * Count total vendors.
     *
     * @return int
     * @throws DatabaseException
     */
    public function getCount(): int
    {
        return $this->count();
    }

    /**
     * Get vendors by status.
     *
     * @param string $status
     * @return array
     * @throws DatabaseException
     */
    public function findByStatus(string $status): array
    {
        return $this->findWhere(['status' => $status]);
    }

    /**
     * Search vendors by name.
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
            WHERE vendor_name LIKE ? OR email LIKE ? OR contact_person LIKE ?
            ORDER BY vendor_name ASC
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
