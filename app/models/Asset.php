<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";

class Asset extends Model {
    public function getAll() {
        $sql = "SELECT * FROM asset ORDER BY Asset_ID DESC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            Logger::error("Query prepare failed", ['error' => $this->conn->error]);
            return [];
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $assets = [];
        
        if ($result) {
            while($row = $result->fetch_assoc()) {
                $assets[] = $row;
            }
        }
        
        return $assets;
    }

    public function getById($id){
        $sql = "SELECT * FROM asset WHERE Asset_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function add($data) {
        $sql = "INSERT INTO asset (Asset_Name, Category, Brand, Model, Serial_Number, Purchase_Date, Warranty_Expiry, Status, Vendor_ID)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $vendor_id = $data['vendor_id'] ?? null;
        $stmt->bind_param(
            "ssssssssi",
            $data['name'],
            $data['category'],
            $data['brand'],
            $data['model'],
            $data['serial'],
            $data['purchase_date'],
            $data['warranty'],
            $data['status'],
            $vendor_id
        );
        return $stmt->execute();
    }

    public function update($id, $data){
        $sql = "UPDATE asset SET Asset_Name = ?, Category = ?, Brand = ?, Model = ?, Serial_Number = ?, Purchase_Date = ?, Warranty_Expiry = ?, Status = ?, Vendor_ID = ? WHERE Asset_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $vendor_id = $data['vendor_id'] ?? null;
        $stmt->bind_param(
            "ssssssssii",
            $data['name'],
            $data['category'],
            $data['brand'],
            $data['model'],
            $data['serial'],
            $data['purchase_date'],
            $data['warranty'],
            $data['status'],
            $vendor_id,
            $id
        );
        return $stmt->execute();
    }

    public function delete($id){
        $sql = "DELETE FROM asset WHERE Asset_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
