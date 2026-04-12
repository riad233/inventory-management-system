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
        $sql = "SELECT * FROM employee WHERE User_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function create($data){
        $sql = "INSERT INTO employee (Name, Designation, Contact_Number, Email, Department_ID) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssi", $data['name'], $data['designation'], $data['contact'], $data['email'], $data['dept_id']);
        return $stmt->execute();
    }

    public function update($id, $data){
        $sql = "UPDATE employee SET Name = ?, Designation = ?, Contact_Number = ?, Email = ?, Department_ID = ? WHERE User_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssii", $data['name'], $data['designation'], $data['contact'], $data['email'], $data['dept_id'], $id);
        return $stmt->execute();
    }

    public function delete($id){
        $sql = "DELETE FROM employee WHERE User_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>