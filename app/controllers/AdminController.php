<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/database.php";
require_once ROOT_PATH . "/config/validator.php";
require_once ROOT_PATH . "/config/logger.php";
require_once ROOT_PATH . "/config/authorization.php";

class AdminController extends Controller {
    
    /**
     * Check admin access
     */
    private function checkAdminAccess() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
            http_response_code(403);
            Logger::warning("Unauthorized admin access attempt", ['user_id' => $_SESSION['user_id'] ?? 'unknown']);
            die('Access Denied: Admin role required');
        }
    }
    
    /**
     * Admin Dashboard / Settings Page
     */
    public function index(){
        $this->checkAdminAccess();
        
        try {
            $userModel = $this->model('User');
            $assetModel = $this->model('Asset');
            $employeeModel = $this->model('Employee');
            $vendorModel = $this->model('Vendor');
            
            $data = [
                'title' => 'Admin Settings',
                'total_users' => count($userModel->getAll() ?? []),
                'total_assets' => $assetModel->getCount(),
                'total_employees' => count($employeeModel->getAll() ?? []),
                'total_vendors' => count($vendorModel->getAll() ?? []),
                'system_settings' => [
                    'app_name' => 'Inventory Management System',
                    'app_version' => '1.0.0',
                    'session_timeout' => 1800,
                    'items_per_page' => 20,
                    'maintenance_threshold' => 365
                ]
            ];
            
            Logger::info("Admin dashboard accessed", ['admin_user' => $_SESSION['username'] ?? 'unknown']);
            $this->view('admin/index', $data);
        } catch (Exception $e) {
            Logger::error("Error in AdminController::index", ['error' => $e->getMessage()]);
            die("Error loading admin dashboard");
        }
    }
    
    /**
     * User Management Page
     */
    public function users(){
        $this->checkAdminAccess();
        
        try {
            $userModel = $this->model('User');
            $users = $userModel->getAll();
            
            $data = [
                'title' => 'User Management',
                'users' => $users ?? [],
                'roles' => ['Admin', 'Manager', 'Employee']
            ];
            
            Logger::info("Admin users page accessed", ['admin_user' => $_SESSION['username'] ?? 'unknown']);
            $this->view('admin/users', $data);
        } catch (Exception $e) {
            Logger::error("Error in AdminController::users", ['error' => $e->getMessage()]);
            die("Error loading users");
        }
    }
    
    /**
     * Add new user
     */
    public function addUser(){
        $this->checkAdminAccess();
        
        $errors = [];
        if(isset($_POST['submit'])){
            require_csrf();
            
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $role = $_POST['role'] ?? 'Employee';
            
            // Validate data
            if (empty(trim($username))) {
                $errors['username'] = 'Username is required';
            } elseif (strlen($username) < 3) {
                $errors['username'] = 'Username must be at least 3 characters';
            }
            
            if (empty(trim($email))) {
                $errors['email'] = 'Email is required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Email must be valid';
            }
            
            if (empty(trim($password))) {
                $errors['password'] = 'Password is required';
            } elseif (strlen($password) < 6) {
                $errors['password'] = 'Password must be at least 6 characters';
            }
            
            if ($password !== $confirm_password) {
                $errors['confirm_password'] = 'Passwords do not match';
            }
            
            if (empty($errors)) {
                try {
                    $userModel = $this->model('User');
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    
                    if ($userModel->create([
                        'Username' => $username,
                        'Email' => $email,
                        'Password' => $hashedPassword,
                        'Role' => $role
                    ])) {
                        Logger::info("New user created", ['created_by' => $_SESSION['username'], 'username' => $username, 'role' => $role]);
                        header("Location: ?url=admin/users&msg=User created successfully");
                        exit;
                    } else {
                        $errors['general'] = "Failed to create user";
                    }
                } catch (Exception $e) {
                    $errors['general'] = "Failed to create user: " . $e->getMessage();
                    Logger::error("Error creating user", ['error' => $e->getMessage()]);
                }
            }
        }
        
        $data = [
            'title' => 'Add User',
            'errors' => $errors,
            'roles' => ['Admin', 'Manager', 'Employee']
        ];
        
        $this->view('admin/add_user', $data);
    }
    
    /**
     * Edit user role
     */
    public function editUser($id){
        $this->checkAdminAccess();
        
        Validator::integer('id', $id, 'User ID');
        if (!Validator::passes()) {
            http_response_code(400);
            die("Invalid user ID");
        }
        
        try {
            $userModel = $this->model('User');
            $user = $userModel->getById($id);
            
            if (!$user) {
                http_response_code(404);
                die("User not found");
            }
            
            $errors = [];
            
            if(isset($_POST['submit'])){
                require_csrf();
                
                $role = $_POST['role'] ?? 'Employee';
                
                if ($userModel->update($id, ['Role' => $role])) {
                    Logger::info("User role updated", ['updated_by' => $_SESSION['username'], 'user_id' => $id, 'new_role' => $role]);
                    header("Location: ?url=admin/users&msg=User updated successfully");
                    exit;
                }
            }
            
            $data = [
                'title' => 'Edit User',
                'user' => $user,
                'errors' => $errors,
                'roles' => ['Admin', 'Manager', 'Employee']
            ];
            
            $this->view('admin/edit_user', $data);
        } catch (Exception $e) {
            Logger::error("Error in AdminController::editUser", ['error' => $e->getMessage()]);
            die("Error loading user");
        }
    }
    
    /**
     * Delete user
     */
    public function deleteUser($id){
        $this->checkAdminAccess();
        
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            http_response_code(405);
            die('Method not allowed');
        }
        
        require_csrf();
        
        Validator::integer('id', $id, 'User ID');
        if (!Validator::passes()) {
            http_response_code(400);
            die("Invalid user ID");
        }
        
        try {
            $userModel = $this->model('User');
            
            if ($userModel->delete($id)) {
                Logger::warning("User deleted", ['deleted_by' => $_SESSION['username'], 'user_id' => $id]);
                header("Location: ?url=admin/users&msg=User deleted successfully");
                exit;
            }
        } catch (Exception $e) {
            Logger::error("Error deleting user", ['error' => $e->getMessage()]);
            die("Error deleting user");
        }
    }
    
    /**
     * Activity Logs Page
     */
    public function logs(){
        $this->checkAdminAccess();
        
        try {
            $data = [
                'title' => 'Activity Logs',
                'logs' => [
                    [
                        'timestamp' => date('Y-m-d H:i:s', strtotime('-1 hour')),
                        'user' => $_SESSION['username'] ?? 'admin',
                        'action' => 'Dashboard accessed',
                        'module' => 'Dashboard',
                        'status' => 'success'
                    ],
                    [
                        'timestamp' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                        'user' => 'manager1',
                        'action' => 'Asset created',
                        'module' => 'Assets',
                        'status' => 'success'
                    ]
                ]
            ];
            
            Logger::info("Admin logs page accessed", ['admin_user' => $_SESSION['username'] ?? 'unknown']);
            $this->view('admin/logs', $data);
        } catch (Exception $e) {
            Logger::error("Error in AdminController::logs", ['error' => $e->getMessage()]);
            die("Error loading logs");
        }
    }
}
