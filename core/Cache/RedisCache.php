<?php

/**
 * RedisCache - Redis cache adapter
 *
 * High-performance caching via Redis.
 * Supports all standard cache operations with Redis efficiency.
 */
class RedisCache extends CacheBase
{
    private \Redis $redis;
    private bool $connected = false;

    /**
     * Constructor - connect to Redis.
     *
     * @param string $host Redis host (default: localhost)
     * @param int $port Redis port (default: 6379)
     * @param int $db Redis database number (default: 0)
     */
    public function __construct(string $host = 'localhost', int $port = 6379, int $db = 0)
    {
        try {
            $this->redis = new \Redis();
            $this->connected = $this->redis->connect($host, $port, 1);

            if ($this->connected) {
                $this->redis->select($db);
                Logger::info('Redis cache connected', ['host' => $host, 'port' => $port]);
            }
        } catch (Exception $e) {
            Logger::warning('Redis connection failed', ['error' => $e->getMessage()]);
            $this->connected = false;
        }
    }

    /**
     * Check if Redis is connected.
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->connected;
    }

    public function get(string $key, $default = null)
    {
        if (!$this->connected) {
            return $default;
        }

        try {
            $value = $this->redis->get($key);

            if ($value === false) {
                $this->stats['misses']++;
                return $default;
            }

            $this->stats['hits']++;
            return unserialize($value);
        } catch (Exception $e) {
            Logger::error('Cache get error', ['key' => $key, 'error' => $e->getMessage()]);
            return $default;
        }
    }

    public function set(string $key, $value, ?int $ttl = null): void
    {
        if (!$this->connected) {
            return;
        }

        try {
            $serialized = serialize($value);
            $ttl = $ttl ?? $this->defaultTtl;

            if ($ttl) {
                $this->redis->setex($key, $ttl, $serialized);
            } else {
                $this->redis->set($key, $serialized);
            }

            $this->stats['writes']++;
        } catch (Exception $e) {
            Logger::error('Cache set error', ['key' => $key, 'error' => $e->getMessage()]);
        }
    }

    public function exists(string $key): bool
    {
        if (!$this->connected) {
            return false;
        }

        try {
            return $this->redis->exists($key) > 0;
        } catch (Exception $e) {
            Logger::error('Cache exists error', ['key' => $key, 'error' => $e->getMessage()]);
            return false;
        }
    }

    public function delete(string $key): void
    {
        if (!$this->connected) {
            return;
        }

        try {
            $this->redis->del($key);
        } catch (Exception $e) {
            Logger::error('Cache delete error', ['key' => $key, 'error' => $e->getMessage()]);
        }
    }

    public function clear(string $pattern = '*'): void
    {
        if (!$this->connected) {
            return;
        }

        try {
            $keys = $this->redis->keys($pattern);

            if (!empty($keys)) {
                $this->redis->del(...$keys);
                Logger::info('Cache cleared', ['pattern' => $pattern, 'count' => count($keys)]);
            }
        } catch (Exception $e) {
            Logger::error('Cache clear error', ['pattern' => $pattern, 'error' => $e->getMessage()]);
        }
    }

    public function flush(): void
    {
        if (!$this->connected) {
            return;
        }

        try {
            $this->redis->flushDB();
            Logger::info('Cache flushed completely');
        } catch (Exception $e) {
            Logger::error('Cache flush error', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get Redis info.
     *
     * @return array
     */
    public function info(): array
    {
        if (!$this->connected) {
            return [];
        }

        try {
            return $this->redis->info();
        } catch (Exception $e) {
            return [];
        }
    }
}
