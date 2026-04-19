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
     * Check if user is Employee
     */
    public static function isEmployee() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'Employee';
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
     * Require Admin or Manager role
     */
    public static function requireAdminOrManager() {
        if (!self::isAdminOrManager()) {
            http_response_code(403);
            Logger::warning("Unauthorized access attempt", ['user_id' => self::getUserId(), 'role' => $_SESSION['role'] ?? 'unknown']);
            die('Access Denied: Admin or Manager role required');
        }
    }
    
    /**
     * Check if user is department manager for given department
     * @param int $department_id Department ID
     * @return bool
     */
    public static function isDepartmentManager($department_id) {
        // Admins can manage any department
        if (self::isAdmin()) {
            return true;
        }
        
        // Managers can only manage their own department (if department_id is set in session)
        if (self::isManager() && isset($_SESSION['department_id'])) {
            return $_SESSION['department_id'] == $department_id;
        }
        
        return false;
    }
    
    /**
     * Check if user owns resource (for employee resource ownership)
     * @param int $resource_owner_id Owner ID from database
     * @return bool
     */
    public static function ownsResource($resource_owner_id) {
        $user_id = self::getUserId();
        if (self::isAdmin()) {
            return true; // Admins can manage all resources
        }
        return $user_id == $resource_owner_id;
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
