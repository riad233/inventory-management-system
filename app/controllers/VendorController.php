<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";

class VendorController extends Controller {
    
    public function index(){
        $vendorModel = $this->model('Vendor');
        $vendors = $vendorModel->getAll();
        $this->view('vendor/view_vendor', ['vendors' => $vendors]);
    }
    
    public function add(){
        if(isset($_POST['submit'])){
            $vendorModel = $this->model('Vendor');
            
            $data = [
                'vendor_name' => $_POST['vendor_name'],
                'contact_person' => $_POST['contact_person'],
                'contact_number' => $_POST['contact_number'],
                'email' => $_POST['email'],
                'address' => $_POST['address']
            ];
            
            if($vendorModel->create($data)){
                header("Location: ../vendor/index.php?msg=Vendor added successfully");
            }
        }
        
        $this->view('vendor/add_vendor');
    }
    
    public function edit($id){
        $vendorModel = $this->model('Vendor');
        
        if(isset($_POST['submit'])){
            $data = [
                'vendor_name' => $_POST['vendor_name'],
                'contact_person' => $_POST['contact_person'],
                'contact_number' => $_POST['contact_number'],
                'email' => $_POST['email'],
                'address' => $_POST['address']
            ];
            
            if($vendorModel->update($id, $data)){
                header("Location: ../vendor/index.php?msg=Vendor updated successfully");
            }
        }
        
        $vendor = $vendorModel->getById($id);
        $this->view('vendor/edit_vendor', ['vendor' => $vendor]);
    }
    
    public function delete($id){
        $vendorModel = $this->model('Vendor');
        if($vendorModel->delete($id)){
            header("Location: ../vendor/index.php?msg=Vendor deleted");
        }
    }
}
?>
