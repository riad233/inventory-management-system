<?php
/**
 * ALERT SYSTEM - DATABASE SETUP & VERIFICATION
 * 
 * This script:
 * 1. Checks if database exists
 * 2. Checks if products table exists
 * 3. Imports the SQL file if needed
 * 4. Verifies sample data is present
 * 
 * Access: /public/setup_database.php
 */

// Don't require authentication for setup
session_status() !== PHP_SESSION_ACTIVE && session_start();

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Get database connection
require_once dirname(dirname(__FILE__)) . '/config/database.php';

// Function to safely execute SQL
function executeSqlFile($filepath, $conn) {
    if (!file_exists($filepath)) {
        return ['success' => false, 'error' => 'SQL file not found: ' . $filepath];
    }
    
    $sql = file_get_contents($filepath);
    
    // Split on ; and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $executed = 0;
    $errors = [];
    
    foreach ($statements as $statement) {
        if (empty($statement)) continue;
        
        if (!$conn->query($statement)) {
            $errors[] = $conn->error;
        } else {
            $executed++;
        }
    }
    
    if (!empty($errors)) {
        return ['success' => false, 'executed' => $executed, 'errors' => $errors];
    }
    
    return ['success' => true, 'executed' => $executed];
}

// Check if setup action requested
$action = $_GET['action'] ?? '';
$setup_result = null;

