<?php

use core\Exceptions\DatabaseException;

/**
 * PermissionRepository - Data access layer for Permission model
 */
class PermissionRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('permission', Permission::class);
    }

    /**
     * Get all permissions.
     *
     * @return array
     * @throws DatabaseException
     */
    public function findAll(array $columns = ['*'], int $limit = 0, int $offset = 0): array
    {
        $conn = $this->getConnection();
        $cols = implode(', ', $columns);
        
        $query = "SELECT {$cols} FROM {$this->table} ORDER BY permission_key ASC";

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
     * Find permission by key.
     *
     * @param string $key
     * @return object|null
     * @throws DatabaseException
     */
    public function findByKey(string $key): ?object
    {
        $results = $this->findWhere(['permission_key' => $key]);
        return $results[0] ?? null;
    }

    /**
     * Count total permissions.
     *
     * @return int
     * @throws DatabaseException
     */
    public function getCount(): int
    {
        return $this->count();
    }

    /**
     * Get permissions for a role.
     * Note: This queries a join table (role_permission or similar)
     *
     * @param int $roleId
     * @return array
     * @throws DatabaseException
     */
    public function findByRole(int $roleId): array
    {
        $conn = $this->getConnection();
        
        // This assumes a role_permission join table exists
        // Adjust if your schema differs
        $query = "
            SELECT p.* FROM {$this->table} p
            INNER JOIN role_permission rp ON p.id = rp.permission_id
            WHERE rp.role_id = ?
            ORDER BY p.permission_key ASC
        ";

        try {
            $stmt = $conn->prepare($query);

            if (!$stmt) {
                throw new DatabaseException($conn->error, $query);
            }

            $stmt->bind_param('i', $roleId);
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

    /**
     * Search permissions by key or description.
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
            WHERE permission_key LIKE ? OR description LIKE ?
            ORDER BY permission_key ASC
        ";

        try {
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                throw new DatabaseException($conn->error, $sql);
            }

            $stmt->bind_param('ss', $searchTerm, $searchTerm);
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
