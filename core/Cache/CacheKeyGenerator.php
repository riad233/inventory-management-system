<?php

/**
 * CacheKeyGenerator - Generates consistent cache keys
 *
 * Prevents key collisions and provides namespacing.
 */
class CacheKeyGenerator
{
    private static string $prefix = 'ims_';

    /**
     * Generate a cache key.
     *
     * @param string $namespace Namespace (e.g., 'asset', 'employee')
     * @param string $action Action (e.g., 'list', 'get', 'count')
     * @param array $params Parameters (e.g., ['id' => 1, 'status' => 'active'])
     * @return string Cache key
     */
    public static function generate(string $namespace, string $action, array $params = []): string
    {
        $key = self::$prefix . $namespace . ':' . $action;

        if (!empty($params)) {
            ksort($params);
            $paramStr = md5(json_encode($params));
            $key .= ':' . substr($paramStr, 0, 8);
        }

        return $key;
    }

    /**
     * Generate API response cache key.
     *
     * @param string $endpoint API endpoint
     * @param array $params Query parameters
     * @return string
     */
    public static function generateApi(string $endpoint, array $params = []): string
    {
        return self::generate('api', $endpoint, $params);
    }

    /**
     * Generate query cache key.
     *
     * @param string $table Table name
     * @param array $where Where conditions
     * @return string
     */
    public static function generateQuery(string $table, array $where = []): string
    {
        return self::generate('query', $table, $where);
    }

    /**
     * Set cache key prefix (e.g., environment-specific).
     *
     * @param string $prefix
     * @return void
     */
    public static function setPrefix(string $prefix): void
    {
        self::$prefix = $prefix;
    }

    /**
     * Generate pattern for wildcard cache clearing.
     *
     * @param string $namespace Namespace
     * @param string|null $action Optional action
     * @return string Pattern
     */
    public static function pattern(string $namespace, ?string $action = null): string
    {
        if ($action) {
            return self::$prefix . $namespace . ':' . $action . ':*';
        }
        return self::$prefix . $namespace . ':*';
    }
}
