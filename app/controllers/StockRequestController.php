<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";

class StockRequestController extends Controller {
    
    /**
     * Display request form
     * If ?product_id=X&source=alert - auto-fill product info
     * Otherwise - show product selection
     */
    public function create() {
        if (!isset($_SESSION['username'])) {
            header("Location: ?url=auth/login");
            exit;
        }
        
        $productModel = $this->model('Product');
        $product = null;
        $isAlert = false;
        
        $product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
        $source = isset($_GET['source']) ? sanitize($_GET['source']) : '';
        $type = isset($_GET['type']) ? sanitize($_GET['type']) : 'normal';
        
        // If alert request - load specific product
        if ($source === 'alert' && $product_id > 0) {
            $product = $productModel->getById($product_id);
            if (!$product) {
                $_SESSION['error'] = "Product not found";
                header("Location: ?url=stock/index");
                exit;
            }
            $isAlert = true;
        } else {
            // Show all products for selection
            $product = null;
        }
        
        // Get all products for dropdown
        $products = $productModel->getAll();
        
        $data = [
            'title' => $isAlert ? 'Request from Alert' : 'Create Stock Request',
            'product' => $product,
            'products' => $products,
            'isAlert' => $isAlert,
            'type' => $type,
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null
        ];
        
        unset($_SESSION['error'], $_SESSION['success']);
        $this->view('stock/request_create', $data);
    }
    
    /**
     * Store request
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
        
        // Get current user ID
        global $conn;
        $username = $_SESSION['username'];
        $userSql = "SELECT User_ID FROM users WHERE Username = ?";
        $userStmt = $conn->prepare($userSql);
        
        if (!$userStmt) {
            $_SESSION['error'] = "Database error";
            header("Location: ?url=stock/index");
            exit;
        }
        
        $userStmt->bind_param("s", $username);
        $userStmt->execute();
        $userResult = $userStmt->get_result();
        $userRow = $userResult->fetch_assoc();
        $user_id = $userRow ? $userRow['User_ID'] : null;
        
        if (!$user_id) {
            $_SESSION['error'] = "User not found";
            header("Location: ?url=stock/index");
            exit;
        }
        
        // Validate inputs
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        $type = isset($_POST['type']) ? sanitize($_POST['type']) : 'normal';
        $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
        
        // Validation
        if ($product_id === 0) {
            $_SESSION['error'] = "Please select a product";
            header("Location: ?url=stock-request/create");
            exit;
        }
        
        if ($quantity <= 0) {
            $_SESSION['error'] = "Quantity must be greater than 0";
            header("Location: ?url=stock-request/create?product_id=$product_id");
            exit;
        }
        
        if (!in_array($type, ['normal', 'emergency'])) {
            $_SESSION['error'] = "Invalid request type";
            header("Location: ?url=stock-request/create?product_id=$product_id");
            exit;
        }
        
        // Verify product exists
        $productModel = $this->model('Product');
        $product = $productModel->getById($product_id);
        
        if (!$product) {
            $_SESSION['error'] = "Product not found";
            header("Location: ?url=stock/index");
            exit;
        }
        
        // Create request
        $requestModel = $this->model('Request');
        
        if ($requestModel->create($product_id, $type, $quantity, $user_id, $notes)) {
            $_SESSION['success'] = "Request created successfully";
            header("Location: ?url=stock/index");
        } else {
            $_SESSION['error'] = "Failed to create request";
            header("Location: ?url=stock-request/create?product_id=$product_id");
        }
        exit;
    }
    
    /**
     * View all requests (admin/manager only)
     */
    public function index() {
        if (!isset($_SESSION['username'])) {
            header("Location: ?url=auth/login");
            exit;
        }
        
        $requestModel = $this->model('Request');
        $requests = $requestModel->getAll();
        
        $data = [
            'title' => 'Stock Requests',
            'requests' => $requests,
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null
        ];
        
        unset($_SESSION['error'], $_SESSION['success']);
        $this->view('stock/requests_list', $data);
    }
    
    /**
     * Approve request
     */
    public function approve() {
        if (!isset($_SESSION['username'])) {
            header("Location: ?url=auth/login");
            exit;
        }
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id === 0) {
            $_SESSION['error'] = "Invalid request ID";
            header("Location: ?url=stock-request/index");
            exit;
        }
        
        $requestModel = $this->model('Request');
        
        if ($requestModel->updateStatus($id, 'approved')) {
            $_SESSION['success'] = "Request approved";
        } else {
            $_SESSION['error'] = "Failed to approve request";
        }
        
        header("Location: ?url=stock-request/index");
        exit;
    }
    
    /**
     * Reject request
     */
    public function reject() {
        if (!isset($_SESSION['username'])) {
            header("Location: ?url=auth/login");
            exit;
        }
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id === 0) {
            $_SESSION['error'] = "Invalid request ID";
            header("Location: ?url=stock-request/index");
            exit;
        }
        
        $requestModel = $this->model('Request');
        
        if ($requestModel->updateStatus($id, 'rejected')) {
            $_SESSION['success'] = "Request rejected";
        } else {
            $_SESSION['error'] = "Failed to reject request";
        }
        
        header("Location: ?url=stock-request/index");
        exit;
    }
}

// Helper function
function sanitize($data) {
    return trim(htmlspecialchars($data, ENT_QUOTES, 'UTF-8'));
}
?>
