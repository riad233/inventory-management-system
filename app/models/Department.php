<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";

class Department extends Model {
    public function getAll() {
        $sql = "SELECT * FROM department";
        $result = mysqli_query($this->conn, $sql);
        $departments = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $departments[] = $row;
        }
        return $departments;
    }
}
?>