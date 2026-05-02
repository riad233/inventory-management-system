<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

/**
 * Cache Helper
 *
 * Uses APCu when available; falls back to file-based caching in storage/cache/.
 * All file cache entries are stored with a TTL payload so they expire automatically.
 */
class Cache {

    private static string $cacheDir  = '';
    private static bool   $useApcu   = false;
    private static bool   $initiated = false;

    private static function init(): void {
        if (self::$initiated) return;
        self::$initiated = true;

        self::$useApcu  = function_exists('apcu_fetch') && (bool)ini_get('apc.enabled');
        self::$cacheDir = ROOT_PATH . '/storage/cache';

        if (!self::$useApcu && !is_dir(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0700, true);
        }
    }

    /**
     * Retrieve a value from the cache.
     * Returns $default if the key does not exist or has expired.
     */
    public static function get(string $key, mixed $default = null): mixed {
        self::init();

        if (self::$useApcu) {
            $value = apcu_fetch(self::prefixed($key), $success);
            return $success ? $value : $default;
        }

        $file = self::filePath($key);
        if (!file_exists($file)) return $default;

        $payload = @unserialize(file_get_contents($file));
        if ($payload === false) return $default;

        if ($payload['expires'] !== 0 && $payload['expires'] < time()) {
            @unlink($file);
            return $default;
        }

        return $payload['value'];
    }

    /**
     * Store a value in the cache for $ttlMinutes minutes.
     * Pass 0 for $ttlMinutes to cache indefinitely (file cache only).
     */
    public static function put(string $key, mixed $value, int $ttlMinutes = 60): void {
        self::init();

        $ttlSeconds = $ttlMinutes * 60;

        if (self::$useApcu) {
            apcu_store(self::prefixed($key), $value, $ttlSeconds);
            return;
        }

        $payload = [
            'value'   => $value,
            'expires' => $ttlSeconds === 0 ? 0 : time() + $ttlSeconds,
        ];
        file_put_contents(self::filePath($key), serialize($payload), LOCK_EX);
    }

    /**
     * Check whether a non-expired entry exists for $key.
     */
    public static function has(string $key): bool {
        return self::get($key) !== null;
    }

    /**
     * Remove a single cache entry.
     */
    public static function forget(string $key): void {
        self::init();

        if (self::$useApcu) {
            apcu_delete(self::prefixed($key));
            return;
        }

        $file = self::filePath($key);
        if (file_exists($file)) @unlink($file);
    }

    /**
     * Remove all cache entries (file: only *.cache files under storage/cache/).
     */
    public static function flush(): void {
        self::init();

        if (self::$useApcu) {
            apcu_clear_cache();
            return;
        }

        foreach (glob(self::$cacheDir . '/*.cache') ?: [] as $file) {
            @unlink($file);
        }
    }

    /**
     * Get a cached value or compute it via $callback, then cache and return it.
     *
     * @param callable $callback  Returns the value to store
     */
    public static function remember(string $key, int $ttlMinutes, callable $callback): mixed {
        $value = self::get($key);
        if ($value !== null) return $value;

        $value = $callback();
        self::put($key, $value, $ttlMinutes);
        return $value;
    }

    // ── Internal helpers ──────────────────────────────────────────────

    private static function prefixed(string $key): string {
        return 'ims_' . $key;
    }

    private static function filePath(string $key): string {
        return self::$cacheDir . '/' . md5($key) . '.cache';
    }
}
