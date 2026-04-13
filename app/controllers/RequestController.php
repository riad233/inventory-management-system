<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";

class RequestController extends Controller {
    
    public function index(){
        $requestModel = $this->model('EquipmentRequest');
        $requests = $requestModel->getAll();
        $this->view('request/view_request', ['requests' => $requests]);
    }
    
    public function create(){
        if(isset($_POST['submit'])){
            require_csrf();
            $requestModel = $this->model('EquipmentRequest');
            
            $data = [
                'user_id' => $_POST['user_id'],
                'equipment_type' => $_POST['equipment_type'],
                'description' => $_POST['description'],
                'vendor_id' => $_POST['vendor_id'] ?? null
            ];
            
            if($requestModel->create($data)){
                header("Location: ?url=request/index&msg=Request submitted successfully");
            }
        }
        
        $employeeModel = $this->model('Employee');
        $this->view('request/request_equipment', ['employees' => $employeeModel->getAll()]);
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
