<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";

class MaintenanceController extends Controller {
    
    public function index(){
        $maintenanceModel = $this->model('Maintenance');
        $maintenance = $maintenanceModel->getAll();
        $this->view('maintenance/view_maintenance', ['maintenance' => $maintenance]);
    }
    
    public function add(){
        if(isset($_POST['submit'])){
            require_csrf();
            $maintenanceModel = $this->model('Maintenance');
            
            $data = [
                'asset_id' => $_POST['asset_id'],
                'cost' => $_POST['cost']
            ];
            
            if($maintenanceModel->create($data)){
                header("Location: ?url=maintenance/index&msg=Maintenance record created");
            }
        }
        
        $assetModel = $this->model('Asset');
        $this->view('maintenance/maintenance_record', ['assets' => $assetModel->getAll()]);
    }
    
    public function updateStatus(){
        if(isset($_POST['submit'])){
            require_csrf();
            $maintenanceModel = $this->model('Maintenance');
            $id = $_POST['maintenance_id'];
            $status = $_POST['status'];
            $end_date = $_POST['end_date'];
            
            if($maintenanceModel->updateStatus($id, $status, $end_date)){
                header("Location: ?url=maintenance/index&msg=Maintenance status updated");
            }
        }
        
        $maintenanceModel = $this->model('Maintenance');
        $maintenance = $maintenanceModel->getAll();
        $this->view('maintenance/update_maintenance', ['maintenance' => $maintenance]);
    }
    
    public function delete($id){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        require_csrf();
        $maintenanceModel = $this->model('Maintenance');
        if($maintenanceModel->delete($id)){
            header("Location: ?url=maintenance/index&msg=Maintenance deleted");
        }
    }
}
?>
