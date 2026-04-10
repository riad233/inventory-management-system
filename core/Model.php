<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

require_once ROOT_PATH . "/config/database.php";

class Model {
    protected $conn;
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
}
?>