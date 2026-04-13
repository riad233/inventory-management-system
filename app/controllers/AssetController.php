<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";

class AssetController extends Controller {

    public function index(){
        $asset = $this->model('Asset');
        $assets = $asset->getAll();
        $this->view("asset/view_asset", ['assets' => $assets]);
    }

    public function add(){
        if(isset($_POST['submit'])){
            require_csrf();
            $asset = $this->model('Asset');
            
            $data = [
                'name' => $_POST['name'],
                'category' => $_POST['category'],
                'brand' => $_POST['brand'],
                'model' => $_POST['model'],
                'serial' => $_POST['serial'],
                'purchase_date' => $_POST['purchase_date'],
                'warranty' => $_POST['warranty'],
                'status' => $_POST['status']
            ];
            
            if($asset->add($data)){
                header("Location: ?url=asset/index&msg=Asset added successfully");
            }
        }
        
        $this->view("asset/add_asset");
    }

    public function edit($id){
        $asset = $this->model('Asset');
        
        if(isset($_POST['submit'])){
            require_csrf();
            $data = [
                'name' => $_POST['name'],
                'category' => $_POST['category'],
                'brand' => $_POST['brand'],
                'model' => $_POST['model'],
                'serial' => $_POST['serial'],
                'purchase_date' => $_POST['purchase_date'],
                'warranty' => $_POST['warranty'],
                'status' => $_POST['status'],
                'vendor_id' => $_POST['vendor_id'] ?? null
            ];
            
            if($asset->update($id, $data)){
                header("Location: ?url=asset/index&msg=Asset updated successfully");
            }
        }
        
        $assetData = $asset->getById($id);
        $this->view("asset/edit_asset", ['asset' => $assetData]);
    }

    public function delete($id){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        require_csrf();
        $asset = $this->model('Asset');
        if($asset->delete($id)){
            header("Location: ?url=asset/index&msg=Asset deleted");
        }
    }
}
?>
