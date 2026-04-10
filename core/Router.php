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
        
        $controllerName = ucfirst($parts[0]) . 'Controller';
        $method = $parts[1] ?? 'index';
        $params = array_slice($parts, 2);

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
