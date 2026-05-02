<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . '/core/Model.php';

class Permission extends Model {

    protected $table = 'permissions';

    // ── Per-request cache so the DB is only queried once ─────────────
    private static array $cache = [];

    /**
     * Return all permissions, grouped by module.
     * [ 'Assets' => [ ['permission_key'=>..., 'label'=>..., ...], ... ], ... ]
     */
    public function getAllGrouped(): array {
        $sql    = "SELECT * FROM permissions ORDER BY module, label";
        $stmt   = $this->conn->prepare($sql);
        $stmt->execute();
        $rows   = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['module']][] = $row;
        }
        return $grouped;
    }

    /**
     * Return a flat array of all permission_key values.
     */
    public function getAllKeys(): array {
        $sql  = "SELECT permission_key FROM permissions ORDER BY module, permission_key";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return array_column($rows, 'permission_key');
    }

    /**
     * Return a flat array of permission_key values that $role has.
     * Result is cached for the lifetime of the request.
     */
    public function getKeysForRole(string $role): array {
        if ($role === 'SuperAdmin') {
            return ['*'];          // SuperAdmin has everything
        }

        if (isset(self::$cache[$role])) {
            return self::$cache[$role];
        }

        $sql  = "SELECT permission_key FROM role_permissions WHERE role = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('s', $role);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        self::$cache[$role] = array_column($rows, 'permission_key');
        return self::$cache[$role];
    }

    /**
     * Return a map of   role → [permission_key, ...]
     * for all non-SuperAdmin roles that have any entry in role_permissions.
     */
    public function getAllRolePermissions(): array {
        $sql  = "SELECT role, permission_key FROM role_permissions ORDER BY role, permission_key";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        $map = [];
        foreach ($rows as $row) {
            $map[$row['role']][] = $row['permission_key'];
        }
        return $map;
    }

    /**
     * Replace all permissions for $role with the given $keys array.
     * Called by SuperAdmin when saving the permission matrix.
     */
    public function setRolePermissions(string $role, array $keys): bool {
        // Prevent modifying SuperAdmin programmatically
        if ($role === 'SuperAdmin') {
            return false;
        }

        $this->conn->begin_transaction();
        try {
            // Delete existing
            $del  = $this->conn->prepare("DELETE FROM role_permissions WHERE role = ?");
            $del->bind_param('s', $role);
            $del->execute();
            $del->close();

            // Re-insert
            if (!empty($keys)) {
                $ins = $this->conn->prepare(
                    "INSERT IGNORE INTO role_permissions (role, permission_key) VALUES (?, ?)"
                );
                foreach ($keys as $key) {
                    $ins->bind_param('ss', $role, $key);
                    $ins->execute();
                }
                $ins->close();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            Logger::error('Permission::setRolePermissions failed', [
                'role'  => $role,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Quick check: does $role have $key?
     */
    public function roleHas(string $role, string $key): bool {
        if ($role === 'SuperAdmin') {
            return true;
        }
        return in_array($key, $this->getKeysForRole($role), true);
    }

    /** Invalidate per-request cache (useful after setRolePermissions) */
    public static function clearCache(): void {
        self::$cache = [];
    }
}
