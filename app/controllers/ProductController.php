<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";

class ProductController extends Controller {
    
    private $uploadDir = '';
    private $maxFileSize = 5242880; // 5MB
    private $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    private $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    
    public function __construct() {
        $this->uploadDir = ROOT_PATH . '/public/uploads/products/';
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    private function validateProductData($data) {
        Validator::reset();
        Validator::required('name', $data['name'] ?? '', 'Product Name');
        Validator::maxLength('name', $data['name'] ?? '', 255, 'Product Name');
        Validator::required('category', $data['category'] ?? '', 'Category');
        Validator::required('description', $data['description'] ?? '', 'Description');
        Validator::maxLength('description', $data['description'] ?? '', 1000, 'Description');
        if (!empty($data['price'])) {
            Validator::numeric('price', $data['price'] ?? '', 'Price');
            Validator::positive('price', $data['price'] ?? '', 'Price');
        }
        return Validator::passes();
    }
    
    private function handleFileUpload($file) {
        if (empty($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Validator::reset();
            Validator::addError('file', 'Upload failed. Please try again.');
            return null;
        }
        
        // Validate file
        if ($file['size'] > $this->maxFileSize) {
            Validator::reset();
            Validator::addError('file', 'File size exceeds 5MB limit');
            return null;
        }
        
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $this->allowedMimes, true)) {
            Validator::reset();
            Validator::addError('file', 'File type not allowed');
            return null;
        }
        
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->allowedExtensions, true)) {
            Validator::reset();
            Validator::addError('file', 'File extension not allowed');
            return null;
        }
        
        // Generate unique filename
        $filename = 'product_' . uniqid() . '.' . $ext;
        $uploadPath = $this->uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            Logger::info("File uploaded", ['filename' => $filename]);
            return $filename;
        } else {
            Logger::error("File upload failed", ['file' => $file['name']]);
            Validator::reset();
            Validator::addError('file', 'Failed to save file');
            return null;
        }
    }
    
    public function index() {
        try {
            $this->view("product/product_page", [
                'title' => 'Product Masterfile - IMS'
            ]);
        } catch (Exception $e) {
            Logger::error("Error in ProductController::index", ['error' => $e->getMessage()]);
            die("An error occurred");
        }
    }

    public function add() {
        $errors = [];
        $formData = [];
        
        if(isset($_POST['submit'])){
            require_csrf();
            
            $formData = [
                'name' => $_POST['name'] ?? '',
                'category' => $_POST['category'] ?? '',
                'description' => $_POST['description'] ?? '',
                'price' => $_POST['price'] ?? '0'
            ];
            
            // Sanitize inputs
            foreach ($formData as $key => $value) {
                if (is_string($value)) {
                    $formData[$key] = Validator::sanitizeString($value);
                }
            }
            
            // Validate data
            if($this->validateProductData($formData)) {
                try {
                    // Handle file upload
                    $filename = null;
                    if (isset($_FILES['image'])) {
                        $filename = $this->handleFileUpload($_FILES['image']);
                        if (!Validator::passes()) {
                            $errors = Validator::getErrors();
                        }
                    }
                    
                    if (empty($errors)) {
                        // Save to database
                        // Note: Add product model and table as needed
                        Logger::info("Product created", ['name' => $formData['name'], 'file' => $filename]);
                        header("Location: ?url=product/index&msg=Product added successfully");
                        exit;
                    }
                } catch (Exception $e) {
                    Logger::error("Error adding product", ['error' => $e->getMessage()]);
                    $errors['general'] = "An error occurred";
                }
            } else {
                $errors = Validator::getErrors();
            }
        }
        
        $this->view("product/product_page", [
            'title' => 'Add Product - IMS',
            'formData' => $formData,
            'errors' => $errors
        ]);
    }
}
?>
