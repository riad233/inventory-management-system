<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";
require_once ROOT_PATH . "/config/validator.php";
require_once ROOT_PATH . "/config/logger.php";

class MaintenanceController extends Controller {
    
    private function validateMaintenance($data) {
        Validator::reset();
        Validator::required('asset_id', $data['asset_id'] ?? '', 'Asset');
        Validator::integer('asset_id', $data['asset_id'] ?? '', 'Asset ID');
        Validator::required('maintenance_status', $data['maintenance_status'] ?? '', 'Status');
        Validator::required('cost', $data['cost'] ?? '', 'Cost');
        Validator::numeric('cost', $data['cost'] ?? '', 'Cost');
        if (($data['cost'] ?? 0) > 0) Validator::positive('cost', $data['cost'], 'Cost');
        return Validator::passes();
    }
    
    public function index(){
        $maintenanceModel = $this->model('Maintenance');
        $maintenance = $maintenanceModel->getAll();
        $this->view('maintenance/view_maintenance', ['maintenance' => $maintenance]);
    }
    
    public function add(){
        $errors = [];
        if(isset($_POST['submit'])){
            require_csrf();
            $data = [
                'asset_id' => $_POST['asset_id'] ?? '',
                'maintenance_status' => $_POST['maintenance_status'] ?? '',
                'cost' => $_POST['cost'] ?? '0',
                'vendor_id' => $_POST['vendor_id'] ?? null
            ];
            $data['asset_id'] = Validator::sanitizeInt($data['asset_id']);
            $data['maintenance_status'] = Validator::sanitizeString($data['maintenance_status']);
            $data['cost'] = Validator::sanitizeInt($data['cost']);
            if($this->validateMaintenance($data)) {
                try {
                    $maintenanceModel = $this->model('Maintenance');
                    if($maintenanceModel->create($data)){
                        Logger::info("Maintenance record created", ['asset_id' => $data['asset_id']]);
                        header("Location: ?url=maintenance/index&msg=Maintenance record created");
                        exit;
                    } else { 
                        $errors['general'] = "Failed to create record"; 
                    }
                } catch (Exception $e) {
                    Logger::error("Error in maintenance", ['error' => $e->getMessage()]);
                    $errors['general'] = "An error occurred";
                }
            } else { 
                $errors = Validator::getErrors(); 
            }
        }
        $assetModel = $this->model('Asset');
        $this->view('maintenance/maintenance_record', ['assets' => $assetModel->getAll(), 'errors' => $errors]);
    }
    
    public function updateStatus(){
        if(isset($_POST['submit'])){
            require_csrf();
            $maintenanceModel = $this->model('Maintenance');
            $id = $_POST['maintenance_id'];
            $status = $_POST['status'];
            $end_date = $_POST['end_date'];
            $vendor_id = $_POST['vendor_id'] ?? null;
            
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
