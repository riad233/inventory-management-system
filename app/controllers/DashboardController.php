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
        $inventoryModel = $this->model('Inventory');
        
        $assets = $assetModel->getAll();
        $assignments = $assignmentModel->getAll();
        $maintenance = $maintenanceModel->getAll();
        $stockAlerts = $inventoryModel->getLowAndOutStock(10);
        
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
            'maintenance' => $maintenance,
            'stock_alerts' => $stockAlerts
        ];
        
        $this->view('dashboard/dashboard', $data);
    }
    
    /**
     * Display full stock alerts page
     */
    public function stockAlerts() {
        if (!isset($_SESSION['username'])) {
            header("Location: ?url=auth/login");
            exit;
        }
        
        $inventoryModel = $this->model('Inventory');
        $stockAlerts = $inventoryModel->getLowAndOutStock(10);
        
        $data = [
            'stock_alerts' => $stockAlerts,
            'title' => 'Stock Alerts'
        ];
        
        $this->view('dashboard/stock_alerts', $data);
    }
}
?>
