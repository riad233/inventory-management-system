<?php

use core\Exceptions\DatabaseException;

/**
 * UserRepository - Data access layer for User model
 */
class UserRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct('users', User::class);
    }

    /**
     * Find user by username.
     *
     * @param string $username
     * @return object|null User instance or null
     * @throws DatabaseException
     */
    public function findByUsername(string $username): ?object
    {
        return $this->findWhere(['username' => $username])[0] ?? null;
    }

    /**
     * Find user by email.
     *
     * @param string $email
     * @return object|null
     * @throws DatabaseException
     */
    public function findByEmail(string $email): ?object
    {
        $results = $this->findWhere(['email' => $email]);
        return $results[0] ?? null;
    }

    /**
     * Get users by role.
     *
     * @param string $role
     * @return array
     * @throws DatabaseException
     */
    public function findByRole(string $role): array
    {
        return $this->findWhere(['role' => $role]);
    }

    /**
     * Count total users.
     *
     * @return int
     * @throws DatabaseException
     */
    public function getCount(): int
    {
        return $this->count();
    }

    /**
     * Check if username exists.
     *
     * @param string $username
     * @return bool
     * @throws DatabaseException
     */
    public function usernameExists(string $username): bool
    {
        $conn = $this->getConnection();
        $query = "SELECT 1 FROM {$this->table} WHERE username = ? LIMIT 1";

        try {
            $stmt = $conn->prepare($query);

            if (!$stmt) {
                throw new DatabaseException($conn->error, $query);
            }

            $stmt->bind_param('s', $username);
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
     * Check if email exists.
     *
     * @param string $email
     * @return bool
     * @throws DatabaseException
     */
    public function emailExists(string $email): bool
    {
        $conn = $this->getConnection();
        $query = "SELECT 1 FROM {$this->table} WHERE email = ? LIMIT 1";

        try {
            $stmt = $conn->prepare($query);

            if (!$stmt) {
                throw new DatabaseException($conn->error, $query);
            }

            $stmt->bind_param('s', $email);
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
     * Search users by username or email.
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
            WHERE username LIKE ? OR email LIKE ?
            ORDER BY username ASC
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
