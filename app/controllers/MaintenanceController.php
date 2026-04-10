<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";

class MaintenanceController extends Controller {
    
    public function index(){
        $maintenanceModel = $this->model('Maintenance');
        $maintenance = $maintenanceModel->getAll();
        $this->view('maintenance/view_maintenance', ['maintenance' => $maintenance]);
    }
    
    public function add(){
        if(isset($_POST['submit'])){
            $maintenanceModel = $this->model('Maintenance');
            
            $data = [
                'asset_id' => $_POST['asset_id'],
                'cost' => $_POST['cost']
            ];
            
            if($maintenanceModel->create($data)){
                header("Location: ../maintenance/index.php?msg=Maintenance record created");
            }
        }
        
        $assetModel = $this->model('Asset');
        $this->view('maintenance/maintenance_record', ['assets' => $assetModel->getAll()]);
    }
    
    public function updateStatus(){
        if(isset($_POST['submit'])){
            $maintenanceModel = $this->model('Maintenance');
            $id = $_POST['maintenance_id'];
            $status = $_POST['status'];
            $end_date = $_POST['end_date'];
            
            if($maintenanceModel->updateStatus($id, $status, $end_date)){
                header("Location: ../maintenance/index.php?msg=Maintenance status updated");
            }
        }
        
        $maintenanceModel = $this->model('Maintenance');
        $maintenance = $maintenanceModel->getAll();
        $this->view('maintenance/update_maintenance', ['maintenance' => $maintenance]);
    }
    
    public function delete($id){
        $maintenanceModel = $this->model('Maintenance');
        if($maintenanceModel->delete($id)){
            header("Location: ../maintenance/index.php?msg=Maintenance deleted");
        }
    }
}
?>
