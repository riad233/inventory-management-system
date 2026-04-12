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
        $sql = "SELECT * FROM assignment WHERE Assignment_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function create($data){
        $assigned_date = date('Y-m-d');
        $sql = "INSERT INTO assignment (Asset_ID, User_ID, Department_ID, Assigned_Date, Expected_Return_Date) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "iiiss",
            $data['asset_id'],
            $data['user_id'],
            $data['dept_id'],
            $assigned_date,
            $data['exp_return_date']
        );
        return $stmt->execute();
    }

    public function returnAsset($id, $data){
        $actual_return = date('Y-m-d');
        $sql = "UPDATE assignment SET Actual_Return_Date = ?, Condition_on_Return = ? WHERE Assignment_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssi", $actual_return, $data['condition'], $id);
        return $stmt->execute();
    }

    public function update($id, $data){
        $sql = "UPDATE assignment SET Asset_ID = ?, User_ID = ?, Department_ID = ?, Expected_Return_Date = ? WHERE Assignment_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiisi", $data['asset_id'], $data['user_id'], $data['dept_id'], $data['exp_return_date'], $id);
        return $stmt->execute();
    }

    public function delete($id){
        $sql = "DELETE FROM assignment WHERE Assignment_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>