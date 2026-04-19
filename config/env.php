<?php
/**
 * Environment Configuration Loader
 * Simple .env file parser for IMS application
 */

class EnvLoader {
    private static $config = [];
    private static $loaded = false;

    /**
     * Load environment variables from .env file
     */
    public static function load($filePath = null) {
        if (self::$loaded) {
            return;
        }

        if ($filePath === null) {
            $filePath = dirname(__FILE__) . '/.env';
        }

        // If .env file doesn't exist, use .env.example as fallback
        if (!file_exists($filePath)) {
            $filePath = dirname(__FILE__) . '/.env.example';
        }

        if (!file_exists($filePath)) {
            error_log("Warning: No .env or .env.example file found at $filePath");
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Skip comments
            if (strpos($line, '#') === 0) {
                continue;
            }

            // Parse KEY=VALUE format (handles both .env and .php define format)
            if (preg_match('/^([A-Z_]+)\s*=\s*(.*)$/', $line, $matches)) {
                $key = $matches[1];
                $value = trim($matches[2]);

                // Remove quotes if present
                if ((strpos($value, '"') === 0 && strpos($value, '"') === strlen($value) - 1) ||
                    (strpos($value, "'") === 0 && strpos($value, "'") === strlen($value) - 1)) {
                    $value = substr($value, 1, -1);
                }

                // Handle boolean values
                if (strtolower($value) === 'true') {
                    $value = true;
                } elseif (strtolower($value) === 'false') {
                    $value = false;
                } elseif (is_numeric($value)) {
                    $value = (int)$value;
                }

                self::$config[$key] = $value;
            }
        }

        self::$loaded = true;
    }

    /**
     * Get environment variable
     */
    public static function get($key, $default = null) {
        if (!self::$loaded) {
            self::load();
        }

        if (isset(self::$config[$key])) {
            return self::$config[$key];
        }

        // Fall back to system environment variables
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        return $default;
    }

    /**
     * Get all configuration
     */
    public static function all() {
        if (!self::$loaded) {
            self::load();
        }
        return self::$config;
    }
}

// Auto-load on require
EnvLoader::load();
?>
