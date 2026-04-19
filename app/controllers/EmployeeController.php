<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";
require_once ROOT_PATH . "/config/validator.php";
require_once ROOT_PATH . "/config/logger.php";

class EmployeeController extends Controller {
    
    private function validateEmployee($data) {
        Validator::reset();
        Validator::required('name', $data['name'] ?? '', 'Employee Name');
        Validator::maxLength('name', $data['name'] ?? '', 255, 'Employee Name');
        Validator::required('designation', $data['designation'] ?? '', 'Designation');
        Validator::maxLength('designation', $data['designation'] ?? '', 100, 'Designation');
        Validator::required('contact', $data['contact'] ?? '', 'Contact Number');
        Validator::phone('contact', $data['contact'] ?? '', 'Contact Number');
        Validator::required('email', $data['email'] ?? '', 'Email');
        Validator::email('email', $data['email'] ?? '', 'Email');
        Validator::required('dept_id', $data['dept_id'] ?? '', 'Department');
        Validator::integer('dept_id', $data['dept_id'] ?? '', 'Department ID');
        return Validator::passes();
    }
    
    public function index(){
        try {
            $employeeModel = $this->model('Employee');
            $employees = $employeeModel->getAll();
            $this->view('employee/view_employee', ['employees' => $employees]);
        } catch (Exception $e) {
            Logger::error("Error in EmployeeController::index", ['error' => $e->getMessage()]);
            die("An error occurred");
        }
    }
    
    public function add(){
        $errors = [];
        if(isset($_POST['submit'])){
            require_csrf();
            
            $data = [
                'name' => $_POST['name'] ?? '',
                'designation' => $_POST['designation'] ?? '',
                'contact' => $_POST['contact'] ?? '',
                'email' => $_POST['email'] ?? '',
                'dept_id' => $_POST['dept_id'] ?? ''
            ];
            
            // Sanitize
            $data['name'] = Validator::sanitizeString($data['name']);
            $data['designation'] = Validator::sanitizeString($data['designation']);
            $data['contact'] = Validator::sanitizeString($data['contact']);
            $data['email'] = Validator::sanitizeEmail($data['email']);
            $data['dept_id'] = Validator::sanitizeInt($data['dept_id']);
            
            if($this->validateEmployee($data)) {
                try {
                    $employeeModel = $this->model('Employee');
                    if($employeeModel->create($data)){
                        Logger::info("Employee added", ['name' => $data['name']]);
                        header("Location: ?url=employee/index&msg=Employee added successfully");
                        exit;
                    } else {
                        $errors['general'] = "Failed to add employee";
                    }
                } catch (Exception $e) {
                    Logger::error("Error adding employee", ['error' => $e->getMessage()]);
                    $errors['general'] = "An error occurred";
                }
            } else {
                $errors = Validator::getErrors();
            }
        }
        
        try {
            $departmentModel = $this->model('Department');
            $departments = $departmentModel->getAll();
            $this->view('employee/add_employee', ['departments' => $departments, 'errors' => $errors]);
        } catch (Exception $e) {
            Logger::error("Error loading departments", ['error' => $e->getMessage()]);
            die("An error occurred");
        }
    }
    
    public function edit($id){
        Validator::integer('id', $id, 'Employee ID');
        if (!Validator::passes()) { http_response_code(400); die("Invalid ID"); }
        
        $employeeModel = $this->model('Employee');
        $employee = $employeeModel->getById($id);
        if (!$employee) { http_response_code(404); die("Not found"); }
        
        $errors = [];
        if(isset($_POST['submit'])){
            require_csrf();
            
            $data = [
                'name' => $_POST['name'] ?? '',
                'designation' => $_POST['designation'] ?? '',
                'contact' => $_POST['contact'] ?? '',
                'email' => $_POST['email'] ?? '',
                'dept_id' => $_POST['dept_id'] ?? ''
            ];
            
            // Sanitize
            $data['name'] = Validator::sanitizeString($data['name']);
            $data['designation'] = Validator::sanitizeString($data['designation']);
            $data['contact'] = Validator::sanitizeString($data['contact']);
            $data['email'] = Validator::sanitizeEmail($data['email']);
            $data['dept_id'] = Validator::sanitizeInt($data['dept_id']);
            
            if($this->validateEmployee($data)) {
                try {
                    if($employeeModel->update($id, $data)){
                        Logger::info("Employee updated", ['employee_id' => $id]);
                        header("Location: ?url=employee/index&msg=Employee updated successfully");
                        exit;
                    } else {
                        $errors['general'] = "Failed to update employee";
                    }
                } catch (Exception $e) {
                    Logger::error("Error updating employee", ['employee_id' => $id, 'error' => $e->getMessage()]);
                    $errors['general'] = "An error occurred";
                }
            } else {
                $errors = Validator::getErrors();
            }
        }
        
        try {
            $departmentModel = $this->model('Department');
            $departments = $departmentModel->getAll();
            $this->view('employee/edit_employee', ['employee' => $employee, 'departments' => $departments, 'errors' => $errors]);
        } catch (Exception $e) {
            Logger::error("Error loading departments", ['employee_id' => $id, 'error' => $e->getMessage()]);
            die("An error occurred");
        }
    }
    
    public function delete($id){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        require_csrf();
        
        Validator::integer('id', $id, 'Employee ID');
        if (!Validator::passes()) { http_response_code(400); die("Invalid ID"); }
        
        try {
            $employeeModel = $this->model('Employee');
            if($employeeModel->delete($id)){
                Logger::info("Employee deleted", ['employee_id' => $id]);
                header("Location: ?url=employee/index&msg=Employee deleted successfully");
                exit;
            } else {
                Logger::warning("Failed to delete employee", ['employee_id' => $id]);
                header("Location: ?url=employee/index&msg=Failed to delete employee");
            }
        } catch (Exception $e) {
            Logger::error("Error deleting employee", ['employee_id' => $id, 'error' => $e->getMessage()]);
            header("Location: ?url=employee/index&msg=An error occurred");
        }
    }
}
?>
?>
