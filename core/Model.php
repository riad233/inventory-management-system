<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

require_once ROOT_PATH . "/config/database.php";
require_once ROOT_PATH . "/config/logger.php";

class Model {
    protected $conn;
    protected $table;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    /**
     * Get count of all records
     */
    public function count() {
        if (!$this->table) return 0;
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            Logger::error("Count query failed", ['table' => $this->table]);
            return 0;
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row['total'] ?? 0;
    }
    
    /**
     * Get count with WHERE clause
     */
    public function countWhere($column, $value) {
        if (!$this->table) return 0;
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE {$column} = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            Logger::error("Count where query failed", ['table' => $this->table, 'column' => $column]);
            return 0;
        }
        
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row['total'] ?? 0;
    }
    
    /**
     * Check if record exists
     */
    public function exists($column, $value) {
        return $this->countWhere($column, $value) > 0;
    }
    
    /**
     * Get records with pagination
     */
    public function paginate($page = 1, $perPage = 50, $orderBy = 'ID DESC') {
        if (!$this->table) return [];
        
        require_once ROOT_PATH . "/config/paginator.php";
        
        $total = $this->count();
        $pagination = Paginator::paginate($total, $page, $perPage);
        
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy} LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            Logger::error("Paginate query failed", ['table' => $this->table]);
            return [];
        }
        
        $stmt->bind_param("ii", $pagination['perPage'], $pagination['offset']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $records = [];
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }
        $stmt->close();
        
        return $records;
    }
    
    /**
     * Get total record count (for pagination info)
     */
    public function getPaginationInfo($page = 1, $perPage = 50) {
        require_once ROOT_PATH . "/config/paginator.php";
        return Paginator::paginate($this->count(), $page, $perPage);
    }
    
    /**
     * Log database operation
     */
    protected function logOperation($operation, $data = []) {
        Logger::info("{$operation} on {$this->table}", $data);
    }
}
?>