<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";
require_once ROOT_PATH . "/config/validator.php";
require_once ROOT_PATH . "/config/logger.php";

class VendorController extends Controller {
    
    private function validateVendor($data) {
        Validator::reset();
        Validator::required('vendor_name', $data['vendor_name'] ?? '', 'Vendor Name');
        Validator::maxLength('vendor_name', $data['vendor_name'] ?? '', 255, 'Vendor Name');
        Validator::required('contact_person', $data['contact_person'] ?? '', 'Contact Person');
        Validator::required('contact_number', $data['contact_number'] ?? '', 'Contact Number');
        Validator::phone('contact_number', $data['contact_number'] ?? '', 'Contact Number');
        Validator::required('email', $data['email'] ?? '', 'Email');
        Validator::email('email', $data['email'] ?? '', 'Email');
        Validator::required('address', $data['address'] ?? '', 'Address');
        Validator::required('vendor_type', $data['vendor_type'] ?? '', 'Vendor Type');
        Validator::required('vendor_status', $data['vendor_status'] ?? '', 'Vendor Status');
        return Validator::passes();
    }
    
    public function index(){
        $vendorModel = $this->model('Vendor');
        $vendors = $vendorModel->getAll();
        $this->view('vendor/view_vendor', ['vendors' => $vendors]);
    }
    
    public function add(){
        $errors = [];
        if(isset($_POST['submit'])){
            require_csrf();
            $data = [
                'vendor_name' => $_POST['vendor_name'] ?? '',
                'contact_person' => $_POST['contact_person'] ?? '',
                'contact_number' => $_POST['contact_number'] ?? '',
                'email' => $_POST['email'] ?? '',
                'address' => $_POST['address'] ?? '',
                'vendor_type' => $_POST['vendor_type'] ?? '',
                'vendor_status' => $_POST['vendor_status'] ?? ''
            ];
            foreach ($data as $key => $value) {
                if (is_string($value)) $data[$key] = Validator::sanitizeString($value);
            }
            if($this->validateVendor($data)) {
                try {
                    $vendorModel = $this->model('Vendor');
                    if($vendorModel->create($data)){
                        Logger::info("Vendor created", ['vendor_name' => $data['vendor_name']]);
                        header("Location: ?url=vendor/index&msg=Vendor added successfully");
                        exit;
                    } else {
                        $errors['general'] = "Failed to add vendor";
                    }
                } catch (Exception $e) {
                    Logger::error("Error adding vendor", ['error' => $e->getMessage()]);
                    $errors['general'] = "An error occurred";
                }
            } else {
                $errors = Validator::getErrors();
            }
        }
        $this->view('vendor/add_vendor', ['errors' => $errors]);
    }
    
    public function edit($id){
        Validator::integer('id', $id, 'Vendor ID');
        if (!Validator::passes()) { http_response_code(400); die("Invalid ID"); }
        $vendorModel = $this->model('Vendor');
        $vendor = $vendorModel->getById($id);
        if (!$vendor) { http_response_code(404); die("Not found"); }
        $errors = [];
        if(isset($_POST['submit'])){
            require_csrf();
            $data = [
                'vendor_name' => $_POST['vendor_name'] ?? '',
                'contact_person' => $_POST['contact_person'] ?? '',
                'contact_number' => $_POST['contact_number'] ?? '',
                'email' => $_POST['email'] ?? '',
                'address' => $_POST['address'] ?? '',
                'vendor_type' => $_POST['vendor_type'] ?? '',
                'vendor_status' => $_POST['vendor_status'] ?? ''
            ];
            foreach ($data as $key => $value) {
                if (is_string($value)) $data[$key] = Validator::sanitizeString($value);
            }
            if($this->validateVendor($data)) {
                try {
                    if($vendorModel->update($id, $data)){
                        Logger::info("Vendor updated", ['vendor_id' => $id]);
                        header("Location: ?url=vendor/index&msg=Vendor updated successfully");
                        exit;
                    } else {
                        $errors['general'] = "Failed to update vendor";
                    }
                } catch (Exception $e) {
                    Logger::error("Error updating vendor", ['vendor_id' => $id, 'error' => $e->getMessage()]);
                    $errors['general'] = "An error occurred";
                }
            } else {
                $errors = Validator::getErrors();
            }
        }
        $this->view('vendor/edit_vendor', ['vendor' => $vendor, 'errors' => $errors]);
    }
    
    public function delete($id){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        require_csrf();
        $vendorModel = $this->model('Vendor');
        if($vendorModel->delete($id)){
            header("Location: ?url=vendor/index&msg=Vendor deleted");
        }
    }
}
?>
