<?php
/**
 * Authorization Helper Class
 * Provides methods for checking permissions and ownership
 */

class AuthorizationHelper {
    
    /**
     * Check if user has Admin role
     */
    public static function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
    }
    
    /**
     * Check if user has Manager role
     */
    public static function isManager() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'Manager';
    }
    
    /**
     * Check if user has Admin or Manager role
     */
    public static function isAdminOrManager() {
        return self::isAdmin() || self::isManager();
    }
    
    /**
     * Check if user has specified role
     */
    public static function hasRole($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }
    
    /**
     * Get current user ID
     */
    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Require Admin role
     */
    public static function requireAdmin() {
        if (!self::isAdmin()) {
            http_response_code(403);
            Logger::warning("Unauthorized access attempt", ['user_id' => self::getUserId(), 'role' => $_SESSION['role'] ?? 'unknown']);
            die('Access Denied: Admin role required');
        }
    }
    
    /**
     * Deny with proper error logging
     */
    public static function deny($message = 'Access Denied') {
        http_response_code(403);
        Logger::warning("Access denied", [
            'user_id' => self::getUserId(),
            'role' => $_SESSION['role'] ?? 'unknown',
            'message' => $message
        ]);
        die($message);
    }
}
?>
