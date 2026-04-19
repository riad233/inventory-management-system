<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";

class Request extends Model {
    
    /**
     * Get all requests
     * @return array Requests data from database
     */
    public function getAll() {
        $sql = "SELECT r.*, p.name as product_name, p.quantity, u.Username as requested_by_name
                FROM requests r
                LEFT JOIN products p ON r.product_id = p.id
                LEFT JOIN users u ON r.requested_by = u.User_ID
                ORDER BY r.created_at DESC
                LIMIT 100";
        
        $result = $this->conn->query($sql);
        $requests = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $requests[] = $row;
            }
        }
        
        return $requests;
    }
    
    /**
     * Get request by ID
     * @param int $id Request ID
     * @return array Request data or null
     */
    public function getById($id) {
        $sql = "SELECT r.*, p.name as product_name, p.quantity, u.Username as requested_by_name
                FROM requests r
                LEFT JOIN products p ON r.product_id = p.id
                LEFT JOIN users u ON r.requested_by = u.User_ID
                WHERE r.id = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return null;
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Create new request
     * @param int $product_id Product ID
     * @param string $type Request type (normal or emergency)
     * @param int $quantity Quantity requested
     * @param int $requested_by User ID who made the request
     * @param string $notes Optional notes
     * @return bool Success status
     */
    public function create($product_id, $type, $quantity, $requested_by, $notes = '') {
        // Validate product exists
        $productSql = "SELECT id FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($productSql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            error_log("Product not found: $product_id");
            return false;
        }
        
        // Create request
        $sql = "INSERT INTO requests (product_id, type, quantity, requested_by, notes, status)
                VALUES (?, ?, ?, ?, ?, 'pending')";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("isii s", $product_id, $type, $quantity, $requested_by, $notes);
        return $stmt->execute();
    }
    
    /**
     * Update request status
     * @param int $id Request ID
     * @param string $status New status
     * @return bool Success status
     */
    public function updateStatus($id, $status) {
        $valid_statuses = ['pending', 'approved', 'rejected', 'completed'];
        
        if (!in_array($status, $valid_statuses)) {
            error_log("Invalid status: $status");
            return false;
        }
        
        $sql = "UPDATE requests SET status = ?, updated_at = NOW() WHERE id = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }
    
    /**
     * Get requests by product ID
     * @param int $product_id Product ID
     * @return array Requests for the product
     */
    public function getByProductId($product_id) {
        $sql = "SELECT r.*, p.name as product_name, u.Username as requested_by_name
                FROM requests r
                LEFT JOIN products p ON r.product_id = p.id
                LEFT JOIN users u ON r.requested_by = u.User_ID
                WHERE r.product_id = ?
                ORDER BY r.created_at DESC
                LIMIT 50";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return [];
        }
        
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $requests = [];
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
        
        return $requests;
    }
    
    /**
     * Get pending requests count
     * @return int Count of pending requests
     */
    public function getPendingCount() {
        $sql = "SELECT COUNT(*) as count FROM requests WHERE status = 'pending'";
        
        $result = $this->conn->query($sql);
        if ($result) {
            $row = $result->fetch_assoc();
            return (int)$row['count'];
        }
        
        return 0;
    }
}
?>
