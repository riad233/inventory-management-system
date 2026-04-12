<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

class Router {
    public static function route($url) {
        $parts = explode('/', trim($url, '/'));
        
        // Filter out empty parts
        $parts = array_filter($parts);
        $parts = array_values($parts);
        
        if (empty($parts)) {
            $parts = ['dashboard', 'index'];
        }
        
        $controllerKey = strtolower($parts[0]);
        $controllerName = ucfirst($parts[0]) . 'Controller';
        $method = $parts[1] ?? 'index';
        $params = array_slice($parts, 2);

        $publicControllers = ['auth', 'home'];
        if (!in_array($controllerKey, $publicControllers, true)) {
            if (empty($_SESSION['username'])) {
                header("Location: ?url=auth/login");
                exit;
            }

            $role = $_SESSION['role'] ?? '';
            if (!in_array($role, ['Admin', 'Manager'], true)) {
                http_response_code(403);
                die('Forbidden');
            }
        }

        $controllerPath = ROOT_PATH . "/app/controllers/$controllerName.php";
        
        if (!file_exists($controllerPath)) {
            die("Controller not found: $controllerName");
        }
        
        require_once $controllerPath;
        
        if (!class_exists($controllerName)) {
            die("Class not found: $controllerName");
        }
        
        $controller = new $controllerName();
        
        if (!method_exists($controller, $method)) {
            die("Method not found: $controllerName->$method");
        }
        
        if (!empty($params)) {
            call_user_func_array([$controller, $method], $params);
        } else {
            call_user_func([$controller, $method]);
        }
    }
}
?>
