<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

// Load authorization helper
require_once ROOT_PATH . "/config/authorization.php";

class Controller {
    public function model($model) {
        require_once ROOT_PATH . "/app/models/" . $model . ".php";
        return new $model();
    }

    public function view($view, $data = []) {
        $viewPath = ROOT_PATH . "/app/views/" . $view . ".php";
        
        if (!file_exists($viewPath)) {
            http_response_code(404);
            die("Page not found.");
        }
        
        // Load layout with view
        $layoutPath = ROOT_PATH . "/app/views/layout.php";
        require_once $layoutPath;
    }

    public function viewPlain($view, $data = []) {
        $viewPath = ROOT_PATH . "/app/views/" . $view . ".php";
        
        if (!file_exists($viewPath)) {
            http_response_code(404);
            die("Page not found.");
        }
        
        require_once $viewPath;
    }
}
?>