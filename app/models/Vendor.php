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
        $sql = "SELECT * FROM vendor WHERE Vendor_ID = '$id'";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function create($data){
        $sql = "INSERT INTO vendor (Vendor_Name, Contact_Person, Contact_Number, Email, Address) 
                VALUES ('{$data['vendor_name']}', '{$data['contact_person']}', '{$data['contact_number']}', '{$data['email']}', '{$data['address']}')";
        return mysqli_query($this->conn, $sql);
    }

    public function update($id, $data){
        $sql = "UPDATE vendor SET Vendor_Name='{$data['vendor_name']}', Contact_Person='{$data['contact_person']}', Contact_Number='{$data['contact_number']}', Email='{$data['email']}', Address='{$data['address']}' WHERE Vendor_ID = '$id'";
        return mysqli_query($this->conn, $sql);
    }

    public function delete($id){
        $sql = "DELETE FROM vendor WHERE Vendor_ID = '$id'";
        return mysqli_query($this->conn, $sql);
    }
}
?>