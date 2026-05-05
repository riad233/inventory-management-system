<?php

namespace Database\Migrations;

use PDO;
use Exception;

/**
 * Database Migrator
 * 
 * Manages running and rolling back migrations
 * Tracks migration history in database
 */
class Migrator
{
    protected PDO $connection;
    protected string $migrationsPath;
    protected string $table = 'migrations';

    /**
     * Constructor
     * 
     * @param PDO $connection Database connection
     * @param string $migrationsPath Path to migrations directory
     */
    public function __construct(PDO $connection, string $migrationsPath = __DIR__)
    {
        $this->connection = $connection;
        $this->migrationsPath = $migrationsPath;
        $this->createMigrationsTable();
    }

    /**
     * Create migrations history table if not exists
     * 
     * @throws Exception
     */
    protected function createMigrationsTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `$this->table` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `migration` VARCHAR(255) NOT NULL UNIQUE,
                `batch` INT NOT NULL,
                `executed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX `batch_idx` (`batch`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";

        try {
            $this->connection->exec($sql);
        } catch (Exception $e) {
            throw new Exception("Failed to create migrations table: " . $e->getMessage());
        }
    }

    /**
     * Run pending migrations
     * 
     * @return array List of executed migrations
     * @throws Exception
     */
    public function migrate(): array
    {
        $pending = $this->getPendingMigrations();
        
        if (empty($pending)) {
            echo "No migrations to run." . PHP_EOL;
            return [];
        }

        $batch = $this->getNextBatch();
        $executed = [];

        $this->connection->beginTransaction();

        try {
            foreach ($pending as $migration) {
                $this->runMigration($migration, $batch);
                $executed[] = $migration;
                echo "✓ Migration executed: $migration" . PHP_EOL;
            }

            $this->connection->commit();
            return $executed;
        } catch (Exception $e) {
            $this->connection->rollBack();
            throw new Exception("Migration failed: " . $e->getMessage());
        }
    }

    /**
     * Rollback migrations
     * 
     * @param int|null $steps Number of batches to rollback (null = rollback all)
     * @return array List of rolled back migrations
     * @throws Exception
     */
    public function rollback(?int $steps = 1): array
    {
        $migrations = $this->getRollbackMigrations($steps);

        if (empty($migrations)) {
            echo "No migrations to rollback." . PHP_EOL;
            return [];
        }

        $rolled = [];

        $this->connection->beginTransaction();

        try {
            foreach ($migrations as $migration) {
                $this->rollbackMigration($migration);
                $rolled[] = $migration;
                echo "✓ Migration rolled back: $migration" . PHP_EOL;
            }

            $this->connection->commit();
            return $rolled;
        } catch (Exception $e) {
            $this->connection->rollBack();
            throw new Exception("Rollback failed: " . $e->getMessage());
        }
    }

    /**
     * Reset all migrations
     * 
     * @throws Exception
     */
    public function reset(): void
    {
        $all = $this->getExecutedMigrations();

        if (empty($all)) {
            echo "No migrations to reset." . PHP_EOL;
            return;
        }

        $this->connection->beginTransaction();

        try {
            foreach (array_reverse($all) as $migration) {
                $this->rollbackMigration($migration);
                echo "✓ Migration reset: $migration" . PHP_EOL;
            }

            $this->connection->commit();
        } catch (Exception $e) {
            $this->connection->rollBack();
            throw new Exception("Reset failed: " . $e->getMessage());
        }
    }

    /**
     * Refresh migrations (reset and migrate)
     * 
     * @throws Exception
     */
    public function refresh(): void
    {
        echo "Resetting all migrations..." . PHP_EOL;
        $this->reset();

        echo "Running migrations..." . PHP_EOL;
        $this->migrate();

        echo "All migrations refreshed successfully!" . PHP_EOL;
    }

    /**
     * Run a single migration
     * 
     * @param string $migration Migration class name
     * @param int $batch Batch number
     * @throws Exception
     */
    protected function runMigration(string $migration, int $batch): void
    {
        $class = "Database\\Migrations\\$migration";

        if (!class_exists($class)) {
            throw new Exception("Migration class not found: $class");
        }

        $instance = new $class($this->connection);
        $instance->up();

        $stmt = $this->connection->prepare(
            "INSERT INTO `$this->table` (migration, batch) VALUES (?, ?)"
        );
        $stmt->execute([$migration, $batch]);
    }

    /**
     * Rollback a single migration
     * 
     * @param string $migration Migration class name
     * @throws Exception
     */
    protected function rollbackMigration(string $migration): void
    {
        $class = "Database\\Migrations\\$migration";

        if (!class_exists($class)) {
            throw new Exception("Migration class not found: $class");
        }

        $instance = new $class($this->connection);
        $instance->down();

        $stmt = $this->connection->prepare(
            "DELETE FROM `$this->table` WHERE migration = ?"
        );
        $stmt->execute([$migration]);
    }

    /**
     * Get pending migrations
     * 
     * @return array
     */
    protected function getPendingMigrations(): array
    {
        $all = $this->getAvailableMigrations();
        $executed = $this->getExecutedMigrations();

        return array_diff($all, $executed);
    }

    /**
     * Get migrations for rollback
     * 
     * @param int|null $steps
     * @return array
     */
    protected function getRollbackMigrations(?int $steps = 1): array
    {
        $query = "SELECT migration FROM `$this->table` ORDER BY batch DESC, id DESC";

        if ($steps !== null) {
            $query .= " LIMIT " . ($steps * 100); // Approximate limit
        }

        $stmt = $this->connection->query($query);
        $migrations = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return array_map(fn($m) => basename($m), $migrations);
    }

    /**
     * Get executed migrations
     * 
     * @return array
     */
    protected function getExecutedMigrations(): array
    {
        $stmt = $this->connection->query(
            "SELECT migration FROM `$this->table` ORDER BY batch ASC, id ASC"
        );
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get available migrations
     * 
     * @return array
     */
    protected function getAvailableMigrations(): array
    {
        $files = glob($this->migrationsPath . '/*.php');
        $migrations = [];

        foreach ($files as $file) {
            $filename = basename($file, '.php');
            if ($filename !== 'Migration' && $filename !== 'Migrator') {
                $migrations[] = $filename;
            }
        }

        return $migrations;
    }

    /**
     * Get next batch number
     * 
     * @return int
     */
    protected function getNextBatch(): int
    {
        $stmt = $this->connection->query("SELECT MAX(batch) as batch FROM `$this->table`");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($result['batch'] ?? 0) + 1;
    }

    /**
     * Get migration status
     * 
     * @return array
     */
    public function getStatus(): array
    {
        $all = $this->getAvailableMigrations();
        $executed = $this->getExecutedMigrations();

        $status = [];

        foreach ($all as $migration) {
            $status[$migration] = in_array($migration, $executed) ? 'executed' : 'pending';
        }

        return $status;
    }
}
