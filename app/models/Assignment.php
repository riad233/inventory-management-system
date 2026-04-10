<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";

class Assignment extends Model {

    public function getAll(){
        $sql = "SELECT a.*, ast.Asset_Name, e.Name 
                FROM assignment a 
                LEFT JOIN asset ast ON a.Asset_ID = ast.Asset_ID 
                LEFT JOIN employee e ON a.User_ID = e.User_ID";
        $result = mysqli_query($this->conn, $sql);
        $assignments = [];
        while($row = mysqli_fetch_assoc($result)){
            $assignments[] = $row;
        }
        return $assignments;
    }

    public function getById($id){
        $sql = "SELECT * FROM assignment WHERE Assignment_ID = '$id'";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function create($data){
        $assigned_date = date('Y-m-d');
        $sql = "INSERT INTO assignment (Asset_ID, User_ID, Department_ID, Assigned_Date, Expected_Return_Date) 
                VALUES ('{$data['asset_id']}', '{$data['user_id']}', '{$data['dept_id']}', '$assigned_date', '{$data['exp_return_date']}')";
        return mysqli_query($this->conn, $sql);
    }

    public function returnAsset($id, $data){
        $actual_return = date('Y-m-d');
        $sql = "UPDATE assignment SET Actual_Return_Date='$actual_return', Condition_on_Return='{$data['condition']}' WHERE Assignment_ID = '$id'";
        return mysqli_query($this->conn, $sql);
    }

    public function update($id, $data){
        $sql = "UPDATE assignment SET Asset_ID='{$data['asset_id']}', User_ID='{$data['user_id']}', Department_ID='{$data['dept_id']}', Expected_Return_Date='{$data['exp_return_date']}' WHERE Assignment_ID = '$id'";
        return mysqli_query($this->conn, $sql);
    }

    public function delete($id){
        $sql = "DELETE FROM assignment WHERE Assignment_ID = '$id'";
        return mysqli_query($this->conn, $sql);
    }
}
?>