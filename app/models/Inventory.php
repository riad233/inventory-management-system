<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";

class Inventory extends Model {
    
    /**
     * Get low and out of stock items
     * Out of Stock: Quantity < 3
     * Low Stock: 3 <= Quantity <= 10
     * @param int $lowThreshold Threshold for low stock (default: 10)
     * @return array Associative array with low_items, out_items, total_low, total_out
     */
    public function getLowAndOutStock($lowThreshold = 10) {
        // Query for low stock items (3 <= quantity <= 10)
        $sqlLow = "SELECT Item_ID, Item_Name, SKU, Category, Quantity, Location, Status
                   FROM inventory_items
                   WHERE Quantity >= 3 AND Quantity <= ?
                   ORDER BY Quantity ASC
                   LIMIT 100";
        
        $stmtLow = $this->conn->prepare($sqlLow);
        $stmtLow->bind_param("i", $lowThreshold);
        $stmtLow->execute();
        $resultLow = $stmtLow->get_result();
        
        $lowItems = [];
        while ($row = $resultLow->fetch_assoc()) {
            $lowItems[] = $row;
        }
        
        // Query for out of stock items (quantity < 3)
        $sqlOut = "SELECT Item_ID, Item_Name, SKU, Category, Quantity, Location, Status
                   FROM inventory_items
                   WHERE Quantity < 3
                   ORDER BY Updated_At DESC
                   LIMIT 100";
        
        $stmtOut = $this->conn->prepare($sqlOut);
        $stmtOut->execute();
        $resultOut = $stmtOut->get_result();
        
        $outItems = [];
        while ($row = $resultOut->fetch_assoc()) {
            $outItems[] = $row;
        }
        
        return [
            'low_items' => $lowItems,
            'out_items' => $outItems,
            'total_low' => count($lowItems),
            'total_out' => count($outItems),
            'low_top5' => array_slice($lowItems, 0, 5),
            'out_top5' => array_slice($outItems, 0, 5)
        ];
    }
    
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
        $sql = "SELECT i.*, a.Asset_Name, a.Serial_Number as SKU, a.Category, a.Brand
                FROM inventory_items i
                LEFT JOIN asset a ON i.Asset_ID = a.Asset_ID
                ORDER BY i.Quantity ASC";
        
        $result = $this->conn->query($sql);
        $items = [];
        
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        return $items;
    }
}
?>
