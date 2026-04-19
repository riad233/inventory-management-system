<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";
require_once ROOT_PATH . "/config/validator.php";
require_once ROOT_PATH . "/config/logger.php";

class AssignmentController extends Controller {
    
    private function validateAssignmentData($data) {
        Validator::reset();
        
        Validator::required('asset_id', $data['asset_id'] ?? '', 'Asset');
        Validator::integer('asset_id', $data['asset_id'] ?? '', 'Asset ID');
        
        Validator::required('user_id', $data['user_id'] ?? '', 'Employee');
        Validator::integer('user_id', $data['user_id'] ?? '', 'Employee ID');
        
        Validator::required('dept_id', $data['dept_id'] ?? '', 'Department');
        Validator::integer('dept_id', $data['dept_id'] ?? '', 'Department ID');
        
        Validator::required('exp_return_date', $data['exp_return_date'] ?? '', 'Expected Return Date');
        Validator::date('exp_return_date', $data['exp_return_date'] ?? '', 'Expected Return Date');
        
        return Validator::passes();
    }
    
    private function validateReturnData($data) {
        Validator::reset();
        
        Validator::required('condition', $data['condition'] ?? '', 'Asset Condition');
        $validConditions = ['Good', 'Damaged', 'Lost', 'Partially Damaged'];
        Validator::in('condition', $data['condition'] ?? '', $validConditions, 'Asset Condition');
        
        return Validator::passes();
    }
    
    public function index(){
        try {
            $assignmentModel = $this->model('Assignment');
            $assignments = $assignmentModel->getAll();
            $this->view('assignment/view_assignment', ['assignments' => $assignments]);
        } catch (Exception $e) {
            Logger::error("Error in AssignmentController::index", ['error' => $e->getMessage()]);
            die("An error occurred while retrieving assignments");
        }
    }
    
    public function assign(){
        $errors = [];
        $assetModel = $this->model('Asset');
        $employeeModel = $this->model('Employee');
        $departmentModel = $this->model('Department');
        
        if(isset($_POST['submit'])){
            require_csrf();
            
            $data = [
                'asset_id' => $_POST['asset_id'] ?? '',
                'user_id' => $_POST['user_id'] ?? '',
                'dept_id' => $_POST['dept_id'] ?? '',
                'exp_return_date' => $_POST['exp_return_date'] ?? ''
            ];
            
            // Sanitize numeric inputs
            $data['asset_id'] = Validator::sanitizeInt($data['asset_id']);
            $data['user_id'] = Validator::sanitizeInt($data['user_id']);
            $data['dept_id'] = Validator::sanitizeInt($data['dept_id']);
            $data['exp_return_date'] = Validator::sanitizeString($data['exp_return_date']);
            
            // Validate data
            if($this->validateAssignmentData($data)) {
                try {
                    // Verify asset, employee, and department exist
                    $asset = $assetModel->getById($data['asset_id']);
                    $employee = $employeeModel->getById($data['user_id']);
                    $dept = $departmentModel->getById($data['dept_id']);
                    
                    if (!$asset) {
                        $errors['asset_id'] = "Selected asset does not exist";
                    } elseif (!$employee) {
                        $errors['user_id'] = "Selected employee does not exist";
                    } elseif (!$dept) {
                        $errors['dept_id'] = "Selected department does not exist";
                    }
                    
                    if (empty($errors)) {
                        $assignmentModel = $this->model('Assignment');
                        if($assignmentModel->create($data)){
                            Logger::info("Asset assigned", ['asset_id' => $data['asset_id'], 'employee_id' => $data['user_id']]);
                            header("Location: ?url=assignment/index&msg=Asset assigned successfully");
                            exit;
                        } else {
                            $errors['general'] = "Failed to assign asset. Please try again.";
                            Logger::warning("Failed to assign asset", ['data' => $data]);
                        }
                    }
                } catch (Exception $e) {
                    $errors['general'] = "An error occurred while assigning the asset";
                    Logger::error("Error assigning asset", ['error' => $e->getMessage(), 'data' => $data]);
                }
            } else {
                $errors = Validator::getErrors();
            }
        }
        
        try {
            $viewData = [
                'assets' => $assetModel->getAll(),
                'employees' => $employeeModel->getAll(),
                'departments' => $departmentModel->getAll(),
                'errors' => $errors
            ];
            $this->view('assignment/assign_asset', $viewData);
        } catch (Exception $e) {
            Logger::error("Error loading assignment form", ['error' => $e->getMessage()]);
            die("An error occurred while loading the assignment form");
        }
    }
    
    public function returnAsset(){
        $errors = [];
        
        if(isset($_POST['submit'])){
            require_csrf();
            
            $assignmentId = $_POST['assignment_id'] ?? '';
            $condition = $_POST['condition'] ?? '';
            
            // Sanitize inputs
            $assignmentId = Validator::sanitizeInt($assignmentId);
            $condition = Validator::sanitizeString($condition);
            
            $data = ['condition' => $condition];
            
            // Validate data
            if($this->validateReturnData($data)) {
                try {
                    Validator::integer('assignment_id', $assignmentId, 'Assignment ID');
                    if (!Validator::passes()) {
                        $errors['assignment_id'] = "Invalid assignment ID";
                    } else {
                        $assignmentModel = $this->model('Assignment');
                        if($assignmentModel->returnAsset($assignmentId, $data)){
                            Logger::info("Asset returned", ['assignment_id' => $assignmentId]);
                            header("Location: ?url=assignment/index&msg=Asset returned successfully");
                            exit;
                        } else {
                            $errors['general'] = "Failed to return asset. Please try again.";
                            Logger::warning("Failed to return asset", ['assignment_id' => $assignmentId]);
                        }
                    }
                } catch (Exception $e) {
                    $errors['general'] = "An error occurred while returning the asset";
                    Logger::error("Error returning asset", ['error' => $e->getMessage()]);
                }
            } else {
                $errors = Validator::getErrors();
            }
        }
        
        try {
            $assignmentModel = $this->model('Assignment');
            $assignments = $assignmentModel->getAll();
            $this->view('assignment/return_asset', ['assignments' => $assignments, 'errors' => $errors]);
        } catch (Exception $e) {
            Logger::error("Error loading return form", ['error' => $e->getMessage()]);
            die("An error occurred while loading the return form");
        }
    }
    
    public function delete($id){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        require_csrf();
        
        Validator::integer('id', $id, 'Assignment ID');
        if (!Validator::passes()) {
            http_response_code(400);
            die("Invalid assignment ID");
        }
        
        try {
            $assignmentModel = $this->model('Assignment');
            if($assignmentModel->delete($id)){
                Logger::info("Assignment deleted", ['assignment_id' => $id]);
                header("Location: ?url=assignment/index&msg=Assignment deleted successfully");
            } else {
                Logger::warning("Failed to delete assignment", ['assignment_id' => $id]);
                header("Location: ?url=assignment/index&msg=Failed to delete assignment");
            }
        } catch (Exception $e) {
            Logger::error("Error deleting assignment", ['assignment_id' => $id, 'error' => $e->getMessage()]);
            header("Location: ?url=assignment/index&msg=An error occurred while deleting the assignment");
        }
    }
}
?>
