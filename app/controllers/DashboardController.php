<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";

class DashboardController extends Controller {
    
    public function index(){
        if(!isset($_SESSION['username'])){
            header("Location: ?url=auth/login");
        }
        
        $assetModel = $this->model('Asset');
        $assignmentModel = $this->model('Assignment');
        $maintenanceModel = $this->model('Maintenance');
        
        $assets = $assetModel->getAll();
        $assignments = $assignmentModel->getAll();
        $maintenance = $maintenanceModel->getAll();
        
        $total_assets = count($assets);
        $total_pending = 0;
        $total_maintenance = 0;
        
        foreach($assignments as $assign){
            if($assign['Actual_Return_Date'] == null){
                $total_pending++;
            }
        }
        
        foreach($maintenance as $maint){
            if($maint['Status'] == 'Pending'){
                $total_maintenance++;
            }
        }
        
        $data = [
            'total_assets' => $total_assets,
            'total_assignments' => count($assignments),
            'total_pending' => $total_pending,
            'total_maintenance' => $total_maintenance,
            'assets' => $assets,
            'assignments' => $assignments,
            'maintenance' => $maintenance
        ];
        
        $this->view('dashboard/dashboard', $data);
    }
}
?>
