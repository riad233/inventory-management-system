<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

require_once ROOT_PATH . '/config/authorization.php';
require_once ROOT_PATH . '/core/Exceptions/NotFoundException.php';
require_once ROOT_PATH . '/core/Exceptions/AuthorizationException.php';

class Router {

    /** Route → permission_key map (loaded once per request). */
    private static ?array $permMap = null;

    // Controllers that never require authentication
    private const PUBLIC_CONTROLLERS = ['auth', 'home'];

    // The SuperAdmin role name – bypasses all permission checks
    private const SUPERADMIN_ROLE = 'SuperAdmin';

    /**
     * Role-based friendly URL aliases.
     * These short URLs redirect to the real route so bookmarks like
     * /staff always land on the correct page.
     * Auth is enforced at the destination.
     * 
     * NOTE: Do NOT add 'employee' here as it conflicts with the EmployeeController.
     */
    private const ROLE_ALIASES = [
        'staff'    => '/dashboard/index',
    ];

    public static function route(string $url): void {
        $parts = array_values(array_filter(explode('/', trim($url, '/'))));

        if (empty($parts)) {
            $parts = ['dashboard', 'index'];
        }

        $controllerKey  = strtolower($parts[0]);
        $controllerName = ucfirst($parts[0]) . 'Controller';
        $methodOriginal = $parts[1] ?? 'index';   // preserve original casing for dispatch
        $method         = strtolower($methodOriginal);
        $params         = array_slice($parts, 2);
        $routeKey       = $controllerKey . '/' . $method;

        // ── 0a. Role-based URL aliases (/staff, /employee) ──────────────────
        // Resolve before file-existence check so they never generate a 404.
        if (isset(self::ROLE_ALIASES[$controllerKey])) {
            $base = defined('APP_BASE') ? APP_BASE : '';
            header('Location: ' . $base . self::ROLE_ALIASES[$controllerKey]);
            exit;
        }

        // ── 0b. Validate controller + method exist BEFORE any auth redirect ──
        // Invalid routes always get a 404 – never a login redirect.
        $controllerPath = ROOT_PATH . "/app/controllers/$controllerName.php";

        if (!file_exists($controllerPath)) {
            throw new NotFoundException('Route', $routeKey);
        }

        require_once $controllerPath;

        if (!class_exists($controllerName)) {
            throw new NotFoundException('Controller', $controllerName);
        }

        if (!method_exists($controllerName, $methodOriginal)) {
            throw new NotFoundException('Route', $routeKey);
        }

        // ── 1. Public routes ─────────────────────────────────────────
        if (!in_array($controllerKey, self::PUBLIC_CONTROLLERS, true)) {

            // ── Block: /superadmin is retired, redirect all users to /admin ─
            if ($controllerKey === 'superadmin') {
                $base = defined('APP_BASE') ? APP_BASE : '';
                header('Location: ' . $base . '/admin/index');
                exit;
            }

            // ── 2. Must be authenticated ─────────────────────────────
            if (empty($_SESSION['username'])) {
                $base = defined('APP_BASE') ? APP_BASE : '';
                header('Location: ' . $base . '/auth/login');
                exit;
            }

            $role = (string)($_SESSION['role'] ?? '');

            // ── 3. SuperAdmin bypasses everything ────────────────────
            if ($role !== self::SUPERADMIN_ROLE) {

                // ── 4. Load route→permission map ─────────────────────
                if (self::$permMap === null) {
                    self::$permMap = require ROOT_PATH . '/config/permissions_map.php';
                }

                // Routes not in the map are SuperAdmin-only by default
                if (!array_key_exists($routeKey, self::$permMap)) {
                    throw new AuthorizationException('SuperAdmin role required', $role);
                }

                $requiredPerm = self::$permMap[$routeKey]; // null = any authenticated user

                if ($requiredPerm !== null) {
                    // ── 5. Load role's permissions from DB (cached) ──
                    $rolePerms = self::getRolePermissions($role);

                    if (!in_array($requiredPerm, $rolePerms, true)) {
                        throw new AuthorizationException($requiredPerm, $role);
                    }
                }
            }
        }

        // ── 6. Dispatch ──────────────────────────────────────────────
        // Controller already required and validated in step 0.
        $controller = new $controllerName();

        if (!empty($params)) {
            call_user_func_array([$controller, $methodOriginal], $params);
        } else {
            call_user_func([$controller, $methodOriginal]);
        }
    }

    /**
     * Return the list of permission keys for $role.
     * Delegates to AuthorizationHelper::loadForRole() so the
     * per-request cache is shared across the Router and controllers.
     */
    private static function getRolePermissions(string $role): array {
        // AuthorizationHelper::loadForRole() owns the shared per-request cache.
        return AuthorizationHelper::loadForRole($role);
    }

    // ========== NOTE: Error handling moved to ExceptionHandler ==========
    // Previous render403() and render404() methods have been replaced with
    // exceptions (AuthorizationException and NotFoundException) that are
    // handled by the global ExceptionHandler in core/ExceptionHandler.php
    // This ensures consistent error responses and proper logging.
}
?>
