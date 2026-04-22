<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";
require_once ROOT_PATH . "/config/logger.php";

class DashboardController extends Controller {
    
    public function index(){
        if(!isset($_SESSION['username'])){
            header("Location: ?url=auth/login");
        }
        
        $assetModel = $this->model('Asset');
        $assignmentModel = $this->model('Assignment');
        $maintenanceModel = $this->model('Maintenance');
        $employeeModel = $this->model('Employee');
        $vendorModel = $this->model('Vendor');
        $userModel = $this->model('User');
        
        try {
            // Use efficient count methods instead of fetching all data
            $total_assets = $assetModel->getCount();
            $total_assignments = $assignmentModel->getCount();
            $total_pending = $assignmentModel->getPendingCount();
            $total_maintenance = $maintenanceModel->getPendingCount();
            $total_employees = count($employeeModel->getAll() ?? []);
            $total_vendors = count($vendorModel->getAll() ?? []);
            $total_users = count($userModel->getAll() ?? []);
            
            // Get recent activity for dashboard
            $recent_assets = $assetModel->getRecent(5);
            $recent_assignments = $assignmentModel->getRecent(5);
            $recent_maintenance = $maintenanceModel->getRecent(5);
            
            // Get stock alerts (simulated)
            $stock_alerts = [
                'total_out' => 0,
                'total_low' => 0,
                'out_items' => [],
                'low_items' => []
            ];
            
            $data = [
                'total_assets' => $total_assets,
                'total_assignments' => $total_assignments,
                'total_pending' => $total_pending,
                'total_maintenance' => $total_maintenance,
                'total_employees' => $total_employees,
                'total_vendors' => $total_vendors,
                'total_users' => $total_users,
                'recent_assets' => $recent_assets,
                'recent_assignments' => $recent_assignments,
                'recent_maintenance' => $recent_maintenance,
                'stock_alerts' => $stock_alerts
            ];
            
            $this->view('dashboard/dashboard', $data);
        } catch (Exception $e) {
            Logger::error("Error in DashboardController::index", ['error' => $e->getMessage()]);
            die("Error loading dashboard: " . $e->getMessage());
        }
    }

    /**
     * Stock alerts page
     */
    public function stockAlerts(){
        if(!isset($_SESSION['username'])){
            header("Location: ?url=auth/login");
        }
        
        try {
            $data = [
                'title' => 'Stock Alerts',
                'alerts' => []
            ];
            
            $this->view('dashboard/stock_alerts', $data);
        } catch (Exception $e) {
            Logger::error("Error in DashboardController::stockAlerts", ['error' => $e->getMessage()]);
            die("Error loading stock alerts");
        }
    }
}
?>
