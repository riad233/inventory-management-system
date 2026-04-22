<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";
require_once ROOT_PATH . "/config/validator.php";
require_once ROOT_PATH . "/config/logger.php";
require_once ROOT_PATH . "/config/authorization.php";

class AssignmentController extends Controller {
    
    public function index(){
        $assignmentModel = $this->model('Assignment');
        $assignments = $assignmentModel->getAll();
        $this->view('assignment/view_assignment', ['assignments' => $assignments]);
    }
    
    public function assign(){
        $errors = [];
        if(isset($_POST['submit'])){
            require_csrf();
            
            if (!$this->validateAssignment()) {
                $errors = Validator::getErrors();
            } else {
                $assignmentModel = $this->model('Assignment');
                
                $data = [
                    'asset_id' => Validator::sanitizeInt($_POST['asset_id']),
                    'user_id' => Validator::sanitizeInt($_POST['user_id']),
                    'dept_id' => Validator::sanitizeInt($_POST['dept_id']),
                    'exp_return_date' => Validator::sanitizeString($_POST['exp_return_date'])
                ];
                
                if($assignmentModel->create($data)){
                    header("Location: ?url=assignment/index&msg=Asset assigned successfully");
                    exit;
                }
            }
        }
        
        $assetModel = $this->model('Asset');
        $employeeModel = $this->model('Employee');
        $departmentModel = $this->model('Department');
        
        $data = [
            'assets' => $assetModel->getAll(),
            'employees' => $employeeModel->getAll(),
            'departments' => $departmentModel->getAll(),
            'errors' => $errors
        ];
        
        $this->view('assignment/assign_asset', $data);
    }
    
    public function returnAsset(){
        if(isset($_POST['submit'])){
            require_csrf();
            $assignmentModel = $this->model('Assignment');
            $id = $_POST['assignment_id'];
            
            $data = [
                'condition' => $_POST['condition']
            ];
            
            if($assignmentModel->returnAsset($id, $data)){
                header("Location: ?url=assignment/index&msg=Asset returned successfully");
            }
        }
        
        $assignmentModel = $this->model('Assignment');
        $assignments = $assignmentModel->getAll();
        
        $this->view('assignment/return_asset', ['assignments' => $assignments]);
    }
    
    public function delete($id){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        require_csrf();
        AuthorizationHelper::requireAdmin();
        $assignmentModel = $this->model('Assignment');
        if($assignmentModel->delete($id)){
            header("Location: ?url=assignment/index&msg=Assignment deleted");
        }
    }
    
    private function validateAssignment() {
        Validator::reset();
        Validator::required('asset_id', $_POST['asset_id'] ?? '', 'Asset');
        Validator::required('user_id', $_POST['user_id'] ?? '', 'Employee');
        Validator::required('dept_id', $_POST['dept_id'] ?? '', 'Department');
        Validator::required('exp_return_date', $_POST['exp_return_date'] ?? '', 'Return Date');
        Validator::date('exp_return_date', $_POST['exp_return_date'] ?? '', 'Return Date');
        return Validator::passes();
    }
}
?>
