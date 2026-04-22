<?php
/**
 * Authorization Helper Class
 * Provides role-based access control and permission checking
 */

class AuthorizationHelper {
    
    /**
     * Check if user has Admin role
     */
    private static function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
    }
    
    /**
     * Get current user ID
     */
    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Require Admin role - Enforce authorization on delete and sensitive operations
     */
    public static function requireAdmin() {
        if (!self::isAdmin()) {
            http_response_code(403);
            Logger::warning("Unauthorized access attempt", ['user_id' => self::getUserId(), 'role' => $_SESSION['role'] ?? 'unknown']);
            die('Access Denied: Admin role required');
        }
    }
}
?>
