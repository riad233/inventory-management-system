<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";

class EquipmentRequest extends Model {

    public function getAll(){
        $sql = "SELECT r.*, e.Name FROM equipment_request r LEFT JOIN employee e ON r.User_ID = e.User_ID";
        $result = mysqli_query($this->conn, $sql);
        $requests = [];
        while($row = mysqli_fetch_assoc($result)){
            $requests[] = $row;
        }
        return $requests;
    }

    public function getById($id){
        $sql = "SELECT * FROM equipment_request WHERE Request_ID = '$id'";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function create($data){
        $request_date = date('Y-m-d');
        $sql = "INSERT INTO equipment_request (User_ID, Equipment_Type, Description, Request_Date, Status) 
                VALUES ('{$data['user_id']}', '{$data['equipment_type']}', '{$data['description']}', '$request_date', 'Pending')";
        return mysqli_query($this->conn, $sql);
    }

    public function approve($id, $approved_by){
        $approval_date = date('Y-m-d');
        $sql = "UPDATE equipment_request SET Status='Approved', Approval_Date='$approval_date', Approved_By='$approved_by' WHERE Request_ID = '$id'";
        return mysqli_query($this->conn, $sql);
    }

    public function reject($id){
        $sql = "UPDATE equipment_request SET Status='Rejected' WHERE Request_ID = '$id'";
        return mysqli_query($this->conn, $sql);
    }

    public function update($id, $data){
        $sql = "UPDATE equipment_request SET Equipment_Type='{$data['equipment_type']}', Description='{$data['description']}' WHERE Request_ID = '$id'";
        return mysqli_query($this->conn, $sql);
    }

    public function delete($id){
        $sql = "DELETE FROM equipment_request WHERE Request_ID = '$id'";
        return mysqli_query($this->conn, $sql);
    }
}
?>
