<?php
/**
 * Validation Helper for IMS
 * Provides reusable validation methods for form inputs
 */

class Validator {
    private static $errors = [];
    
    /**
     * Start fresh validation session
     */
    public static function reset() {
        self::$errors = [];
    }
    
    /**
     * Get all validation errors
     */
    public static function getErrors() {
        return self::$errors;
    }
    
    /**
     * Check if validation passed (no errors)
     */
    public static function passes() {
        return empty(self::$errors);
    }
    
    /**
     * Add a validation error
     */
    private static function addError($field, $message) {
        self::$errors[$field] = $message;
    }
    
    /**
     * Validate required field
     */
    public static function required($field, $value, $label = null) {
        $value = trim($value ?? '');
        if (empty($value)) {
            $label = $label ?? ucfirst($field);
            self::addError($field, "$label is required");
            return false;
        }
        return true;
    }
    
    /**
     * Validate string length
     */
    public static function minLength($field, $value, $min, $label = null) {
        if (strlen($value) < $min) {
            $label = $label ?? ucfirst($field);
            self::addError($field, "$label must be at least $min characters");
            return false;
        }
        return true;
    }
    
    public static function maxLength($field, $value, $max, $label = null) {
        if (strlen($value) > $max) {
            $label = $label ?? ucfirst($field);
            self::addError($field, "$label must not exceed $max characters");
            return false;
        }
        return true;
    }
    
    /**
     * Validate email format
     */
    public static function email($field, $value, $label = null) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $label = $label ?? ucfirst($field);
            self::addError($field, "$label must be a valid email address");
            return false;
        }
        return true;
    }
    
    /**
     * Validate date format (YYYY-MM-DD)
     */
    public static function date($field, $value, $label = null) {
        $d = \DateTime::createFromFormat('Y-m-d', $value);
        if (!$d || $d->format('Y-m-d') !== $value) {
            $label = $label ?? ucfirst($field);
            self::addError($field, "$label must be a valid date (YYYY-MM-DD)");
            return false;
        }
        return true;
    }
    
    /**
     * Validate numeric value
     */
    public static function numeric($field, $value, $label = null) {
        if (!is_numeric($value)) {
            $label = $label ?? ucfirst($field);
            self::addError($field, "$label must be numeric");
            return false;
        }
        return true;
    }
    
    /**
     * Validate integer value
     */
    public static function integer($field, $value, $label = null) {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            $label = $label ?? ucfirst($field);
            self::addError($field, "$label must be an integer");
            return false;
        }
        return true;
    }
    
    /**
     * Validate value in a list
     */
    public static function in($field, $value, $options, $label = null) {
        if (!in_array($value, $options, true)) {
            $label = $label ?? ucfirst($field);
            self::addError($field, "$label contains an invalid value");
            return false;
        }
        return true;
    }
    
    /**
     * Validate positive number
     */
    public static function positive($field, $value, $label = null) {
        if ($value <= 0) {
            $label = $label ?? ucfirst($field);
            self::addError($field, "$label must be greater than 0");
            return false;
        }
        return true;
    }
    
    /**
     * Validate phone number (basic)
     */
    public static function phone($field, $value, $label = null) {
        if (!preg_match('/^[\d\s\-\+\(\)]{10,}$/', $value)) {
            $label = $label ?? ucfirst($field);
            self::addError($field, "$label must be a valid phone number");
            return false;
        }
        return true;
    }
    
    /**
     * Sanitize string input
     */
    public static function sanitizeString($value) {
        return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
    }
    
    /**
     * Sanitize email
     */
    public static function sanitizeEmail($value) {
        return filter_var($value, FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Sanitize integer
     */
    public static function sanitizeInt($value) {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

}
?>
