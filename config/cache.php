<?php
/**
 * Cache Helper - Simple caching layer
 * Uses file-based caching as fallback (APCu if available)
 */

class Cache {
    private static $cacheDir = ROOT_PATH . '/storage/cache';
    private static $useAPCu = false;
    
    public static function init() {
        // Check if APCu is available
        self::$useAPCu = extension_loaded('apcu') && ini_get('apc.enabled');
        
        // Create cache directory if not exists
        if (!is_dir(self::$cacheDir)) {
            @mkdir(self::$cacheDir, 0755, true);
        }
    }
    
    /**
     * Get value from cache
     */
    public static function get($key, $default = null) {
        self::init();
        
        if (self::$useAPCu) {
            $value = apcu_fetch('ims_' . $key);
            return $value !== false ? $value : $default;
        }
        
        $file = self::$cacheDir . '/' . md5($key) . '.cache';
        if (!file_exists($file)) {
            return $default;
        }
        
        $data = file_get_contents($file);
        $cached = json_decode($data, true);
        
        if ($cached['expires'] < time()) {
            @unlink($file);
            return $default;
        }
        
        return $cached['value'] ?? $default;
    }
    
    /**
     * Store value in cache
     */
    public static function put($key, $value, $minutes = 5) {
        self::init();
        
        if (self::$useAPCu) {
            apcu_store('ims_' . $key, $value, $minutes * 60);
            return;
        }
        
        $file = self::$cacheDir . '/' . md5($key) . '.cache';
        $data = json_encode([
            'value' => $value,
            'expires' => time() + ($minutes * 60)
        ]);
        
        file_put_contents($file, $data);
    }
    
    /**
     * Check if key exists in cache
     */
    public static function has($key) {
        return self::get($key) !== null;
    }
    
    /**
     * Delete from cache
     */
    public static function forget($key) {
        self::init();
        
        if (self::$useAPCu) {
            apcu_delete('ims_' . $key);
            return;
        }
        
        $file = self::$cacheDir . '/' . md5($key) . '.cache';
        @unlink($file);
    }
    
    /**
     * Clear all cache
     */
    public static function flush() {
        self::init();
        
        if (self::$useAPCu) {
            apcu_clear_cache();
            return;
        }
        
        $files = glob(self::$cacheDir . '/*.cache');
        foreach ($files as $file) {
            @unlink($file);
        }
    }
    
    /**
     * Cache query results with TTL
     */
    public static function remember($key, $minutes, $callback) {
        $cached = self::get($key);
        if ($cached !== null) {
            return $cached;
        }
        
        $result = $callback();
        self::put($key, $result, $minutes);
        return $result;
    }
}
?>
