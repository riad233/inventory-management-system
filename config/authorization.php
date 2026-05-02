<?php
/**
 * Authorization Helper Class
 *
 * The Router enforces the permission-based ACL on every request (first line of defence).
 * These helpers are a second, in-controller defence layer for sensitive operations.
 *
 * Roles:
 *   SuperAdmin – full system access; manages permissions for other roles
 *   Admin      – configurable via SuperAdmin permission matrix
 *   Manager    – configurable via SuperAdmin permission matrix
 *   Employee   – configurable via SuperAdmin permission matrix (minimal defaults)
 */

class AuthorizationHelper {

    public static function getRole(): string {
        return (string)($_SESSION['role'] ?? '');
    }

    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }

    public static function isSuperAdmin(): bool {
        return self::getRole() === 'SuperAdmin';
    }

    public static function hasRole(string $role): bool {
        return self::getRole() === $role;
    }

    public static function hasAnyRole(array $roles): bool {
        return in_array(self::getRole(), $roles, true);
    }

    /**
     * Check if the current user's role has a specific permission.
     * SuperAdmin always returns true. Queries the DB (cached per request).
     */
    public static function hasPermission(string $permissionKey): bool {
        $role = self::getRole();

        if ($role === 'SuperAdmin') {
            return true;
        }

        global $conn;
        static $cache = [];

        if (!isset($cache[$role])) {
            if (!$conn) {
                require_once ROOT_PATH . '/config/database.php';
                global $conn;
            }
            $stmt = $conn->prepare(
                "SELECT permission_key FROM role_permissions WHERE role = ?"
            );
            if (!$stmt) {
                $cache[$role] = [];
            } else {
                $stmt->bind_param('s', $role);
                $stmt->execute();
                $rows        = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                $cache[$role] = array_column($rows, 'permission_key');
            }
        }

        return in_array($permissionKey, $cache[$role], true);
    }

    /**
     * Abort with 403 unless the current user has Admin or SuperAdmin role.
     */
    public static function requireAdmin(): void {
        if (!self::hasAnyRole(['Admin', 'SuperAdmin'])) {
            self::deny('Admin role required');
        }
    }

    /**
     * Abort with 403 unless the current user's role is in $roles
     * or is SuperAdmin.
     */
    public static function requireRole(array $roles): void {
        if (!self::isSuperAdmin() && !self::hasAnyRole($roles)) {
            self::deny('Required role: ' . implode(' or ', $roles));
        }
    }

    /**
     * Abort with 403 unless the current user has the given permission.
     */
    public static function requirePermission(string $permissionKey): void {
        if (!self::hasPermission($permissionKey)) {
            self::deny("Permission required: $permissionKey");
        }
    }

    // ── Internal ─────────────────────────────────────────────────────

    private static function deny(string $reason = ''): void {
        Logger::warning('Authorization denied: ' . $reason, [
            'user_id' => self::getUserId(),
            'role'    => self::getRole(),
        ]);
        http_response_code(403);
        $view = 'errors/403';
        $data = ['title' => '403 – Access Denied'];
        require ROOT_PATH . '/app/views/layout.php';
        exit;
    }
}
?>
