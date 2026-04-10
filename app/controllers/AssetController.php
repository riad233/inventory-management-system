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
                header("Location: ../asset/index.php?msg=Asset added successfully");
            }
        }
        
        $this->view("asset/add_asset");
    }

    public function edit($id){
        $asset = $this->model('Asset');
        
        if(isset($_POST['submit'])){
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
            
            if($asset->update($id, $data)){
                header("Location: ../asset/index.php?msg=Asset updated successfully");
            }
        }
        
        $assetData = $asset->getById($id);
        $this->view("asset/edit_asset", ['asset' => $assetData]);
    }

    public function delete($id){
        $asset = $this->model('Asset');
        if($asset->delete($id)){
            header("Location: ../asset/index.php?msg=Asset deleted");
        }
    }
}
?>
