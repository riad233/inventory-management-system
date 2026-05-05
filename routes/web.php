<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

require_once ROOT_PATH . "/core/Router.php";
require_once ROOT_PATH . "/config/config.php";


// Get the URL
$url = isset($_GET['url']) ? $_GET['url'] : '';

// ========== STEP 5: API ROUTING ==========
// Detect and route API requests (format: /api/resource/action)
if (!empty($url) && strpos($url, 'api/') === 0) {
    // Parse API route: api/assets, api/employees, etc.
    $parts = explode('/', $url);
    
    if (isset($parts[1])) {
        $resource = strtolower($parts[1]);  // assets, employees, etc.
        $action = $parts[2] ?? 'index';      // action method
        
        // Map resource names to controller class names
        $resourceMap = [
            'assets' => 'Api_AssetController',
            'employees' => 'Api_EmployeeController',
            'vendors' => 'Api_VendorController',
            'assignments' => 'Api_AssignmentController',
            'maintenance' => 'Api_MaintenanceController',
            'requests' => 'Api_EquipmentRequestController',
            'equipment-requests' => 'Api_EquipmentRequestController',
            'health' => 'Api_HealthCheckController',
        ];
        
        if (isset($resourceMap[$resource])) {
            $controllerClass = $resourceMap[$resource];
            
            // Instantiate and call action
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                
                // Convert action name (e.g., 'change-status' -> 'changeStatus')
                $methodName = lcfirst(str_replace('-', '', ucwords($action, '-')));
                
                if (method_exists($controller, $methodName)) {
                    $controller->$methodName();
                    exit;
                }
            }
            
            // Controller or method not found
            ApiResponse::notFound('Endpoint', $url);
            exit;
        }
        
        // Resource not found
        ApiResponse::notFound('Resource', $resource);
        exit;
    }
    
    // Invalid API route
    ApiResponse::error('invalid_route', 'Invalid API route format', 400);
    exit;
}

// ========== REGULAR ROUTING ==========
if(empty($url)) {
    $url = 'home/index';
}

Router::route($url);
