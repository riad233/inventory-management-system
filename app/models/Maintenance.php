<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";

class Maintenance extends Model {

    public function getAll(){
        $sql = "SELECT m.*, a.Asset_Name FROM maintenance m LEFT JOIN asset a ON m.Asset_ID = a.Asset_ID";
        $result = mysqli_query($this->conn, $sql);
        $maintenance = [];
        while($row = mysqli_fetch_assoc($result)){
            $maintenance[] = $row;
        }
        return $maintenance;
    }

    public function getById($id){
        $sql = "SELECT * FROM maintenance WHERE Maintenance_ID = '$id'";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function create($data){
        $reported_date = date('Y-m-d');
        $sql = "INSERT INTO maintenance (Asset_ID, Reported_Date, Status, Cost) 
                VALUES ('{$data['asset_id']}', '$reported_date', 'Pending', '{$data['cost']}')";
        return mysqli_query($this->conn, $sql);
    }

    public function updateStatus($id, $status, $end_date){
        $sql = "UPDATE maintenance SET Status='$status', Repair_End_Date='$end_date' WHERE Maintenance_ID = '$id'";
        return mysqli_query($this->conn, $sql);
    }

    public function update($id, $data){
        $sql = "UPDATE maintenance SET Asset_ID='{$data['asset_id']}', Cost='{$data['cost']}' WHERE Maintenance_ID = '$id'";
        return mysqli_query($this->conn, $sql);
    }

    public function delete($id){
        $sql = "DELETE FROM maintenance WHERE Maintenance_ID = '$id'";
        return mysqli_query($this->conn, $sql);
    }
}
?>