<?php
/**
 * Error Logger for IMS
 * Logs all errors, warnings, and important events
 */

class Logger {
    const LOG_ERROR = 'ERROR';
    const LOG_WARNING = 'WARNING';
    const LOG_INFO = 'INFO';
    const LOG_DEBUG = 'DEBUG';
    
    private static $logFile = null;
    
    /**
     * Initialize logger with log file path
     */
    public static function init($logPath = null) {
        if ($logPath === null) {
            $logPath = ROOT_PATH . '/storage/logs/app.log';
        }
        self::$logFile = $logPath;
        
        // Create logs directory if it doesn't exist
        $dir = dirname($logPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    /**
     * Write log entry
     */
    private static function write($level, $message, $context = []) {
        if (self::$logFile === null) {
            self::init();
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' | ' . json_encode($context) : '';
        $logMessage = "[$timestamp] $level: $message$contextStr" . PHP_EOL;
        
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Log error
     */
    public static function error($message, $context = []) {
        self::write(self::LOG_ERROR, $message, $context);
    }
    
    /**
     * Log warning
     */
    public static function warning($message, $context = []) {
        self::write(self::LOG_WARNING, $message, $context);
    }
    
    /**
     * Log info
     */
    public static function info($message, $context = []) {
        self::write(self::LOG_INFO, $message, $context);
    }
    
    /**
     * Log debug (only in development)
     */
    public static function debug($message, $context = []) {
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            self::write(self::LOG_DEBUG, $message, $context);
        }
    }
    
    /**
     * Log database error
     */
    public static function dbError($query, $error, $context = []) {
        self::error("Database Error: $error", array_merge(['query' => $query], $context));
    }
}

// Initialize logger on include
Logger::init();
?>
