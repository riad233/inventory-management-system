<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";

class Vendor extends Model {

    public function getAll(){
        $sql = "SELECT * FROM vendor";
        $result = mysqli_query($this->conn, $sql);
        $vendors = [];
        while($row = mysqli_fetch_assoc($result)){
            $vendors[] = $row;
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
        $sql = "INSERT INTO vendor (Vendor_Name, Contact_Person, Contact_Number, Email, Address) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssss", $data['vendor_name'], $data['contact_person'], $data['contact_number'], $data['email'], $data['address']);
        return $stmt->execute();
    }

    public function update($id, $data){
        $sql = "UPDATE vendor SET Vendor_Name = ?, Contact_Person = ?, Contact_Number = ?, Email = ?, Address = ? WHERE Vendor_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssi", $data['vendor_name'], $data['contact_person'], $data['contact_number'], $data['email'], $data['address'], $id);
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