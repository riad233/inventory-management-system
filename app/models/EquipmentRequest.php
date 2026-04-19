<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";
require_once ROOT_PATH . "/config/logger.php";

class EquipmentRequest extends Model {

    public function getAll(){
        $sql = "SELECT r.*, e.Name FROM equipment_request r LEFT JOIN employee e ON r.User_ID = e.User_ID ORDER BY r.Request_ID DESC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            Logger::error("Query prepare failed", ['error' => $this->conn->error]);
            return [];
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $requests = [];
        
        if ($result) {
            while($row = $result->fetch_assoc()){
                $requests[] = $row;
            }
        }
        return $requests;
    }

    public function getById($id){
        $sql = "SELECT * FROM equipment_request WHERE Request_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function create($data){
        $request_date = date('Y-m-d');
        $sql = "INSERT INTO equipment_request (User_ID, Equipment_Type, Description, Request_Date, Status, Vendor_ID) 
                VALUES (?, ?, ?, ?, 'Pending', ?)";
        $stmt = $this->conn->prepare($sql);
        $vendor_id = $data['vendor_id'] ?? null;
        $stmt->bind_param("isssi", $data['user_id'], $data['equipment_type'], $data['description'], $request_date, $vendor_id);
        return $stmt->execute();
    }

    public function approve($id, $approved_by){
        $approval_date = date('Y-m-d');
        $sql = "UPDATE equipment_request SET Status = 'Approved', Approval_Date = ?, Approved_By = ? WHERE Request_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $approval_date, $approved_by, $id);
        return $stmt->execute();
    }

    public function reject($id){
        $sql = "UPDATE equipment_request SET Status = 'Rejected' WHERE Request_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function update($id, $data){
        $sql = "UPDATE equipment_request SET Equipment_Type = ?, Description = ?, Vendor_ID = ? WHERE Request_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $vendor_id = $data['vendor_id'] ?? null;
        $stmt->bind_param("ssii", $data['equipment_type'], $data['description'], $vendor_id, $id);
        return $stmt->execute();
    }

    public function delete($id){
        $sql = "DELETE FROM equipment_request WHERE Request_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
