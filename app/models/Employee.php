<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Model.php";

class Employee extends Model {
    protected $table = 'employee';

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
        // Wrap both INSERTs in a transaction so an orphan user record can never
        // be left behind if the employee INSERT fails.
        $this->conn->begin_transaction();

        try {
            // Generate a unique username from the employee's name
            $baseUsername = strtolower(str_replace([' ', "'"], ['.', ''], $data['name']));
            $username = $baseUsername;
            $counter  = 1;

            while (true) {
                $checkStmt = $this->conn->prepare("SELECT User_ID FROM users WHERE Username = ? LIMIT 1");
                $checkStmt->bind_param("s", $username);
                $checkStmt->execute();
                $checkStmt->store_result();
                $exists = $checkStmt->num_rows > 0;
                $checkStmt->close();
                if (!$exists) break;
                $username = $baseUsername . $counter;
                $counter++;
            }

            $default_password = password_hash('password123', PASSWORD_BCRYPT);
            $userStmt = $this->conn->prepare(
                "INSERT INTO users (Username, Password, Email, Role) VALUES (?, ?, ?, 'Employee')"
            );
            $userStmt->bind_param("sss", $username, $default_password, $data['email']);
            $userStmt->execute();
            $userStmt->close();

            $userId = $this->conn->insert_id;

            $empStmt = $this->conn->prepare(
                "INSERT INTO employee (User_ID, Name, Designation, Contact_Number, Email, Department_ID)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $empStmt->bind_param(
                "issssi",
                $userId,
                $data['name'],
                $data['designation'],
                $data['contact'],
                $data['email'],
                $data['dept_id']
            );
            $empStmt->execute();
            $empStmt->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            Logger::error("Employee create failed", ['error' => $e->getMessage()]);
            return false;
        }
    }

    public function update($id, $data){
        $sql = "UPDATE employee SET Name = ?, Designation = ?, Contact_Number = ?, Email = ?, Department_ID = ? WHERE User_ID = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssii", $data['name'], $data['designation'], $data['contact'], $data['email'], $data['dept_id'], $id);
        return $stmt->execute();
    }

    public function delete($id){
        // Use a transaction so both rows are removed atomically.
        // If only employee is deleted the users record becomes an orphan.
        $this->conn->begin_transaction();
        try {
            $stmt = $this->conn->prepare("DELETE FROM employee WHERE User_ID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            $stmt = $this->conn->prepare("DELETE FROM users WHERE User_ID = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            Logger::error("Employee delete failed", ['id' => $id, 'error' => $e->getMessage()]);
            return false;
        }
    }
}
?>