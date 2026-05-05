<?php

/**
 * ServiceFactory - Service Locator for all service instances
 *
 * Provides centralized access to all services with lazy loading.
 * Ensures single instance per service (singleton pattern per request).
 *
 * Usage:
 *   $assetService = ServiceFactory::assets();
 *   $employeeService = ServiceFactory::employees();
 *   etc...
 */
class ServiceFactory
{
    private static array $instances = [];

    /**
     * Prevent direct instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Get AssetService instance.
     */
    public static function assets(): AssetService
    {
        return self::getInstance(AssetService::class);
    }

    /**
     * Get EmployeeService instance.
     */
    public static function employees(): EmployeeService
    {
        return self::getInstance(EmployeeService::class);
    }

    /**
     * Get VendorService instance.
     */
    public static function vendors(): VendorService
    {
        return self::getInstance(VendorService::class);
    }

    /**
     * Get AssignmentService instance.
     */
    public static function assignments(): AssignmentService
    {
        return self::getInstance(AssignmentService::class);
    }

    /**
     * Get MaintenanceService instance.
     */
    public static function maintenance(): MaintenanceService
    {
        return self::getInstance(MaintenanceService::class);
    }

    /**
     * Get EquipmentRequestService instance.
     */
    public static function requests(): EquipmentRequestService
    {
        return self::getInstance(EquipmentRequestService::class);
    }

    /**
     * Get or create service instance (singleton per request).
     *
     * @param string $class Service class name
     * @return object Service instance
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
