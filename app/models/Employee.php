<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";
require_once ROOT_PATH . "/config/logger.php";

class Employee extends Model {

    public function getAll(){
        $sql = "SELECT e.*, d.Department_Name FROM employee e LEFT JOIN department d ON e.Department_ID = d.Department_ID ORDER BY e.User_ID DESC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            Logger::error("Query prepare failed", ['error' => $this->conn->error]);
            return [];
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $employees = [];
        
        if ($result) {
            while($row = $result->fetch_assoc()){
                $employees[] = $row;
            }
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
        // First create a user account for the employee
        $username = strtolower(str_replace(' ', '.', $data['name']));
        $default_password = password_hash('password123', PASSWORD_BCRYPT);
        
        $userSql = "INSERT INTO users (Username, Password, Email, Role) VALUES (?, ?, ?, 'Employee')";
        $userStmt = $this->conn->prepare($userSql);
        $userStmt->bind_param("sss", $username, $default_password, $data['email']);
        
        if(!$userStmt->execute()) {
            return false;
        }
        
        // Get the newly created user ID
        $userId = $this->conn->insert_id;
        
        // Now create the employee record with the new user ID
        $sql = "INSERT INTO employee (User_ID, Name, Designation, Contact_Number, Email, Department_ID) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("issssi", $userId, $data['name'], $data['designation'], $data['contact'], $data['email'], $data['dept_id']);
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