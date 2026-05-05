<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";
require_once ROOT_PATH . "/config/logger.php";
require_once ROOT_PATH . "/app/models/Asset.php";
require_once ROOT_PATH . "/app/models/Assignment.php";
require_once ROOT_PATH . "/app/models/Maintenance.php";
require_once ROOT_PATH . "/app/models/Employee.php";
require_once ROOT_PATH . "/app/models/Vendor.php";
require_once ROOT_PATH . "/app/models/EquipmentRequest.php";

class DashboardController extends Controller {
    
    public function index(){
        try {
            // Use Models directly (compatible with database schema)
            $asset = new Asset();
            $assignment = new Assignment();
            $maintenance = new Maintenance();
            $employee = new Employee();
            $vendor = new Vendor();
            $request = new EquipmentRequest();
            
            // Fetch data using models (simple, works with database schema)
            $total_assets      = $asset->count();
            $total_assignments = $assignment->count();
            $total_pending     = 0;  // TODO: Add countPending to Assignment model
            $total_maintenance = $maintenance->count();
            $pending_requests  = 0;  // TODO: Add countPending to EquipmentRequest model
            $total_employees   = $employee->count();
            $total_vendors     = $vendor->count();
            
            // Recent records
            $recent_assets      = $asset->getAll();  // Get all for now
            $recent_assignments = $assignment->getAll();
            $recent_maintenance = $maintenance->getAll();

            // Status counts
            $asset_status_counts       = [];
            $maintenance_status_counts = [];
            
            $data = [
                'total_assets'             => $total_assets,
                'total_assignments'        => $total_assignments,
                'total_pending'            => $total_pending,
                'total_maintenance'        => $total_maintenance,
                'pending_requests'         => $pending_requests,
                'total_employees'          => $total_employees,
                'total_vendors'            => $total_vendors,
                'recent_assets'            => $recent_assets,
                'recent_assignments'       => $recent_assignments,
                'recent_maintenance'       => $recent_maintenance,
                'asset_status_counts'      => $asset_status_counts,
                'maintenance_status_counts'=> $maintenance_status_counts,
            ];
            
            $this->view('dashboard/dashboard', $data);
        } catch (Exception $e) {
            Logger::error("Error in DashboardController::index", ['error' => $e->getMessage()]);
            throw $e; // Let ExceptionHandler render the error page
        }
    }

}

?>
