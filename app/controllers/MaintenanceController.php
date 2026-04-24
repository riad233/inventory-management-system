<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";
require_once ROOT_PATH . "/config/validator.php";
require_once ROOT_PATH . "/config/logger.php";
require_once ROOT_PATH . "/config/authorization.php";

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
        $errors = [];
        if(isset($_POST['submit'])){
            require_csrf();
            
            Validator::reset();
            Validator::required('maintenance_id', $_POST['maintenance_id'] ?? '', 'Maintenance ID');
            Validator::required('status', $_POST['status'] ?? '', 'Status');
            $validStatuses = ['Pending', 'In Progress', 'Approved', 'Rejected', 'Completed'];
            if (!in_array($_POST['status'] ?? '', $validStatuses)) {
                Validator::addError('status', 'Invalid status selected');
            }
            if (isset($_POST['end_date']) && !empty($_POST['end_date'])) {
                Validator::date('end_date', $_POST['end_date'], 'Completion Date');
            }
            
            if (Validator::passes()) {
                $maintenanceModel = $this->model('Maintenance');
                $id = Validator::sanitizeInt($_POST['maintenance_id']);
                $status = Validator::sanitizeString($_POST['status']);
                $end_date = isset($_POST['end_date']) ? Validator::sanitizeString($_POST['end_date']) : null;
                
                if($maintenanceModel->updateStatus($id, $status, $end_date)){
                    header("Location: ?url=maintenance/index&msg=Maintenance status updated");
                    exit;
                }
            } else {
                $errors = Validator::getErrors();
            }
        }
        
        $maintenanceModel = $this->model('Maintenance');
        $maintenance = $maintenanceModel->getAll();
        $this->view('maintenance/update_maintenance', ['maintenance' => $maintenance, 'errors' => $errors]);
    }
    
    public function delete($id){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        require_csrf();
        AuthorizationHelper::requireAdmin();
        $maintenanceModel = $this->model('Maintenance');
        if($maintenanceModel->delete($id)){
            header("Location: ?url=maintenance/index&msg=Maintenance deleted");
        }
    }
}
?>
