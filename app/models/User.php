<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";

class User extends Model {

    public function login($username, $password){
        $sql = "SELECT * FROM users WHERE Username=? AND Password=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getAll(){
        $sql = "SELECT * FROM users";
        $result = mysqli_query($this->conn, $sql);
        $users = [];
        while($row = mysqli_fetch_assoc($result)){
            $users[] = $row;
        }
        return $users;
    }

    public function getById($id){
        $sql = "SELECT * FROM users WHERE User_ID = '$id'";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($result);
    }

    public function create($data){
        $sql = "INSERT INTO users (Username, Password, Email, Role) VALUES ('{$data['username']}', '{$data['password']}', '{$data['email']}', '{$data['role']}')";
        return mysqli_query($this->conn, $sql);
    }

    public function update($id, $data){
        $sql = "UPDATE users SET Username='{$data['username']}', Email='{$data['email']}', Role='{$data['role']}' WHERE User_ID = '$id'";
        return mysqli_query($this->conn, $sql);
    }

    public function delete($id){
        $sql = "DELETE FROM users WHERE User_ID = '$id'";
        return mysqli_query($this->conn, $sql);
    }
}
?>