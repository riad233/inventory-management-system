<?php

/**
 * RepositoryFactory - Service Locator for all repository instances
 *
 * Provides centralized access to all repositories with lazy loading.
 * Ensures single instance per repository (singleton pattern per request).
 *
 * Usage:
 *   $assetRepo = RepositoryFactory::assets();
 *   $employeeRepo = RepositoryFactory::employees();
 *   etc...
 */
class RepositoryFactory
{
    private static array $instances = [];

    /**
     * Prevent direct instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Get AssetRepository instance.
     */
    public static function assets(): AssetRepository
    {
        return self::getInstance(AssetRepository::class);
    }

    /**
     * Get EmployeeRepository instance.
     */
    public static function employees(): EmployeeRepository
    {
        return self::getInstance(EmployeeRepository::class);
    }

    /**
     * Get VendorRepository instance.
     */
    public static function vendors(): VendorRepository
    {
        return self::getInstance(VendorRepository::class);
    }

    /**
     * Get AssignmentRepository instance.
     */
    public static function assignments(): AssignmentRepository
    {
        return self::getInstance(AssignmentRepository::class);
    }

    /**
     * Get MaintenanceRepository instance.
     */
    public static function maintenance(): MaintenanceRepository
    {
        return self::getInstance(MaintenanceRepository::class);
    }

    /**
     * Get DepartmentRepository instance.
     */
    public static function departments(): DepartmentRepository
    {
        return self::getInstance(DepartmentRepository::class);
    }

    /**
     * Get UserRepository instance.
     */
    public static function users(): UserRepository
    {
        return self::getInstance(UserRepository::class);
    }

    /**
     * Get EquipmentRequestRepository instance.
     */
    public static function requests(): EquipmentRequestRepository
    {
        return self::getInstance(EquipmentRequestRepository::class);
    }

    /**
     * Get PermissionRepository instance.
     */
    public static function permissions(): PermissionRepository
    {
        return self::getInstance(PermissionRepository::class);
    }

    /**
     * Get or create repository instance (singleton per request).
     *
     * @param string $class Repository class name
     * @return object Repository instance
     */
    private static function getInstance(string $class): object
    {
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class();
        }

        return self::$instances[$class];
    }

    /**
     * Reset all instances (useful for testing).
     */
    public static function reset(): void
    {
        self::$instances = [];
    }
}
