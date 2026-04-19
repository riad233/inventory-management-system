<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";
require_once ROOT_PATH . "/config/validator.php";
require_once ROOT_PATH . "/config/logger.php";

class AssetController extends Controller {

    private function validateAssetData($data) {
        Validator::reset();
        
        Validator::required('name', $data['name'] ?? '', 'Asset Name');
        Validator::maxLength('name', $data['name'] ?? '', 255, 'Asset Name');
        
        Validator::required('category', $data['category'] ?? '', 'Category');
        Validator::maxLength('category', $data['category'] ?? '', 100, 'Category');
        
        Validator::required('brand', $data['brand'] ?? '', 'Brand');
        Validator::maxLength('brand', $data['brand'] ?? '', 100, 'Brand');
        
        Validator::maxLength('model', $data['model'] ?? '', 100, 'Model');
        Validator::maxLength('serial', $data['serial'] ?? '', 100, 'Serial Number');
        
        if (!empty($data['purchase_date'])) {
            Validator::date('purchase_date', $data['purchase_date'], 'Purchase Date');
        }
        
        if (!empty($data['warranty'])) {
            Validator::date('warranty', $data['warranty'], 'Warranty Expiry');
        }
        
        $validStatuses = ['Active', 'Inactive', 'Repair', 'Disposal'];
        Validator::in('status', $data['status'] ?? 'Active', $validStatuses, 'Status');
        
        if (!empty($data['vendor_id'])) {
            Validator::integer('vendor_id', $data['vendor_id'], 'Vendor ID');
        }
        
        return Validator::passes();
    }

    public function index(){
        try {
            $asset = $this->model('Asset');
            $assets = $asset->getAll();
            $this->view("asset/view_asset", ['assets' => $assets]);
        } catch (Exception $e) {
            Logger::error("Error in AssetController::index", ['error' => $e->getMessage()]);
            die("An error occurred while retrieving assets");
        }
    }

    public function add(){
        $errors = [];
        
        if(isset($_POST['submit'])){
            require_csrf();
            
            $data = [
                'name' => $_POST['name'] ?? '',
                'category' => $_POST['category'] ?? '',
                'brand' => $_POST['brand'] ?? '',
                'model' => $_POST['model'] ?? '',
                'serial' => $_POST['serial'] ?? '',
                'purchase_date' => $_POST['purchase_date'] ?? '',
                'warranty' => $_POST['warranty'] ?? '',
                'status' => $_POST['status'] ?? 'Active',
                'vendor_id' => $_POST['vendor_id'] ?? null
            ];
            
            // Sanitize data
            foreach ($data as $key => $value) {
                if (is_string($value)) {
                    $data[$key] = Validator::sanitizeString($value);
                }
            }
            
            // Validate data
            if($this->validateAssetData($data)) {
                try {
                    $asset = $this->model('Asset');
                    if($asset->add($data)){
                        Logger::info("Asset created", ['asset_name' => $data['name']]);
                        header("Location: ?url=asset/index&msg=Asset added successfully");
                        exit;
                    } else {
                        $errors['general'] = "Failed to add asset. Please try again.";
                        Logger::warning("Failed to add asset", ['data' => $data]);
                    }
                } catch (Exception $e) {
                    $errors['general'] = "An error occurred while adding the asset";
                    Logger::error("Error adding asset", ['error' => $e->getMessage(), 'data' => $data]);
                }
            } else {
                $errors = Validator::getErrors();
            }
        }
        
        $this->view("asset/add_asset", ['errors' => $errors]);
    }

    public function edit($id){
        Validator::integer('id', $id, 'Asset ID');
        if (!Validator::passes()) {
            http_response_code(400);
            die("Invalid asset ID");
        }
        
        $asset = $this->model('Asset');
        $assetData = $asset->getById($id);
        
        if (!$assetData) {
            http_response_code(404);
            die("Asset not found");
        }
        
        $errors = [];
        
        if(isset($_POST['submit'])){
            require_csrf();
            
            $data = [
                'name' => $_POST['name'] ?? '',
                'category' => $_POST['category'] ?? '',
                'brand' => $_POST['brand'] ?? '',
                'model' => $_POST['model'] ?? '',
                'serial' => $_POST['serial'] ?? '',
                'purchase_date' => $_POST['purchase_date'] ?? '',
                'warranty' => $_POST['warranty'] ?? '',
                'status' => $_POST['status'] ?? 'Active',
                'vendor_id' => $_POST['vendor_id'] ?? null
            ];
            
            // Sanitize data
            foreach ($data as $key => $value) {
                if (is_string($value)) {
                    $data[$key] = Validator::sanitizeString($value);
                }
            }
            
            // Validate data
            if($this->validateAssetData($data)) {
                try {
                    if($asset->update($id, $data)){
                        Logger::info("Asset updated", ['asset_id' => $id, 'asset_name' => $data['name']]);
                        header("Location: ?url=asset/index&msg=Asset updated successfully");
                        exit;
                    } else {
                        $errors['general'] = "Failed to update asset. Please try again.";
                        Logger::warning("Failed to update asset", ['asset_id' => $id]);
                    }
                } catch (Exception $e) {
                    $errors['general'] = "An error occurred while updating the asset";
                    Logger::error("Error updating asset", ['asset_id' => $id, 'error' => $e->getMessage()]);
                }
            } else {
                $errors = Validator::getErrors();
            }
        }
        
        $this->view("asset/edit_asset", ['asset' => $assetData, 'errors' => $errors]);
    }

    public function delete($id){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        require_csrf();
        
        // Require Admin role for deletion
        AuthorizationHelper::requireAdmin();
        
        Validator::integer('id', $id, 'Asset ID');
        if (!Validator::passes()) {
            http_response_code(400);
            die("Invalid asset ID");
        }
        
        try {
            $asset = $this->model('Asset');
            $assetData = $asset->getById($id);
            
            if (!$assetData) {
                http_response_code(404);
                die("Asset not found");
            }
            
            if($asset->delete($id)){
                Logger::info("Asset deleted", ['asset_id' => $id, 'deleted_by' => AuthorizationHelper::getUserId()]);
                header("Location: ?url=asset/index&msg=Asset deleted successfully");
            } else {
                Logger::warning("Failed to delete asset", ['asset_id' => $id]);
                header("Location: ?url=asset/index&msg=Failed to delete asset");
            }
        } catch (Exception $e) {
            Logger::error("Error deleting asset", ['asset_id' => $id, 'error' => $e->getMessage()]);
            header("Location: ?url=asset/index&msg=An error occurred while deleting the asset");
        }
    }
}
?>
