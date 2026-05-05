<?php

namespace Database\Migrations;

use PDO;
use Exception;

/**
 * Base Migration Class
 * 
 * Provides base functionality for running database migrations
 * Handles both up (apply) and down (rollback) operations
 */
abstract class Migration
{
    protected PDO $connection;
    protected string $migrationName;

    /**
     * Constructor
     * 
     * @param PDO $connection Database connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
        $this->migrationName = static::class;
    }

    /**
     * Run migration (up)
     * 
     * @throws Exception
     */
    abstract public function up(): void;

    /**
     * Rollback migration (down)
     * 
     * @throws Exception
     */
    abstract public function down(): void;

    /**
     * Get migration name/description
     * 
     * @return string
     */
    abstract public function getDescription(): string;

    /**
     * Execute raw SQL
     * 
     * @param string $sql SQL statement
     * @return bool
     * @throws Exception
     */
    protected function execute(string $sql): bool
    {
        try {
            return $this->connection->exec($sql) !== false;
        } catch (Exception $e) {
            throw new Exception("Migration failed: " . $e->getMessage());
        }
    }

    /**
     * Execute prepared statement
     * 
     * @param string $sql SQL statement
     * @param array $params Parameters
     * @return \PDOStatement
     * @throws Exception
     */
    protected function prepare(string $sql, array $params = []): \PDOStatement
    {
        try {
            $stmt = $this->connection->prepare($sql);
            if (!empty($params)) {
                $stmt->execute($params);
            }
            return $stmt;
        } catch (Exception $e) {
            throw new Exception("Prepare failed: " . $e->getMessage());
        }
    }

    /**
     * Check if table exists
     * 
     * @param string $table Table name
     * @return bool
     */
    protected function tableExists(string $table): bool
    {
        try {
            $result = $this->connection->query("
                SELECT 1 FROM information_schema.TABLES 
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table'
            ");
            return $result->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check if column exists
     * 
     * @param string $table Table name
     * @param string $column Column name
     * @return bool
     */
    protected function columnExists(string $table, string $column): bool
    {
        try {
            $result = $this->connection->query("
                SELECT 1 FROM information_schema.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table' AND COLUMN_NAME = '$column'
            ");
            return $result->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check if index exists
     * 
     * @param string $table Table name
     * @param string $index Index name
     * @return bool
     */
    protected function indexExists(string $table, string $index): bool
    {
        try {
            $result = $this->connection->query("
                SELECT 1 FROM information_schema.STATISTICS 
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$table' AND INDEX_NAME = '$index'
            ");
            return $result->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Log migration info
     * 
     * @param string $message Message to log
     */
    protected function info(string $message): void
    {
        echo "[" . date('Y-m-d H:i:s') . "] " . $message . PHP_EOL;
    }
}
