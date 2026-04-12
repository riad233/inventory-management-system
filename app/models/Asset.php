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
        $sql = "SELECT * FROM asset WHERE Asset_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function add($data) {
        $sql = "INSERT INTO asset (Asset_Name, Category, Brand, Model, Serial_Number, Purchase_Date, Warranty_Expiry, Status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "ssssssss",
            $data['name'],
            $data['category'],
            $data['brand'],
            $data['model'],
            $data['serial'],
            $data['purchase_date'],
            $data['warranty'],
            $data['status']
        );
        return $stmt->execute();
    }

    public function update($id, $data){
        $sql = "UPDATE asset SET Asset_Name = ?, Category = ?, Brand = ?, Model = ?, Serial_Number = ?, Purchase_Date = ?, Warranty_Expiry = ?, Status = ? WHERE Asset_ID = ?";
        $stmt = $this->conn->prepare($sql);
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
