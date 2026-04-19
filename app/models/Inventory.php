<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";
require_once ROOT_PATH . "/config/logger.php";

class Inventory extends Model {
    
    
    /**
     * Get inventory item by Asset ID
     */
    public function getByAssetId($assetId) {
        $sql = "SELECT * FROM inventory_items WHERE Asset_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $assetId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    /**
     * Update inventory quantity
     */
    public function updateQuantity($itemId, $quantity) {
        $sql = "UPDATE inventory_items SET Quantity = ?, Last_Updated = NOW() WHERE Item_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $quantity, $itemId);
        return $stmt->execute();
    }
    
    /**
     * Get all inventory items with asset details
     */
    public function getAll() {
        $sql = "SELECT i.Item_ID, i.Asset_ID, i.Quantity, i.Location, i.Last_Updated,
                       COALESCE(a.Asset_Name, 'Unknown') as Asset_Name, 
                       COALESCE(a.Serial_Number, '') as SKU, 
                       COALESCE(a.Category, '') as Category, 
                       COALESCE(a.Brand, '') as Brand
                FROM inventory_items i
                LEFT JOIN asset a ON i.Asset_ID = a.Asset_ID
                ORDER BY i.Quantity ASC";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            Logger::error("Query prepare failed", ['error' => $this->conn->error]);
            // Fallback: simple query without joins
            $fallbackSql = "SELECT Item_ID, Asset_ID, Quantity, Location, Last_Updated FROM inventory_items ORDER BY Quantity ASC";
            $stmt = $this->conn->prepare($fallbackSql);
            if (!$stmt) {
                Logger::error("Fallback query failed", ['error' => $this->conn->error]);
                return [];
            }
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $items = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        
        return $items;
    }
}
?>
