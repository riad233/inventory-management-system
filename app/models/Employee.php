<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";

class Employee extends Model {

    public function getAll(){
        $sql = "SELECT e.*, d.Department_Name FROM employee e LEFT JOIN department d ON e.Department_ID = d.Department_ID";
        $result = mysqli_query($this->conn, $sql);
        $employees = [];
        while($row = mysqli_fetch_assoc($result)){
            $employees[] = $row;
        }
        return $employees;
    }

    public function getById($id){
        $sql = "SELECT * FROM employee WHERE User_ID = '$id'";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function create($data){
        $sql = "INSERT INTO employee (Name, Designation, Contact_Number, Email, Department_ID) 
                VALUES ('{$data['name']}', '{$data['designation']}', '{$data['contact']}', '{$data['email']}', '{$data['dept_id']}')";
        return mysqli_query($this->conn, $sql);
    }

    public function update($id, $data){
        $sql = "UPDATE employee SET Name='{$data['name']}', Designation='{$data['designation']}', Contact_Number='{$data['contact']}', Email='{$data['email']}', Department_ID='{$data['dept_id']}' WHERE User_ID = '$id'";
        return mysqli_query($this->conn, $sql);
    }

    public function delete($id){
        $sql = "DELETE FROM employee WHERE User_ID = '$id'";
        return mysqli_query($this->conn, $sql);
    }
}
?>