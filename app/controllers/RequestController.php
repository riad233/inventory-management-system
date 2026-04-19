<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";
require_once ROOT_PATH . "/config/validator.php";
require_once ROOT_PATH . "/config/logger.php";

class RequestController extends Controller {
    
    private function validateRequest($data) {
        Validator::reset();
        Validator::required('user_id', $data['user_id'] ?? '', 'Employee');
        Validator::integer('user_id', $data['user_id'] ?? '', 'Employee ID');
        Validator::required('equipment_type', $data['equipment_type'] ?? '', 'Equipment Type');
        Validator::maxLength('equipment_type', $data['equipment_type'] ?? '', 255, 'Equipment Type');
        Validator::required('description', $data['description'] ?? '', 'Description');
        Validator::maxLength('description', $data['description'] ?? '', 1000, 'Description');
        return Validator::passes();
    }
    
    public function index(){
        $requestModel = $this->model('EquipmentRequest');
        $requests = $requestModel->getAll();
        $this->view('request/view_request', ['requests' => $requests]);
    }
    
    public function create(){
        $errors = [];
        if(isset($_POST['submit'])){
            require_csrf();
            $data = [
                'user_id' => $_POST['user_id'] ?? '',
                'equipment_type' => $_POST['equipment_type'] ?? '',
                'description' => $_POST['description'] ?? '',
                'vendor_id' => $_POST['vendor_id'] ?? null
            ];
            $data['user_id'] = Validator::sanitizeInt($data['user_id']);
            $data['equipment_type'] = Validator::sanitizeString($data['equipment_type']);
            $data['description'] = Validator::sanitizeString($data['description']);
            if($this->validateRequest($data)) {
                try {
                    $requestModel = $this->model('EquipmentRequest');
                    if($requestModel->create($data)){
                        Logger::info("Equipment request created", ['user_id' => $data['user_id']]);
                        header("Location: ?url=request/index&msg=Request submitted successfully");
                        exit;
                    } else { 
                        $errors['general'] = "Failed to submit request"; 
                    }
                } catch (Exception $e) {
                    Logger::error("Error creating request", ['error' => $e->getMessage()]);
                    $errors['general'] = "An error occurred";
                }
            } else { 
                $errors = Validator::getErrors(); 
            }
        }
        $employeeModel = $this->model('Employee');
        $this->view('request/request_equipment', ['employees' => $employeeModel->getAll(), 'errors' => $errors]);
    }
    
    public function edit($id){
        $requestModel = $this->model('EquipmentRequest');
        
        if(isset($_POST['submit'])){
            require_csrf();
            $data = [
                'equipment_type' => $_POST['equipment_type'],
                'description' => $_POST['description'],
                'vendor_id' => $_POST['vendor_id'] ?? null
            ];
            
            if($requestModel->update($id, $data)){
                header("Location: ?url=request/index&msg=Request updated successfully");
                exit;
            }
        }
        
        $request = $requestModel->getById($id);
        $employeeModel = $this->model('Employee');
        
        $this->view('request/edit_request', [
            'request' => $request, 
            'employees' => $employeeModel->getAll()
        ]);
    }
    
    public function approve($id){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        require_csrf();
        $requestModel = $this->model('EquipmentRequest');
        $approved_by = $_SESSION['user_id'] ?? 1;
        
        if($requestModel->approve($id, $approved_by)){
            header("Location: ?url=request/index&msg=Request approved");
        }
    }
    
    public function reject($id){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        require_csrf();
        $requestModel = $this->model('EquipmentRequest');
        
        if($requestModel->reject($id)){
            header("Location: ?url=request/index&msg=Request rejected");
        }
    }
    
    public function delete($id){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        require_csrf();
        $requestModel = $this->model('EquipmentRequest');
        if($requestModel->delete($id)){
            header("Location: ?url=request/index&msg=Request deleted");
        }
    }
}
?>
