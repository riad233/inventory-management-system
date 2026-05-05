<?php

/**
 * CacheManager - Main cache interface
 *
 * Handles Redis with automatic fallback to file cache.
 * Single entry point for all caching operations.
 */
class CacheManager
{
    private static ?CacheManager $instance = null;
    private CacheBase $cache;
    private string $driver = 'file'; // 'redis' or 'file'

    /**
     * Private constructor - use getInstance().
     */
    private function __construct()
    {
        $this->initializeCache();
    }

    /**
     * Initialize cache (Redis with file fallback).
     */
    private function initializeCache(): void
    {
        // Try Redis first
        if (extension_loaded('redis')) {
            try {
                $redisCache = new RedisCache('localhost', 6379, 0);

                if ($redisCache->isConnected()) {
                    $this->cache = $redisCache;
                    $this->driver = 'redis';
                    Logger::info('Cache initialized', ['driver' => 'redis']);
                    return;
                }
            } catch (Exception $e) {
                Logger::warning('Redis initialization failed', ['error' => $e->getMessage()]);
            }
        }

        // Fall back to file cache
        $this->cache = new FileCache();
        $this->driver = 'file';
        Logger::info('Cache initialized', ['driver' => 'file']);
    }

    /**
     * Get singleton instance.
     *
     * @return CacheManager
     */
    public static function getInstance(): CacheManager
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Get underlying cache driver.
     *
     * @return CacheBase
     */
    public function getDriver(): CacheBase
    {
        return $this->cache;
    }

    /**
     * Get current driver name.
     *
     * @return string
     */
    public function getDriverName(): string
    {
        return $this->driver;
    }

    /**
     * Get value from cache.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->cache->get($key, $default);
    }

    /**
     * Set value in cache.
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $ttl
     * @return void
     */
    public function set(string $key, $value, ?int $ttl = null): void
    {
        $this->cache->set($key, $value, $ttl);
    }

    /**
     * Check if key exists.
     *
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return $this->cache->exists($key);
    }

    /**
     * Delete key from cache.
     *
     * @param string $key
     * @return void
     */
    public function delete(string $key): void
    {
        $this->cache->delete($key);
    }

    /**
     * Clear cache by pattern.
     *
     * @param string $pattern
     * @return void
     */
    public function clear(string $pattern = '*'): void
    {
        $this->cache->clear($pattern);
    }

    /**
     * Flush entire cache.
     *
     * @return void
     */
    public function flush(): void
    {
        $this->cache->flush();
    }

    /**
     * Get or set value.
     *
     * @param string $key
     * @param callable $callback
     * @param int|null $ttl
     * @return mixed
     */
    public function remember(string $key, callable $callback, ?int $ttl = null)
    {
        return $this->cache->remember($key, $callback, $ttl);
    }

    /**
     * Delete multiple keys.
     *
     * @param array $keys
     * @return void
     */
    public function deleteMany(array $keys): void
    {
        $this->cache->deleteMany($keys);
    }

    /**
     * Set multiple keys.
     *
     * @param array $items
     * @param int|null $ttl
     * @return void
     */
    public function setMany(array $items, ?int $ttl = null): void
    {
        $this->cache->setMany($items, $ttl);
    }

    /**
     * Get cache statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        return $this->cache->getStats();
    }

    /**
     * Reset statistics.
     *
     * @return void
     */
    public function resetStats(): void
    {
        $this->cache->resetStats();
    }

    /**
     * Set default TTL.
     *
     * @param int $ttl
     * @return void
     */
    public function setDefaultTtl(int $ttl): void
    {
        $this->cache->setDefaultTtl($ttl);
    }
}
