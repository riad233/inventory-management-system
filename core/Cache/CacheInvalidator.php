<?php

/**
 * CacheInvalidator - Handle cache invalidation strategies
 *
 * Clears cache when data is modified to maintain consistency.
 */
class CacheInvalidator
{
    /**
     * Invalidate cache after entity creation.
     *
     * @param string $entity Entity name (asset, employee, etc.)
     * @return void
     */
    public static function onCreated(string $entity): void
    {
        $cache = CacheManager::getInstance();

        // Clear list caches for this entity
        $cache->clear(CacheKeyGenerator::pattern($entity, 'list'));
        $cache->clear(CacheKeyGenerator::pattern($entity, 'search'));
        $cache->clear(CacheKeyGenerator::pattern($entity, 'count'));
        $cache->clear(CacheKeyGenerator::pattern($entity, 'getRecent'));

        Logger::info('Cache invalidated', ['entity' => $entity, 'action' => 'created']);
    }

    /**
     * Invalidate cache after entity update.
     *
     * @param string $entity Entity name
     * @param int $id Entity ID
     * @return void
     */
    public static function onUpdated(string $entity, int $id): void
    {
        $cache = CacheManager::getInstance();

        // Clear specific entity cache
        $cache->clear(CacheKeyGenerator::pattern($entity, 'get') . ':*');
        $cache->delete(CacheKeyGenerator::generate($entity, 'get', ['id' => $id]));

        // Clear list caches
        $cache->clear(CacheKeyGenerator::pattern($entity, 'list'));
        $cache->clear(CacheKeyGenerator::pattern($entity, 'search'));
        $cache->clear(CacheKeyGenerator::pattern($entity, 'count'));

        Logger::info('Cache invalidated', ['entity' => $entity, 'id' => $id, 'action' => 'updated']);
    }

    /**
     * Invalidate cache after entity deletion.
     *
     * @param string $entity Entity name
     * @param int $id Entity ID
     * @return void
     */
    public static function onDeleted(string $entity, int $id): void
    {
        $cache = CacheManager::getInstance();

        // Clear specific entity cache
        $cache->delete(CacheKeyGenerator::generate($entity, 'get', ['id' => $id]));

        // Clear list caches
        $cache->clear(CacheKeyGenerator::pattern($entity, 'list'));
        $cache->clear(CacheKeyGenerator::pattern($entity, 'search'));
        $cache->clear(CacheKeyGenerator::pattern($entity, 'count'));
        $cache->clear(CacheKeyGenerator::pattern($entity, 'getRecent'));

        Logger::info('Cache invalidated', ['entity' => $entity, 'id' => $id, 'action' => 'deleted']);
    }

    /**
     * Invalidate cache for related entities.
     *
     * @param array $entities Array of entity names
     * @return void
     */
    public static function invalidateRelated(array $entities): void
    {
        $cache = CacheManager::getInstance();

        foreach ($entities as $entity) {
            // Clear all caches for this entity
            $cache->clear(CacheKeyGenerator::pattern($entity));
        }

        Logger::info('Related cache invalidated', ['entities' => implode(',', $entities)]);
    }

    /**
     * Clear all API response caches.
     *
     * @return void
     */
    public static function clearApi(): void
    {
        $cache = CacheManager::getInstance();
        $cache->clear(CacheKeyGenerator::pattern('api'));

        Logger::info('API cache cleared');
    }

    /**
     * Clear all caches.
     *
     * @return void
     */
    public static function clearAll(): void
    {
        $cache = CacheManager::getInstance();
        $cache->flush();

        Logger::info('All caches cleared');
    }
}
