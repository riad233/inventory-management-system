<?php

/**
 * FileCache - File-based cache adapter
 *
 * Fallback cache when Redis is unavailable.
 * Stores cache data as PHP files.
 */
class FileCache extends CacheBase
{
    private string $cacheDir;

    /**
     * Constructor - set cache directory.
     *
     * @param string $cacheDir Directory to store cache files
     */
    public function __construct(string $cacheDir = null)
    {
        $this->cacheDir = $cacheDir ?? (ROOT_PATH . '/storage/cache');

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * Get file path for cache key.
     *
     * @param string $key
     * @return string
     */
    private function getFilePath(string $key): string
    {
        return $this->cacheDir . '/' . md5($key) . '.cache';
    }

    public function get(string $key, $default = null)
    {
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            $this->stats['misses']++;
            return $default;
        }

        try {
            $data = unserialize(file_get_contents($file));

            // Check expiration
            if (isset($data['expiry']) && time() > $data['expiry']) {
                unlink($file);
                $this->stats['misses']++;
                return $default;
            }

            $this->stats['hits']++;
            return $data['value'] ?? $default;
        } catch (Exception $e) {
            Logger::error('File cache get error', ['key' => $key, 'error' => $e->getMessage()]);
            $this->stats['misses']++;
            return $default;
        }
    }

    public function set(string $key, $value, ?int $ttl = null): void
    {
        try {
            $data = [
                'value' => $value,
                'expiry' => $ttl ? time() + $ttl : null,
                'created' => time(),
            ];

            file_put_contents($this->getFilePath($key), serialize($data), LOCK_EX);
            $this->stats['writes']++;
        } catch (Exception $e) {
            Logger::error('File cache set error', ['key' => $key, 'error' => $e->getMessage()]);
        }
    }

    public function exists(string $key): bool
    {
        $file = $this->getFilePath($key);

        if (!file_exists($file)) {
            return false;
        }

        try {
            $data = unserialize(file_get_contents($file));

            // Check expiration
            if (isset($data['expiry']) && time() > $data['expiry']) {
                unlink($file);
                return false;
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function delete(string $key): void
    {
        try {
            $file = $this->getFilePath($key);
            if (file_exists($file)) {
                unlink($file);
            }
        } catch (Exception $e) {
            Logger::error('File cache delete error', ['key' => $key, 'error' => $e->getMessage()]);
        }
    }

    public function clear(string $pattern = '*'): void
    {
        try {
            $files = glob($this->cacheDir . '/*.cache');
            $deleted = 0;

            foreach ($files as $file) {
                if ($pattern === '*' || preg_match('/' . str_replace('*', '.*', $pattern) . '/', basename($file))) {
                    unlink($file);
                    $deleted++;
                }
            }

            Logger::info('File cache cleared', ['pattern' => $pattern, 'count' => $deleted]);
        } catch (Exception $e) {
            Logger::error('File cache clear error', ['error' => $e->getMessage()]);
        }
    }

    public function flush(): void
    {
        try {
            $files = glob($this->cacheDir . '/*.cache');

            foreach ($files as $file) {
                unlink($file);
            }

            Logger::info('File cache flushed completely');
        } catch (Exception $e) {
            Logger::error('File cache flush error', ['error' => $e->getMessage()]);
        }
    }
}
