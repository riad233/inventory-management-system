<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";

class Vendor extends Model {

    public function getAll(){
        $sql = "SELECT * FROM vendor ORDER BY Vendor_ID DESC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            Logger::error("Query prepare failed", ['error' => $this->conn->error]);
            return [];
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $vendors = [];
        
        if ($result) {
            while($row = $result->fetch_assoc()){
                $vendors[] = $row;
            }
        }
        return $vendors;
    }

    public function getById($id){
        $sql = "SELECT * FROM vendor WHERE Vendor_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function create($data){
        $sql = "INSERT INTO vendor (Vendor_Name, Contact_Person, Contact_Number, Email, Address, Vendor_Type, Vendor_Status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssss", $data['vendor_name'], $data['contact_person'], $data['contact_number'], $data['email'], $data['address'], $data['vendor_type'], $data['vendor_status']);
        return $stmt->execute();
    }

    public function update($id, $data){
        $sql = "UPDATE vendor SET Vendor_Name = ?, Contact_Person = ?, Contact_Number = ?, Email = ?, Address = ?, Vendor_Type = ?, Vendor_Status = ? WHERE Vendor_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssssi", $data['vendor_name'], $data['contact_person'], $data['contact_number'], $data['email'], $data['address'], $data['vendor_type'], $data['vendor_status'], $id);
        return $stmt->execute();
    }

    public function delete($id){
        $sql = "DELETE FROM vendor WHERE Vendor_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>