<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";

class StockController extends Controller {
    
    /**
     * Display all products with alerts
     */
    public function index() {
        if (!isset($_SESSION['username'])) {
            header("Location: ?url=auth/login");
            exit;
        }
        
        $productModel = $this->model('Product');
        $products = $productModel->getAll();
        $alerts = $productModel->getAlerts();
        
        $data = [
            'title' => 'Stock Management',
            'products' => $products,
            'alerts' => $alerts
        ];
        
        $this->view('stock/index', $data);
    }
    
    /**
     * Show add product form
     */
    public function add() {
        if (!isset($_SESSION['username'])) {
            header("Location: ?url=auth/login");
            exit;
        }
        
        // Get departments for dropdown
        global $conn;
        $sql = "SELECT Department_ID, Department_Name FROM department ORDER BY Department_Name";
        $result = $conn->query($sql);
        $departments = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $departments[] = $row;
            }
        }
        
        $data = [
            'title' => 'Add Product',
            'departments' => $departments,
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null
        ];
        
        unset($_SESSION['error'], $_SESSION['success']);
        $this->view('stock/add', $data);
    }
    
    /**
     * Store new product
     */
    public function store() {
        if (!isset($_SESSION['username'])) {
            header("Location: ?url=auth/login");
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?url=stock/index");
            exit;
        }
        
        // Validate inputs
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
        $department_id = isset($_POST['department_id']) ? (int)$_POST['department_id'] : null;
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        
        // Validation
        if (empty($name)) {
            $_SESSION['error'] = "Product name is required";
            header("Location: ?url=stock/add");
            exit;
        }
        
        if ($quantity < 0) {
            $_SESSION['error'] = "Quantity cannot be negative";
            header("Location: ?url=stock/add");
            exit;
        }
        
        if (strlen($name) > 255) {
            $_SESSION['error'] = "Product name is too long";
            header("Location: ?url=stock/add");
            exit;
        }
        
        $productModel = $this->model('Product');
        
        if ($productModel->add($name, $quantity, $department_id, $description)) {
            $_SESSION['success'] = "Product added successfully";
            header("Location: ?url=stock/index");
        } else {
            $_SESSION['error'] = "Failed to add product. Please check if product already exists.";
            header("Location: ?url=stock/add");
        }
        exit;
    }
    
    /**
     * Show edit product form
     */
    public function edit() {
        if (!isset($_SESSION['username'])) {
            header("Location: ?url=auth/login");
            exit;
        }
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id === 0) {
            $_SESSION['error'] = "Invalid product ID";
            header("Location: ?url=stock/index");
            exit;
        }
        
        $productModel = $this->model('Product');
        $product = $productModel->getById($id);
        
        if (!$product) {
            $_SESSION['error'] = "Product not found";
            header("Location: ?url=stock/index");
            exit;
        }
        
        // Get departments for dropdown
        global $conn;
        $sql = "SELECT Department_ID, Department_Name FROM department ORDER BY Department_Name";
        $result = $conn->query($sql);
        $departments = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $departments[] = $row;
            }
        }
        
        $data = [
            'title' => 'Edit Product',
            'product' => $product,
            'departments' => $departments,
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null
        ];
        
        unset($_SESSION['error'], $_SESSION['success']);
        $this->view('stock/edit', $data);
    }
    
    /**
     * Update product
     */
    public function update() {
        if (!isset($_SESSION['username'])) {
            header("Location: ?url=auth/login");
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ?url=stock/index");
            exit;
        }
        
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
        $department_id = isset($_POST['department_id']) ? (int)$_POST['department_id'] : null;
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        
        // Validation
        if ($id === 0) {
            $_SESSION['error'] = "Invalid product ID";
            header("Location: ?url=stock/index");
            exit;
        }
        
        if (empty($name)) {
            $_SESSION['error'] = "Product name is required";
            header("Location: ?url=stock/edit&id=$id");
            exit;
        }
        
        if ($quantity < 0) {
            $_SESSION['error'] = "Quantity cannot be negative";
            header("Location: ?url=stock/edit&id=$id");
            exit;
        }
        
        $productModel = $this->model('Product');
        
        if ($productModel->update($id, $name, $quantity, $department_id, $description)) {
            $_SESSION['success'] = "Product updated successfully";
            header("Location: ?url=stock/index");
        } else {
            $_SESSION['error'] = "Failed to update product";
            header("Location: ?url=stock/edit&id=$id");
        }
        exit;
    }
    
    /**
     * Delete product
     */
    public function delete() {
        if (!isset($_SESSION['username'])) {
            header("Location: ?url=auth/login");
            exit;
        }
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id === 0) {
            $_SESSION['error'] = "Invalid product ID";
            header("Location: ?url=stock/index");
            exit;
        }
        
        $productModel = $this->model('Product');
        
        if ($productModel->delete($id)) {
            $_SESSION['success'] = "Product deleted successfully";
        } else {
            $_SESSION['error'] = "Failed to delete product";
        }
        
        header("Location: ?url=stock/index");
        exit;
    }
}
?>
