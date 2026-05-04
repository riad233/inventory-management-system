<?php
/**
 * SuperAdminController – DEPRECATED / ROUTING DISABLED
 *
 * All /superadmin routes are intercepted by Router::route() and
 * permanently redirected to /admin before this controller is ever
 * instantiated.  This file is kept to satisfy any legacy references
 * and to preserve the SuperAdmin role in the system, but none of its
 * methods should ever be called through normal routing.
 *
 * DO NOT add business logic here.
 * DO NOT add this controller to the route map.
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . '/config/config.php';

class SuperAdminController {

    /**
     * Catch-all: redirect every method call to /admin.
     *
     * If somehow the Router dispatches here (e.g. a future misconfiguration),
     * every public method will safely bounce the user to the admin panel
     * instead of throwing a fatal error or exposing an unguarded route.
     */
    public function __call(string $name, array $args): void {
        $base = defined('APP_BASE') ? APP_BASE : '';
        header('Location: ' . $base . '/admin/index');
        exit;
    }

    public function index(): void {
        $base = defined('APP_BASE') ? APP_BASE : '';
        header('Location: ' . $base . '/admin/index');
        exit;
    }
}
