<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";
require_once ROOT_PATH . "/config/logger.php";

class Product extends Model {
    
    /**
     * Get all products with stock information
     * @return array Products data from database
     */
    public function getAll() {
        $sql = "SELECT i.Item_ID as id, 
                       COALESCE(a.Asset_Name, 'Unknown') as name, 
                       COALESCE(a.Category, 'N/A') as category, 
                       i.Quantity as quantity, 
                       i.Location as location,
                       COALESCE(a.Serial_Number, '') as sku
                FROM inventory_items i
                LEFT JOIN asset a ON i.Asset_ID = a.Asset_ID
                ORDER BY i.Quantity ASC";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            Logger::error("Query prepare failed", ['error' => $this->conn->error]);
            // Fallback: simple query without joins
            $fallbackSql = "SELECT Item_ID as id, Quantity as quantity, Location as location FROM inventory_items ORDER BY Quantity ASC";
            $stmt = $this->conn->prepare($fallbackSql);
            if (!$stmt) {
                Logger::error("Fallback query failed", ['error' => $this->conn->error]);
                return [];
            }
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        
        return $products;
    }
    
    /**
     * Get product by ID
     * @param int $id Product ID
     * @return array Product data or null
     */
    public function getById($id) {
        $sql = "SELECT i.Item_ID as id, 
                       COALESCE(a.Asset_Name, 'Unknown') as name, 
                       COALESCE(a.Category, 'N/A') as category, 
                       i.Quantity as quantity, 
                       i.Location as location,
                       COALESCE(a.Serial_Number, '') as sku
                FROM inventory_items i
                LEFT JOIN asset a ON i.Asset_ID = a.Asset_ID
                WHERE i.Item_ID = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            Logger::error("Query prepare failed", ['error' => $this->conn->error]);
            // Fallback: simple query without joins
            $fallbackSql = "SELECT Item_ID as id, Quantity as quantity, Location as location FROM inventory_items WHERE Item_ID = ?";
            $stmt = $this->conn->prepare($fallbackSql);
            if (!$stmt) {
                Logger::error("Fallback query failed", ['error' => $this->conn->error]);
                return null;
            }
        }
        
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            Logger::error("Query execution failed", ['error' => $stmt->error]);
            return null;
        }
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    /**
     * Add new product (creates inventory item)
     * @param int $asset_id Asset ID
     * @param int $quantity Initial quantity
     * @param string $location Location
     * @return bool Success status
     */
    public function add($asset_id, $quantity, $location = '') {
        $sql = "INSERT INTO inventory_items (Asset_ID, Quantity, Location) 
                VALUES (?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            Logger::error("Prepare failed", ['error' => $this->conn->error]);
            return false;
        }
        
        $stmt->bind_param("iis", $asset_id, $quantity, $location);
        if ($stmt->execute()) {
            Logger::info("Product added", ['asset_id' => $asset_id, 'quantity' => $quantity]);
            return true;
        } else {
            Logger::error("Failed to add product", ['error' => $stmt->error]);
            return false;
        }
    }
    
    /**
     * Update product quantity
     * @param int $id Item ID
     * @param int $quantity New quantity
     * @param string $location Location
     * @return bool Success status
     */
    public function update($id, $quantity, $location = '') {
        $sql = "UPDATE inventory_items 
                SET Quantity = ?, Location = ?, Last_Updated = NOW()
                WHERE Item_ID = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            Logger::error("Prepare failed", ['error' => $this->conn->error]);
            return false;
        }
        
        $stmt->bind_param("isi", $quantity, $location, $id);
        if ($stmt->execute()) {
            Logger::info("Product updated", ['item_id' => $id]);
            return true;
        } else {
            Logger::error("Failed to update product", ['error' => $stmt->error]);
            return false;
        }
    }
    
    /**
     * Delete product
     * @param int $id Item ID
     * @return bool Success status
     */
    public function delete($id) {
        $sql = "DELETE FROM inventory_items WHERE Item_ID = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            Logger::error("Prepare failed", ['error' => $this->conn->error]);
            return false;
        }
        
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            Logger::info("Product deleted", ['item_id' => $id]);
            return true;
        } else {
            Logger::error("Failed to delete product", ['error' => $stmt->error]);
            return false;
        }
    }
    
    /**
     * Update product quantity
     * @param int $id Item ID
     * @param int $quantity New quantity
     * @return bool Success status
     */
    public function updateQuantity($id, $quantity) {
        $sql = "UPDATE inventory_items SET Quantity = ?, Last_Updated = NOW() WHERE Item_ID = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            Logger::error("Prepare failed", ['error' => $this->conn->error]);
            return false;
        }
        
        $stmt->bind_param("ii", $quantity, $id);
        if ($stmt->execute()) {
            Logger::info("Product quantity updated", ['item_id' => $id, 'quantity' => $quantity]);
            return true;
        } else {
            Logger::error("Failed to update quantity", ['error' => $stmt->error]);
            return false;
        }
    }
    
    /**
     * Get dynamic alerts based on stock levels
     * OUT OF STOCK: quantity = 0
     * LOW STOCK: 0 < quantity < 10
     * @return array Alerts with products data
     */
    public function getAlerts() {
        $alerts = [
            'out_of_stock' => [],
            'low_stock' => [],
            'total_out' => 0,
            'total_low' => 0
        ];
        
        // OUT OF STOCK: quantity = 0
        $sqlOut = "SELECT i.Item_ID as id, 
                          COALESCE(a.Asset_Name, 'Unknown') as name,
                          i.Quantity as quantity, 
                          i.Location as location,
                          'out_of_stock' as alert_type
                   FROM inventory_items i
                   LEFT JOIN asset a ON i.Asset_ID = a.Asset_ID
                   WHERE i.Quantity = 0
                   ORDER BY i.Last_Updated DESC";
        
        $stmtOut = $this->conn->prepare($sqlOut);
        if ($stmtOut) {
            $stmtOut->execute();
            $resultOut = $stmtOut->get_result();
            if ($resultOut) {
                while ($row = $resultOut->fetch_assoc()) {
                    $alerts['out_of_stock'][] = $row;
                }
            }
        }
        $alerts['total_out'] = count($alerts['out_of_stock']);
        
        // LOW STOCK: 0 < quantity < 10
        $sqlLow = "SELECT i.Item_ID as id, 
                          COALESCE(a.Asset_Name, 'Unknown') as name,
                          i.Quantity as quantity, 
                          i.Location as location,
                          'low_stock' as alert_type
                   FROM inventory_items i
                   LEFT JOIN asset a ON i.Asset_ID = a.Asset_ID
                   WHERE i.Quantity > 0 AND i.Quantity < 10
                   ORDER BY i.Quantity ASC";
        
        $stmtLow = $this->conn->prepare($sqlLow);
        if ($stmtLow) {
            $stmtLow->execute();
            $resultLow = $stmtLow->get_result();
            if ($resultLow) {
                while ($row = $resultLow->fetch_assoc()) {
                    $alerts['low_stock'][] = $row;
                }
            }
        }
        $alerts['total_low'] = count($alerts['low_stock']);
        
        return $alerts;
    }
}
?>
