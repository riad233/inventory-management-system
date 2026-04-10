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
            $requestModel = $this->model('EquipmentRequest');
            
            $data = [
                'user_id' => $_POST['user_id'],
                'equipment_type' => $_POST['equipment_type'],
                'description' => $_POST['description']
            ];
            
            if($requestModel->create($data)){
                header("Location: ../request/index.php?msg=Request submitted successfully");
            }
        }
        
        $employeeModel = $this->model('Employee');
        $this->view('request/request_equipment', ['employees' => $employeeModel->getAll()]);
    }
    
    public function approve($id){
        $requestModel = $this->model('EquipmentRequest');
        $approved_by = $_SESSION['user_id'] ?? 1;
        
        if($requestModel->approve($id, $approved_by)){
            header("Location: ../request/index.php?msg=Request approved");
        }
    }
    
    public function reject($id){
        $requestModel = $this->model('EquipmentRequest');
        
        if($requestModel->reject($id)){
            header("Location: ../request/index.php?msg=Request rejected");
        }
    }
    
    public function delete($id){
        $requestModel = $this->model('EquipmentRequest');
        if($requestModel->delete($id)){
            header("Location: ../request/index.php?msg=Request deleted");
        }
    }
}
?>
