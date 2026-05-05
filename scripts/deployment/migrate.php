<?php

/**
 * Database Migration Script
 * 
 * Usage:
 *   php scripts/deployment/migrate.php              # Run pending migrations
 *   php scripts/deployment/migrate.php rollback     # Rollback last batch
 *   php scripts/deployment/migrate.php refresh      # Refresh all migrations
 *   php scripts/deployment/migrate.php reset        # Reset all migrations
 *   php scripts/deployment/migrate.php status       # Show migration status
 */

require_once __DIR__ . '/../../config/config.php';

use Database\Migrations\Migrator;

try {
    $command = $argv[1] ?? 'migrate';

    // Create database connection
    $config = require __DIR__ . '/../../config/database.php';
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $config['host'],
        $config['port'],
        $config['name']
    );

    $pdo = new PDO($dsn, $config['user'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create migrator
    $migrator = new Migrator($pdo, __DIR__ . '/../../database/migrations');

    // Execute command
    switch ($command) {
        case 'migrate':
            echo "Running migrations..." . PHP_EOL;
            $executed = $migrator->migrate();
            echo PHP_EOL . count($executed) . " migration(s) executed successfully." . PHP_EOL;
            break;

        case 'rollback':
            $steps = intval($argv[2] ?? 1);
            echo "Rolling back $steps batch(es)..." . PHP_EOL;
            $rolled = $migrator->rollback($steps);
            echo PHP_EOL . count($rolled) . " migration(s) rolled back successfully." . PHP_EOL;
            break;

        case 'refresh':
            echo "Refreshing all migrations..." . PHP_EOL;
            $migrator->refresh();
            echo "All migrations refreshed successfully." . PHP_EOL;
            break;

        case 'reset':
            echo "Resetting all migrations..." . PHP_EOL;
            $migrator->reset();
            echo "All migrations reset successfully." . PHP_EOL;
            break;

        case 'status':
            echo "Migration Status:" . PHP_EOL;
            echo "=================" . PHP_EOL;
            $status = $migrator->getStatus();
            
            foreach ($status as $migration => $state) {
                $indicator = $state === 'executed' ? '✓' : '○';
                echo "$indicator $migration ... $state" . PHP_EOL;
            }
            break;

        default:
            echo "Unknown command: $command" . PHP_EOL;
            echo PHP_EOL . "Available commands:" . PHP_EOL;
            echo "  migrate      Run pending migrations (default)" . PHP_EOL;
            echo "  rollback [n] Rollback last n batch(es)" . PHP_EOL;
            echo "  refresh      Refresh all migrations" . PHP_EOL;
            echo "  reset        Reset all migrations" . PHP_EOL;
            echo "  status       Show migration status" . PHP_EOL;
            exit(1);
    }

    exit(0);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