if ($action === 'import' && isset($conn)) {
    $sql_file = dirname(dirname(__FILE__)) . '/database/ims_database.sql';
    $setup_result = executeSqlFile($sql_file, $conn);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alert System - Database Setup</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; }
        .setup-container { background: white; border-radius: 12px; padding: 40px; max-width: 600px; width: 100%; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
        .setup-header { text-align: center; margin-bottom: 30px; }
        .setup-header h1 { color: #667eea; font-size: 28px; margin-bottom: 10px; }
        .status-item { 
            background: #f8f9fa; 
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .status-item.success { border-left-color: #28a745; background: #f0fdf4; }
        .status-item.error { border-left-color: #dc3545; background: #fdf8f8; }
        .status-item.warning { border-left-color: #ffc107; background: #fffbf0; }
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
        }
        .status-badge.pass { background: #28a745; color: white; }
        .status-badge.fail { background: #dc3545; color: white; }
        .status-badge.warning { background: #ffc107; color: #333; }
        .status-badge.pending { background: #6c757d; color: white; }
        .action-buttons { margin-top: 30px; display: flex; gap: 10px; }
        .action-buttons button { flex: 1; }
        .result-box {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
            display: none;
        }
        .result-box.show { display: block; }
        .result-box.success { background: #f0fdf4; border-left: 4px solid #28a745; }
        .result-box.error { background: #fdf8f8; border-left: 4px solid #dc3545; }
        .spinner { display: none; text-align: center; margin: 20px 0; }
        .spinner.show { display: block; }
        .next-steps { margin-top: 30px; padding-top: 30px; border-top: 1px solid #eee; }
        .next-steps h4 { color: #667eea; margin-bottom: 15px; }
        .next-steps li { margin-bottom: 8px; }
    </style>
</head>
<body>

<div class="setup-container">
    <div class="setup-header">
        <h1>🔔 Alert System Setup</h1>
        <p>Database Configuration & Verification</p>
    </div>

    <!-- Status Checks -->
    <h4 style="margin-bottom: 20px;">System Status</h4>
    
    <!-- Database Connection -->
    <div class="status-item <?php echo isset($conn) && !$conn->connect_error ? 'success' : 'error'; ?>">
        <div>
            <strong>Database Connection</strong>
            <div style="font-size: 12px; color: #666; margin-top: 3px;">
                <?php
                if (isset($conn)) {
                    if ($conn->connect_error) {
                        echo 'Error: ' . htmlspecialchars($conn->connect_error);
                    } else {
                        echo 'Host: localhost | User: root';
                    }
                } else {
                    echo 'Not configured';
                }
                ?>
            </div>
        </div>
        <span class="status-badge <?php echo isset($conn) && !$conn->connect_error ? 'pass' : 'fail'; ?>">
            <?php echo isset($conn) && !$conn->connect_error ? 'Connected' : 'Failed'; ?>
        </span>
    </div>

    <!-- Database Exists -->
    <?php
    $db_exists = false;
    $table_exists = false;
    $product_count = 0;
    $alert_count = 0;
    
    if (isset($conn) && !$conn->connect_error) {
        $result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'ims_db'");
        $db_exists = $result && $result->num_rows > 0;
        
        if ($db_exists) {
            $conn->select_db('ims_db');
            
            $result = $conn->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='ims_db' AND TABLE_NAME='products'");
            $table_exists = $result && $result->num_rows > 0;
            
            if ($table_exists) {
                $result = $conn->query("SELECT COUNT(*) as count FROM products");
                if ($result) {
                    $row = $result->fetch_assoc();
                    $product_count = $row['count'];
                }
                
                $result = $conn->query("SELECT COUNT(*) as count FROM products WHERE quantity < 3 OR (quantity >= 3 AND quantity <= 10)");
                if ($result) {
                    $row = $result->fetch_assoc();
                    $alert_count = $row['count'];
                }
            }
        }
    }
    ?>

    <div class="status-item <?php echo $db_exists ? 'success' : 'warning'; ?>">
        <div>
            <strong>Database 'ims_db'</strong>
            <div style="font-size: 12px; color: #666; margin-top: 3px;">
                <?php echo $db_exists ? 'Database exists and accessible' : 'Database not found or not imported yet'; ?>
            </div>
        </div>
        <span class="status-badge <?php echo $db_exists ? 'pass' : 'warning'; ?>">
            <?php echo $db_exists ? 'Exists' : 'Missing'; ?>
        </span>
    </div>

    <!-- Products Table -->
    <div class="status-item <?php echo $table_exists ? 'success' : 'error'; ?>">
        <div>
            <strong>Products Table</strong>
            <div style="font-size: 12px; color: #666; margin-top: 3px;">
                <?php 
                if ($table_exists) {
                    echo 'Table exists with ' . $product_count . ' records';
                } else {
                    echo 'Table needs to be imported';
                }
                ?>
            </div>
        </div>
        <span class="status-badge <?php echo $table_exists ? 'pass' : 'fail'; ?>">
            <?php echo $table_exists ? 'Ready' : 'Missing'; ?>
        </span>
    </div>

    <!-- Sample Data -->
    <?php if ($table_exists): ?>
    <div class="status-item <?php echo $product_count > 0 ? 'success' : 'warning'; ?>">
        <div>
            <strong>Sample Products</strong>
            <div style="font-size: 12px; color: #666; margin-top: 3px;">
                <?php echo $product_count . ' products available'; ?>
            </div>
        </div>
        <span class="status-badge <?php echo $product_count > 0 ? 'pass' : 'warning'; ?>">
            <?php echo $product_count . ' records'; ?>
        </span>
    </div>

    <!-- Alert Products -->
    <div class="status-item <?php echo $alert_count > 0 ? 'success' : 'warning'; ?>">
        <div>
            <strong>Products Needing Alerts</strong>
            <div style="font-size: 12px; color: #666; margin-top: 3px;">
                Products with stock alerts (out of stock or low stock)
            </div>
        </div>
        <span class="status-badge <?php echo $alert_count > 0 ? 'pass' : 'warning'; ?>">
            <?php echo $alert_count . ' alerts'; ?>
        </span>
    </div>
    <?php endif; ?>

    <!-- Setup Result -->
    <?php if ($setup_result): ?>
    <div class="result-box show <?php echo $setup_result['success'] ? 'success' : 'error'; ?>">
        <?php if ($setup_result['success']): ?>
            <strong style="color: #28a745;">✓ Database Imported Successfully</strong>
            <p style="margin: 10px 0 0 0; font-size: 13px;">
                <?php echo $setup_result['executed']; ?> SQL statements executed successfully.
            </p>
        <?php else: ?>
            <strong style="color: #dc3545;">✗ Import Failed</strong>
            <p style="margin: 10px 0 0 0; font-size: 13px;">
                <?php echo $setup_result['error'] ?? 'Unknown error'; ?>
            </p>
            <?php if (isset($setup_result['errors'])): ?>
                <div style="background: #fff; padding: 10px; border-radius: 4px; margin-top: 10px; max-height: 150px; overflow-y: auto; font-size: 11px; font-family: monospace;">
                    <?php foreach ($setup_result['errors'] as $error): ?>
                        <div style="color: #c33; margin-bottom: 5px;">• <?php echo htmlspecialchars($error); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <?php if (!$table_exists): ?>
        <form method="get" style="flex: 1; margin: 0;">
            <input type="hidden" name="action" value="import">
            <button type="submit" class="btn btn-primary btn-block w-100" onclick="showSpinner()">
                📥 Import Database
            </button>
        </form>
        <?php endif; ?>
        <a href="../routes/web.php" class="btn btn-secondary flex-1 w-auto" style="flex: 1;">
            ↩️ Back to Home
        </a>
    </div>

    <div class="spinner" id="spinner">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p>Importing database...</p>
    </div>

    <!-- Next Steps -->
    <?php if ($table_exists && $alert_count > 0): ?>
    <div class="next-steps">
        <h4>✅ System Ready</h4>
        <p>The alert system is now configured and ready to use.</p>
        <ol>
            <li><strong>Login</strong> to the application with your admin account</li>
            <li><strong>Look for the bell icon</strong> (🔔) in the top-right corner</li>
            <li><strong>Badge should show:</strong> <?php echo $alert_count; ?> alerts</li>
            <li><strong>Click the bell</strong> to see the alert list</li>
            <li><strong>Test features:</strong> Click items, wait 30 seconds for auto-refresh</li>
        </ol>
        <hr>
        <p style="font-size: 13px; color: #666;">
            <strong>For verification:</strong> Navigate to <code>/public/alert_diagnostic.php</code> (after login) to run comprehensive tests.
        </p>
    </div>
    <?php elseif ($table_exists && $alert_count === 0): ?>
    <div class="next-steps">
        <h4>⚠️ No Alerts Found</h4>
        <p>Products table exists but no products have alerts.</p>
        <p>You can:</p>
        <ol>
            <li>Wait for products to reach alert thresholds</li>
            <li>Or manually update product quantities in the database</li>
        </ol>
    </div>
    <?php elseif ($db_exists && !$table_exists): ?>
    <div class="next-steps">
        <h4>⚠️ Database Needs Setup</h4>
        <p>Click the "Import Database" button above to set up the products table and sample data.</p>
    </div>
    <?php endif; ?>

</div>

<script>
function showSpinner() {
    document.getElementById('spinner').classList.add('show');
}
</script>

</body>
</html>
