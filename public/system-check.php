<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>IMS - System Check</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .status-ok { background-color: #d4edda; }
    .status-error { background-color: #f8d7da; }
    .status-warning { background-color: #fff3cd; }
</style>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4"><i class="fas fa-heartbeat"></i> System Health Check</h1>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">System Requirements</h5>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Component</th>
                        <th>Status</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sections = [];
                    
                    // PHP Version
                    $php_ok = version_compare(PHP_VERSION, '7.4.0', '>=');
                    $sections[] = [
                        'name' => 'PHP Version',
                        'status' => $php_ok ? 'ok' : 'error',
                        'details' => PHP_VERSION . ($php_ok ? ' ✓' : ' (Required: 7.4+)')
                    ];
                    
                    // Extensions
                    $extensions = ['mysqli', 'json', 'session', 'filter'];
                    $missing_ext = [];
                    foreach($extensions as $ext) {
                        if (!extension_loaded($ext)) {
                            $missing_ext[] = $ext;
                        }
                    }
                    $ext_ok = empty($missing_ext);
                    $sections[] = [
                        'name' => 'Required Extensions',
                        'status' => $ext_ok ? 'ok' : 'error',
                        'details' => $ext_ok ? 'All installed ✓' : 'Missing: ' . implode(', ', $missing_ext)
                    ];
                    
                    // Writable Directories
                    $dirs_to_check = ['storage', 'storage/logs'];
                    $writable = true;
                    $not_writable = [];
                    foreach($dirs_to_check as $dir) {
                        $path = __DIR__ . '/../' . $dir;
                        if (!is_writable($path)) {
                            $writable = false;
                            $not_writable[] = $dir;
                        }
                    }
                    $sections[] = [
                        'name' => 'Directory Permissions',
                        'status' => $writable ? 'ok' : 'warning',
                        'details' => $writable ? 'All writable ✓' : 'Not writable: ' . implode(', ', $not_writable)
                    ];
                    
                    // Database Connection
                    $db_ok = false;
                    $db_details = 'Not connected';
                    try {
                        require_once __DIR__ . '/../config/database.php';
                        if ($conn) {
                            $db_ok = true;
                            $db_version_result = mysqli_query($conn, "SELECT VERSION() as version");
                            $db_row = mysqli_fetch_assoc($db_version_result);
                            $db_details = 'Connected ✓ - MySQL ' . $db_row['version'];
                        }
                    } catch (Exception $e) {
                        $db_details = 'Error: ' . $e->getMessage();
                    }
                    $sections[] = [
                        'name' => 'Database Connection',
                        'status' => $db_ok ? 'ok' : 'error',
                        'details' => $db_details
                    ];
                    
                    // Database Tables
                    if ($db_ok) {
                        $required_tables = ['users', 'asset', 'assignment', 'employee', 'maintenance', 'vendor', 'equipment_request'];
                        $missing_tables = [];
                        foreach($required_tables as $table) {
                            $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
                            if (!mysqli_fetch_row($result)) {
                                $missing_tables[] = $table;
                            }
                        }
                        $tables_ok = empty($missing_tables);
                        $sections[] = [
                            'name' => 'Database Tables',
                            'status' => $tables_ok ? 'ok' : 'error',
                            'details' => $tables_ok ? 'All tables exist ✓' : 'Missing: ' . implode(', ', $missing_tables)
                        ];
                        
                        // Sample Admin User
                        $admin_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE Role='Admin'");
                        $admin_row = mysqli_fetch_assoc($admin_result);
                        $admin_ok = $admin_row['count'] > 0;
                        $sections[] = [
                            'name' => 'Admin User',
                            'status' => $admin_ok ? 'ok' : 'warning',
                            'details' => $admin_ok ? 'Admin user found ✓' : 'No admin user (create one to login)'
                        ];
                    }
                    
                    // File Permissions
                    $public_index = __DIR__ . '/index.php';
                    $public_readable = is_readable($public_index);
                    $sections[] = [
                        'name' => 'File Permissions',
                        'status' => $public_readable ? 'ok' : 'error',
                        'details' => $public_readable ? 'public/index.php readable ✓' : 'Cannot read public/index.php'
                    ];
                    
                    // Display sections
                    foreach($sections as $section):
                        $status_class = 'status-' . $section['status'];
                    ?>
                        <tr class="<?php echo $status_class; ?>">
                            <td><strong><?php echo $section['name']; ?></strong></td>
                            <td>
                                <?php
                                $badge_class = 'badge bg-';
                                if ($section['status'] === 'ok') echo '<span class="badge bg-success">OK</span>';
                                elseif ($section['status'] === 'error') echo '<span class="badge bg-danger">ERROR</span>';
                                else echo '<span class="badge bg-warning">WARNING</span>';
                                ?>
                            </td>
                            <td><?php echo $section['details']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Next Steps</h5>
        </div>
        <div class="card-body">
            <ol>
                <li>Ensure all system requirements show "OK" status</li>
                <li>Fix any "ERROR" items before proceeding</li>
                <li>If no admin user exists, create one using:
                    <code>INSERT INTO users (Username, Password, Email, Role) VALUES ('admin', 'password123', 'admin@ims.local', 'Admin');</code>
                </li>
                <li>Once all checks pass, proceed to <a href="/">IMS Login Page</a></li>
            </ol>
        </div>
    </div>
    
    <div class="alert alert-info mt-4">
        <strong>System Info:</strong><br>
        PHP Version: <?php echo PHP_VERSION; ?><br>
        OS: <?php echo php_uname(); ?><br>
        Loaded Extensions: <?php echo implode(', ', get_loaded_extensions()); ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
