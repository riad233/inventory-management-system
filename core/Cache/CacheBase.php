<?php

/**
 * CacheBase - Abstract base cache class
 *
 * Defines interface for all cache implementations (Redis, File, etc.)
 */
abstract class CacheBase
{
    protected array $stats = ['hits' => 0, 'misses' => 0, 'writes' => 0];
    protected int $defaultTtl = 3600; // 1 hour

    /**
     * Get value from cache.
     *
     * @param string $key
     * @param mixed $default Default if not found
     * @return mixed
     */
    abstract public function get(string $key, $default = null);

    /**
     * Set value in cache.
     *
     * @param string $key
     * @param mixed $value
     * @param int|null $ttl Time to live in seconds (null = forever)
     * @return void
     */
    abstract public function set(string $key, $value, ?int $ttl = null): void;

    /**
     * Check if key exists in cache.
     *
     * @param string $key
     * @return bool
     */
    abstract public function exists(string $key): bool;

    /**
     * Delete key from cache.
     *
     * @param string $key
     * @return void
     */
    abstract public function delete(string $key): void;

    /**
     * Clear cache by pattern.
     *
     * @param string $pattern Pattern with * wildcard (e.g., 'asset:*')
     * @return void
     */
    abstract public function clear(string $pattern = '*'): void;

    /**
     * Clear entire cache.
     *
     * @return void
     */
    abstract public function flush(): void;

    /**
     * Get or set value.
     *
     * @param string $key
     * @param callable $callback Function to generate value if not cached
     * @param int|null $ttl Time to live
     * @return mixed
     */
    public function remember(string $key, callable $callback, ?int $ttl = null)
    {
        if ($this->exists($key)) {
            $this->stats['hits']++;
            return $this->get($key);
        }

        $this->stats['misses']++;
        $value = $callback();
        $this->set($key, $value, $ttl ?? $this->defaultTtl);

        return $value;
    }

    /**
     * Delete multiple keys.
     *
     * @param array $keys
     * @return void
     */
    public function deleteMany(array $keys): void
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
    }

    /**
     * Set multiple keys.
     *
     * @param array $items Key => value pairs
     * @param int|null $ttl Time to live
     * @return void
     */
    public function setMany(array $items, ?int $ttl = null): void
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value, $ttl);
        }
    }

    /**
     * Get cache statistics.
     *
     * @return array
     */
    public function getStats(): array
    {
        return array_merge($this->stats, [
            'hitRate' => $this->stats['hits'] + $this->stats['misses'] > 0
                ? round(($this->stats['hits'] / ($this->stats['hits'] + $this->stats['misses'])) * 100, 2)
                : 0,
        ]);
    }

    /**
     * Reset statistics.
     *
     * @return void
     */
    public function resetStats(): void
    {
        $this->stats = ['hits' => 0, 'misses' => 0, 'writes' => 0];
    }

    /**
     * Set default TTL.
     *
     * @param int $ttl Seconds
     * @return void
     */
    public function setDefaultTtl(int $ttl): void
    {
        $this->defaultTtl = $ttl;
    }
}
