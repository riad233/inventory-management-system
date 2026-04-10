<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";

class Asset extends Model {
    public function getAll() {
        $sql = "SELECT * FROM asset";
        $result = mysqli_query($this->conn, $sql);
        $assets = [];
        while($row = mysqli_fetch_assoc($result)) {
            $assets[] = $row;
        }
        return $assets;
    }

    public function getById($id){
        $sql = "SELECT * FROM asset WHERE Asset_ID = '$id'";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function add($data) {
        $sql = "INSERT INTO asset (Asset_Name, Category, Brand, Model, Serial_Number, Purchase_Date, Warranty_Expiry, Status)
                VALUES ('{$data['name']}', '{$data['category']}', '{$data['brand']}', '{$data['model']}', '{$data['serial']}', '{$data['purchase_date']}', '{$data['warranty']}', '{$data['status']}')";
        return mysqli_query($this->conn, $sql);
    }

    public function update($id, $data){
        $sql = "UPDATE asset SET Asset_Name='{$data['name']}', Category='{$data['category']}', Brand='{$data['brand']}', Model='{$data['model']}', Serial_Number='{$data['serial']}', Purchase_Date='{$data['purchase_date']}', Warranty_Expiry='{$data['warranty']}', Status='{$data['status']}' WHERE Asset_ID = '$id'";
        return mysqli_query($this->conn, $sql);
    }

    public function delete($id){
        $sql = "DELETE FROM asset WHERE Asset_ID = '$id'";
        return mysqli_query($this->conn, $sql);
    }
}
?>
