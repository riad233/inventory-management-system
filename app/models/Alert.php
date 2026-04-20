<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";
require_once ROOT_PATH . "/config/logger.php";

class Alert extends Model {
    
    /**
     * Get all alerts based on stock levels
     * OUT OF STOCK: quantity < 3
     * LOW STOCK: quantity >= 3 AND quantity <= 10
     * @return array Alerts with products data
     */
    public function getAll() {
        $alerts = [];
        
        $sql = "SELECT id, name, quantity,
                       CASE 
                           WHEN quantity < 3 THEN 'Out of Stock'
                           WHEN quantity >= 3 AND quantity <= 10 THEN 'Low Stock'
                       END as alert_type
                FROM products
                WHERE quantity < 3 OR (quantity >= 3 AND quantity <= 10)
                ORDER BY quantity ASC, name ASC";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            Logger::error("Query prepare failed", ['error' => $this->conn->error]);
            return $alerts;
        }
        
        if (!$stmt->execute()) {
            Logger::error("Query execution failed", ['error' => $stmt->error]);
            return $alerts;
        }
        
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $alerts[] = $row;
            }
        }
        
        $stmt->close();
        return $alerts;
    }
    
    /**
     * Get alert count (total number of products with alerts)
     * @return int Count of products matching alert conditions
     */
    public function getCount() {
        $sql = "SELECT COUNT(*) as count
                FROM products
                WHERE quantity < 3 OR (quantity >= 3 AND quantity <= 10)";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            Logger::error("Query prepare failed", ['error' => $this->conn->error]);
            return 0;
        }
        
        if (!$stmt->execute()) {
            Logger::error("Query execution failed", ['error' => $stmt->error]);
            return 0;
        }
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count = $row['count'] ?? 0;
        
        $stmt->close();
        return (int)$count;
    }
    
    /**
     * Get out of stock products only (quantity < 3)
     * @return array Out of stock products
     */
    public function getOutOfStock() {
        $alerts = [];
        
        $sql = "SELECT id, name, quantity, 'Out of Stock' as alert_type
                FROM products
                WHERE quantity < 3
                ORDER BY quantity ASC, name ASC";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            Logger::error("Query prepare failed", ['error' => $this->conn->error]);
            return $alerts;
        }
        
        if (!$stmt->execute()) {
            Logger::error("Query execution failed", ['error' => $stmt->error]);
            return $alerts;
        }
        
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $alerts[] = $row;
            }
        }
        
        $stmt->close();
        return $alerts;
    }
    
    /**
     * Get low stock products only (quantity >= 3 AND quantity <= 10)
     * @return array Low stock products
     */
    public function getLowStock() {
        $alerts = [];
        
        $sql = "SELECT id, name, quantity, 'Low Stock' as alert_type
                FROM products
                WHERE quantity >= 3 AND quantity <= 10
                ORDER BY quantity ASC, name ASC";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            Logger::error("Query prepare failed", ['error' => $this->conn->error]);
            return $alerts;
        }
        
        if (!$stmt->execute()) {
            Logger::error("Query execution failed", ['error' => $stmt->error]);
            return $alerts;
        }
        
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $alerts[] = $row;
            }
        }
        
        $stmt->close();
        return $alerts;
    }
}
?>
