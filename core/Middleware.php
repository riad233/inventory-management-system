<?php
/**
 * Middleware – RBAC enforcement layer
 *
 * The Router already performs a full permission check on every request
 * (first line of defence).  Middleware is the *second* line: controllers
 * call it to double-check sensitive actions without duplicating logic.
 *
 * Roles (managed via SuperAdmin → Permissions):
 *   SuperAdmin – bypasses every check; manages permission matrix
 *   Admin      – configurable; full access by default
 *   Staff      – configurable; can add limited data (assets, vendors, etc.)
 *   Employee   – configurable; view + submit requests only
 *
 * Usage inside a controller method:
 *   Middleware::auth();                          // Must be logged in
 *   Middleware::role('Admin', 'Staff');          // Must have one of these roles
 *   Middleware::permission('asset.create');      // Must own this permission
 *
 * Usage inside a view (boolean check, never aborts):
 *   if (Middleware::can('asset.delete')) { ... }
 *   if (Middleware::hasRole('Admin', 'SuperAdmin')) { ... }
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

require_once ROOT_PATH . '/config/authorization.php';

class Middleware
{
    // The role name that bypasses all checks
    private const SUPERADMIN = 'SuperAdmin';

    // ── Public guards (abort on failure) ─────────────────────────────

    /**
     * Require an active authenticated session.
     * Redirects to the login page if no session exists.
     */
    public static function auth(): void
    {
        if (empty($_SESSION['username'])) {
            $base = defined('APP_BASE') ? APP_BASE : '';
            header('Location: ' . $base . '/auth/login');
            exit;
        }
    }

    /**
     * Require the current user to have one of the given roles.
     * SuperAdmin always passes.
     * Aborts with HTTP 403 if the role does not match.
     *
     * @param string ...$roles  One or more allowed role names.
     */
    public static function role(string ...$roles): void
    {
        self::auth();

        $current = (string)($_SESSION['role'] ?? '');

        if ($current === self::SUPERADMIN) {
            return;
        }

        if (!in_array($current, $roles, true)) {
            self::deny('Role required: ' . implode(' or ', $roles));
        }
    }

    /**
     * Require the current user's role to own a specific permission key.
     * SuperAdmin always passes.
     * Aborts with HTTP 403 if the permission is absent.
     *
     * @param string $permKey  e.g. 'asset.create', 'request.approve'
     */
    public static function permission(string $permKey): void
    {
        self::auth();

        if (!AuthorizationHelper::hasPermission($permKey)) {
            self::deny('Permission required: ' . $permKey);
        }
    }

    // ── Public boolean checks (never abort) ──────────────────────────

    /**
     * Return true if the current user's role has the given permission.
     * SuperAdmin always returns true.  Returns false when not logged in.
     *
     * @param string $permKey  e.g. 'asset.delete'
     */
    public static function can(string $permKey): bool
    {
        if (empty($_SESSION['username'])) {
            return false;
        }
        return AuthorizationHelper::hasPermission($permKey);
    }

    /**
     * Return true if the current user has one of the given roles.
     * SuperAdmin always returns true.  Returns false when not logged in.
     *
     * @param string ...$roles  One or more role names.
     */
    public static function hasRole(string ...$roles): bool
    {
        if (empty($_SESSION['username'])) {
            return false;
        }

        $current = (string)($_SESSION['role'] ?? '');

        if ($current === self::SUPERADMIN) {
            return true;
        }

        return in_array($current, $roles, true);
    }

    /**
     * Return the current user's role string, or '' if not logged in.
     */
    public static function getRole(): string
    {
        return (string)($_SESSION['role'] ?? '');
    }

    // ── Internal ─────────────────────────────────────────────────────

    /**
     * Log the denial and render a 403 page, then halt.
     */
    private static function deny(string $reason): void
    {
        if (class_exists('Logger')) {
            Logger::warning('Middleware denied: ' . $reason, [
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'role'    => $_SESSION['role']    ?? 'unknown',
                'url'     => $_GET['url'] ?? '',
            ]);
        }

        http_response_code(403);
        $view = 'errors/403';
        $data = ['title' => '403 – Access Denied'];
        require ROOT_PATH . '/app/views/layout.php';
        exit;
    }
}
