<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

class Router {

    /** Route → permission_key map (loaded once per request). */
    private static ?array $permMap = null;

    /** Per-request permission cache: role → [perm_key, ...] */
    private static array $permCache = [];

    // Controllers that never require authentication
    private const PUBLIC_CONTROLLERS = ['auth', 'home'];

    // The SuperAdmin role name – bypasses all permission checks
    private const SUPERADMIN_ROLE = 'SuperAdmin';

    public static function route(string $url): void {
        $parts = array_values(array_filter(explode('/', trim($url, '/'))));

        if (empty($parts)) {
            $parts = ['dashboard', 'index'];
        }

        $controllerKey  = strtolower($parts[0]);
        $controllerName = ucfirst($parts[0]) . 'Controller';
        $method         = strtolower($parts[1] ?? 'index');
        $params         = array_slice($parts, 2);
        $routeKey       = $controllerKey . '/' . $method;

        // ── 1. Public routes ─────────────────────────────────────────
        if (!in_array($controllerKey, self::PUBLIC_CONTROLLERS, true)) {

            // ── 2. Must be authenticated ─────────────────────────────
            if (empty($_SESSION['username'])) {
                header('Location: ?url=auth/login');
                exit;
            }

            $role = (string)($_SESSION['role'] ?? '');

            // ── 3. SuperAdmin bypasses everything ────────────────────
            if ($role !== self::SUPERADMIN_ROLE) {

                // SuperAdmin-only controller: block everyone else
                if ($controllerKey === 'superadmin') {
                    self::render403($routeKey);
                    return;
                }

                // ── 4. Load route→permission map ─────────────────────
                if (self::$permMap === null) {
                    self::$permMap = require ROOT_PATH . '/config/permissions_map.php';
                }

                // Routes not in the map are SuperAdmin-only by default
                if (!array_key_exists($routeKey, self::$permMap)) {
                    self::render403($routeKey);
                    return;
                }

                $requiredPerm = self::$permMap[$routeKey]; // null = any authenticated user

                if ($requiredPerm !== null) {
                    // ── 5. Load role's permissions from DB (cached) ──
                    $rolePerms = self::getRolePermissions($role);

                    if (!in_array($requiredPerm, $rolePerms, true)) {
                        self::render403($routeKey, $requiredPerm);
                        return;
                    }
                }
            }
        }

        // ── 6. Dispatch ──────────────────────────────────────────────
        // Restore original casing for controller class name
        $controllerName = ucfirst($parts[0]) . 'Controller';
        $methodOriginal = $parts[1] ?? 'index';   // preserve case for method call
        $controllerPath = ROOT_PATH . "/app/controllers/$controllerName.php";

        if (!file_exists($controllerPath)) {
            http_response_code(404);
            die("Page not found.");
        }

        require_once $controllerPath;

        if (!class_exists($controllerName)) {
            http_response_code(500);
            die("An unexpected error occurred.");
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $methodOriginal)) {
            http_response_code(404);
            die("Page not found.");
        }

        if (!empty($params)) {
            call_user_func_array([$controller, $methodOriginal], $params);
        } else {
            call_user_func([$controller, $methodOriginal]);
        }
    }

    /**
     * Return the list of permission keys for $role.
     * Queries the DB once per role per request, then caches.
     */
    private static function getRolePermissions(string $role): array {
        if (isset(self::$permCache[$role])) {
            return self::$permCache[$role];
        }

        global $conn;
        if (!$conn) {
            require_once ROOT_PATH . '/config/database.php';
            global $conn;
        }

        $stmt = $conn->prepare(
            "SELECT permission_key FROM role_permissions WHERE role = ?"
        );

        if (!$stmt) {
            self::$permCache[$role] = [];
            return [];
        }

        $stmt->bind_param('s', $role);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        self::$permCache[$role] = array_column($rows, 'permission_key');
        return self::$permCache[$role];
    }

    /**
     * Render the styled 403 page inside the normal layout and halt.
     */
    private static function render403(string $routeKey = '', string $perm = ''): void {
        http_response_code(403);

        if (class_exists('Logger')) {
            Logger::warning('ACL: access denied', [
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'role'    => $_SESSION['role']    ?? 'unknown',
                'route'   => $routeKey,
                'perm'    => $perm,
                'url'     => $_GET['url'] ?? '',
            ]);
        }

        $view = 'errors/403';
        $data = ['title' => '403 – Access Denied'];
        require ROOT_PATH . '/app/views/layout.php';
        exit;
    }
}
?>
