<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";

class EmployeeController extends Controller {
    
    public function index(){
        $employeeModel = $this->model('Employee');
        $employees = $employeeModel->getAll();
        $this->view('employee/view_employee', ['employees' => $employees]);
    }
    
    public function add(){
        if(isset($_POST['submit'])){
            $employeeModel = $this->model('Employee');
            
            $data = [
                'name' => $_POST['name'],
                'designation' => $_POST['designation'],
                'contact' => $_POST['contact'],
                'email' => $_POST['email'],
                'dept_id' => $_POST['dept_id']
            ];
            
            if($employeeModel->create($data)){
                header("Location: ?url=employee/index&msg=Employee added successfully");
                exit;
            }
        }
        
        global $conn;
        $deptQuery = mysqli_query($conn, "SELECT * FROM department");
        $departments = [];
        while($row = mysqli_fetch_assoc($deptQuery)){
            $departments[] = $row;
        }

        $this->view('employee/add_employee', ['departments' => $departments]);
    }
    
    public function edit($id){
        $employeeModel = $this->model('Employee');
        
        if(isset($_POST['submit'])){
            $data = [
                'name' => $_POST['name'],
                'designation' => $_POST['designation'],
                'contact' => $_POST['contact'],
                'email' => $_POST['email'],
                'dept_id' => $_POST['dept_id']
            ];
            
            if($employeeModel->update($id, $data)){
                header("Location: ?url=employee/index&msg=Employee updated successfully");
                exit;
            }
        }
        
        $employee = $employeeModel->getById($id);
        
        global $conn;
        $deptQuery = mysqli_query($conn, "SELECT * FROM department");
        $departments = [];
        while($row = mysqli_fetch_assoc($deptQuery)){
            $departments[] = $row;
        }

        $this->view('employee/edit_employee', ['employee' => $employee, 'departments' => $departments]);
    }
    
    public function delete($id){
        $employeeModel = $this->model('Employee');
        if($employeeModel->delete($id)){
            header("Location: ?url=employee/index&msg=Employee deleted");
            exit;
        }
    }
}
?>
