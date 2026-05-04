<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

require_once ROOT_PATH . '/config/authorization.php';

class Router {

    /** Route → permission_key map (loaded once per request). */
    private static ?array $permMap = null;

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
        $methodOriginal = $parts[1] ?? 'index';   // preserve original casing for dispatch
        $method         = strtolower($methodOriginal);
        $params         = array_slice($parts, 2);
        $routeKey       = $controllerKey . '/' . $method;

        // ── 0. Validate controller + method exist BEFORE any auth redirect ───
        // Invalid routes always get a 404 – never a login redirect.
        $controllerPath = ROOT_PATH . "/app/controllers/$controllerName.php";

        if (!file_exists($controllerPath)) {
            self::render404($routeKey);
            return;
        }

        require_once $controllerPath;

        if (!class_exists($controllerName)) {
            http_response_code(500);
            die("An unexpected error occurred.");
        }

        if (!method_exists($controllerName, $methodOriginal)) {
            self::render404($routeKey);
            return;
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

    /**
     * Render a 404 page and halt.
     * Uses layout.php when the user is authenticated; falls back to a
     * standalone page otherwise (no session = no sidebar needed).
     */
    private static function render404(string $routeKey = ''): void {
        http_response_code(404);

        if (class_exists('Logger')) {
            Logger::warning('404: route not found', [
                'route' => $routeKey,
                'url'   => $_GET['url'] ?? '',
            ]);
        }

        $view = 'errors/404';
        $data = ['title' => '404 – Page Not Found'];

        if (!empty($_SESSION['username'])) {
            // Authenticated: render inside the full layout with sidebar
            require ROOT_PATH . '/app/views/layout.php';
        } else {
            // Unauthenticated: render a minimal standalone page
            $viewPath = ROOT_PATH . '/app/views/errors/404.php';
            echo '<!DOCTYPE html><html lang="en"><head>'
               . '<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
               . '<title>404 – Page Not Found</title>'
               . '<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">'
               . '<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">'
               . '</head><body style="background:#f8f9fa">';
            if (file_exists($viewPath)) {
                include $viewPath;
            } else {
                echo '<div class="d-flex align-items-center justify-content-center" style="min-height:100vh">'
                   . '<div class="text-center">'
                   . '<h1 class="display-1 fw-bold text-secondary">404</h1>'
                   . '<h2 class="mb-3">Page Not Found</h2>'
                   . '<a href="?url=home/index" class="btn btn-primary">Go Home</a>'
                   . '</div></div>';
            }
            echo '</body></html>';
        }
        exit;
    }
}
?>
