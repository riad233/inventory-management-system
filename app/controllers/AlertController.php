<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
}

require_once ROOT_PATH . "/core/Controller.php";
require_once ROOT_PATH . "/config/logger.php";

class AlertController extends Controller {
    
    /**
     * Get all alerts as JSON (for bell icon)
     * Used by JavaScript to fetch alert data dynamically
     */
    public function getAlerts() {
        // Set header first
        header('Content-Type: application/json');
        
        // Check authentication
        if (empty($_SESSION['username'])) {
            http_response_code(401);
            die(json_encode(['success' => false, 'error' => 'Unauthorized']));
        }
        
        try {
            $alertModel = $this->model('Alert');
            $alerts = $alertModel->getAll();
            $count = $alertModel->getCount();
            
            // Return JSON response
            echo json_encode([
                'success' => true,
                'count' => $count,
                'alerts' => is_array($alerts) ? $alerts : []
            ]);
            exit;
        } catch (Exception $e) {
            Logger::error("Error fetching alerts", ['error' => $e->getMessage()]);
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch alerts']);
            exit;
        }
    }
    
    /**
     * Get alert count only (for badge update)
     */
    public function getCount() {
        // Set header first
        header('Content-Type: application/json');
        
        // Check authentication
        if (empty($_SESSION['username'])) {
            http_response_code(401);
            die(json_encode(['success' => false, 'error' => 'Unauthorized']));
        }
        
        try {
            $alertModel = $this->model('Alert');
            $count = $alertModel->getCount();
            
            echo json_encode([
                'success' => true,
                'count' => (int)$count
            ]);
            exit;
        } catch (Exception $e) {
            Logger::error("Error fetching alert count", ['error' => $e->getMessage()]);
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch alert count']);
            exit;
        }
    }
}
?>

