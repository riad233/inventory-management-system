<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";
require_once ROOT_PATH . "/config/logger.php";

class User extends Model {

    public function login($username){
        $sql = "SELECT * FROM users WHERE Username = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getAll(){
        $sql = "SELECT * FROM users ORDER BY User_ID DESC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            Logger::error("Query prepare failed", ['error' => $this->conn->error]);
            return [];
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $users = [];
        
        if ($result) {
            while($row = $result->fetch_assoc()){
                $users[] = $row;
            }
        }
        return $users;
    }

    public function getById($id){
        $sql = "SELECT * FROM users WHERE User_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function create($data){
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (Username, Password, Email, Role) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $data['username'], $hash, $data['email'], $data['role']);
        return $stmt->execute();
    }

    public function update($id, $data){
        $sql = "UPDATE users SET Username = ?, Email = ?, Role = ? WHERE User_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi", $data['username'], $data['email'], $data['role'], $id);
        return $stmt->execute();
    }

    public function updatePassword($id, $password){
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET Password = ? WHERE User_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $hash, $id);
        return $stmt->execute();
    }

    public function delete($id){
        $sql = "DELETE FROM users WHERE User_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>