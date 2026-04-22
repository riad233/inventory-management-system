<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";

class Maintenance extends Model {
    protected $table = 'maintenance';

    public function getAll(){
        $sql = "SELECT m.*, a.Asset_Name FROM maintenance m LEFT JOIN asset a ON m.Asset_ID = a.Asset_ID ORDER BY m.Maintenance_ID DESC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            Logger::error("Query prepare failed", ['error' => $this->conn->error]);
            return [];
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $maintenance = [];
        
        if ($result) {
            while($row = $result->fetch_assoc()){
                $maintenance[] = $row;
            }
        }
        return $maintenance;
    }

    public function getById($id){
        $sql = "SELECT * FROM maintenance WHERE Maintenance_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function create($data){
        $reported_date = date('Y-m-d');
        $status = $data['maintenance_status'] ?? 'Pending';
        $sql = "INSERT INTO maintenance (Asset_ID, Reported_Date, Status, Cost, Vendor_ID) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $vendor_id = $data['vendor_id'] ?? null;
        $stmt->bind_param("issdi", $data['asset_id'], $reported_date, $status, $data['cost'], $vendor_id);
        return $stmt->execute();
    }

    public function updateStatus($id, $status, $end_date){
        $sql = "UPDATE maintenance SET Status = ?, Repair_End_Date = ? WHERE Maintenance_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $status, $end_date, $id);
        return $stmt->execute();
    }

    public function update($id, $data){
        $sql = "UPDATE maintenance SET Asset_ID = ?, Cost = ? WHERE Maintenance_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("idi", $data['asset_id'], $data['cost'], $id);
        return $stmt->execute();
    }

    public function delete($id){
        $sql = "DELETE FROM maintenance WHERE Maintenance_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getPendingCount() {
        $sql = "SELECT COUNT(*) as total FROM maintenance WHERE Status = 'Pending'";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            Logger::error("Query prepare failed", ['error' => $this->conn->error]);
            return 0;
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    public function getRecent($limit = 5) {
        $sql = "SELECT m.Maintenance_ID, m.Reported_Date, m.Status, m.Cost, a.Asset_Name
                FROM maintenance m
                LEFT JOIN asset a ON m.Asset_ID = a.Asset_ID
                ORDER BY m.Maintenance_ID DESC LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            Logger::error("Query prepare failed", ['error' => $this->conn->error]);
            return [];
        }
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        $maintenance = [];
        
        if ($result) {
            while($row = $result->fetch_assoc()) {
                $maintenance[] = $row;
            }
        }
        return $maintenance;
    }
}
?>