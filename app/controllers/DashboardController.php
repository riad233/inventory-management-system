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
        
        // Use efficient count methods instead of fetching all data
        $total_assets = $assetModel->getCount();
        $total_assignments = $assignmentModel->getCount();
        $total_pending = $assignmentModel->getPendingCount();
        $total_maintenance = $maintenanceModel->getPendingCount();
        
        // Get recent activity for dashboard
        $recent_assets = $assetModel->getRecent(5);
        $recent_assignments = $assignmentModel->getRecent(5);
        $recent_maintenance = $maintenanceModel->getRecent(5);
        
        $data = [
            'total_assets' => $total_assets,
            'total_assignments' => $total_assignments,
            'total_pending' => $total_pending,
            'total_maintenance' => $total_maintenance,
            'recent_assets' => $recent_assets,
            'recent_assignments' => $recent_assignments,
            'recent_maintenance' => $recent_maintenance
        ];
        
        $this->view('dashboard/dashboard', $data);
    }

}
?>
