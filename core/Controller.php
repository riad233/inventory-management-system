<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

class Controller {
    public function model($model) {
        require_once ROOT_PATH . "/app/models/" . $model . ".php";
        return new $model();
    }

    public function view($view, $data = []) {
        $viewPath = ROOT_PATH . "/app/views/" . $view . ".php";
        
        if (!file_exists($viewPath)) {
            die("View not found: $view");
        }
        
        require_once $viewPath;
    }
}
?>