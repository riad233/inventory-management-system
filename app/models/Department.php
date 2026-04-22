<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";

class Department extends Model {
    protected $table = 'department';
    
    public function getAll() {
        $sql = "SELECT * FROM department";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            Logger::error("Query prepare failed", ['error' => $this->conn->error]);
            return [];
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $departments = [];
        while ($row = $result->fetch_assoc()) {
            $departments[] = $row;
        }
        $stmt->close();
        return $departments;
    }
}
?>