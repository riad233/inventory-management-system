<?php
/**
 * Alert System - COMPREHENSIVE DIAGNOSTIC TEST PAGE
 * Visit this page: /public/alert_diagnostic.php
 * 
 * This page tests EVERY component of the alert system
 * and shows detailed debugging information
 */

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// No output buffering
ob_implicit_flush(true);

// Set to development mode
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alert System - Comprehensive Diagnostic</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f5f5f5; padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .diagnostic-container { max-width: 1200px; margin: 0 auto; }
        .test-section { 
            background: white; 
            border-radius: 8px; 
            margin-bottom: 20px; 
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .section-header { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            font-size: 18px;
            font-weight: bold;
        }
        .section-content { padding: 20px; }
        .test-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #eee;
        }
        .test-item:last-child { border-bottom: none; }
        .test-label { font-weight: 500; flex: 1; }
        .test-status { 
            padding: 4px 12px; 
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            min-width: 80px;
            text-align: center;
        }
        .status-pass { background: #d4edda; color: #155724; }
        .status-fail { background: #f8d7da; color: #721c24; }
        .status-warning { background: #fff3cd; color: #856404; }
        .status-info { background: #d1ecf1; color: #0c5460; }
        .detail-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 12px;
            margin: 10px 0;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            overflow-x: auto;
        }
        .detail-box.error { border-left-color: #dc3545; background: #fff5f5; }
        .detail-box.success { border-left-color: #28a745; background: #f5fff5; }
        .detail-box.warning { border-left-color: #ffc107; background: #fffaf5; }
        .code-block {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 12px;
            border-radius: 4px;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            line-height: 1.4;
            margin: 10px 0;
        }
        .summary-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .summary-stat {
            display: inline-block;
            margin-right: 30px;
            margin-bottom: 10px;
        }
        .summary-stat-label { font-size: 12px; opacity: 0.9; }
        .summary-stat-value { font-size: 28px; font-weight: bold; }
        .api-test-result {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 12px;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            word-break: break-all;
        }
        table { width: 100%; margin: 10px 0; font-size: 13px; }
        table th { background: #f8f9fa; font-weight: bold; padding: 8px; }
        table td { padding: 8px; border-bottom: 1px solid #eee; }
        table tr:last-child td { border-bottom: none; }
    </style>
</head>
<body>

<div class="diagnostic-container">
    <div class="summary-box">
        <h1>🔍 Alert System Comprehensive Diagnostic</h1>
        <p style="margin-top: 10px; opacity: 0.9;">Testing all components of the notification system</p>
    </div>

    <!-- SESSION TEST -->
    <div class="test-section">
        <div class="section-header">📋 SESSION & AUTHENTICATION</div>
        <div class="section-content">
            <?php
            $session_tests = [];
            
            // Test 1: Session status
            $session_tests[] = [
                'label' => 'Session Started',
                'value' => session_status() === PHP_SESSION_ACTIVE ? 'Yes' : 'No',
                'status' => session_status() === PHP_SESSION_ACTIVE ? 'pass' : 'fail'
            ];
            
            // Test 2: Username in session
            $username = $_SESSION['username'] ?? 'NOT SET';
            $session_tests[] = [
                'label' => 'Username in Session',
                'value' => $username,
                'status' => !empty($_SESSION['username']) ? 'pass' : 'fail'
            ];
            
            // Test 3: Role in session
            $role = $_SESSION['role'] ?? 'NOT SET';
            $session_tests[] = [
                'label' => 'User Role',
                'value' => $role,
                'status' => !empty($_SESSION['role']) ? 'pass' : 'fail'
            ];
            
            // Test 4: Role is Admin or Manager
            $is_admin_or_manager = !empty($_SESSION['role']) && in_array($_SESSION['role'], ['Admin', 'Manager']);
            $session_tests[] = [
                'label' => 'Role is Admin/Manager',
                'value' => $is_admin_or_manager ? 'Yes' : 'No (Required for alert API)',
                'status' => $is_admin_or_manager ? 'pass' : 'warning'
            ];
            
            foreach ($session_tests as $test) {
                echo '<div class="test-item">';
                echo '<span class="test-label">' . htmlspecialchars($test['label']) . '</span>';
                echo '<span class="test-status status-' . $test['status'] . '">' . htmlspecialchars($test['value']) . '</span>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <!-- DATABASE TEST -->
    <div class="test-section">
        <div class="section-header">🗄️ DATABASE CONNECTION</div>
        <div class="section-content">
            <?php
            // Include database config
            $config_path = dirname(dirname(__FILE__)) . '/config/database.php';
            
            if (file_exists($config_path)) {
                require_once $config_path;
                
                $db_tests = [];
                
                // Test 1: Connection exists
                $conn_exists = isset($conn) && $conn instanceof mysqli;
                $db_tests[] = [
                    'label' => 'Database Connection Object',
                    'value' => $conn_exists ? 'Connected' : 'Not Found',
                    'status' => $conn_exists ? 'pass' : 'fail'
                ];
                
                if ($conn_exists) {
                    // Test 2: Connection error check
                    if ($conn->connect_error) {
                        $db_tests[] = [
                            'label' => 'Connection Error',
                            'value' => $conn->connect_error,
                            'status' => 'fail'
                        ];
                    } else {
                        $db_tests[] = [
                            'label' => 'Connection Status',
                            'value' => 'OK',
                            'status' => 'pass'
                        ];
                    }
                    
                    // Test 3: Database selected
                    $db_tests[] = [
                        'label' => 'Database Selected',
                        'value' => $conn->select_db('ims_db') ? 'ims_db' : 'Failed',
                        'status' => $conn->get_server_info() ? 'pass' : 'fail'
                    ];
                    
                    // Test 4: Check products table exists
                    $result = $conn->query("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='ims_db' AND TABLE_NAME='products'");
                    $table_exists = $result && $result->fetch_assoc()['count'] > 0;
                    
                    $db_tests[] = [
                        'label' => 'Products Table Exists',
                        'value' => $table_exists ? 'Yes' : 'No',
                        'status' => $table_exists ? 'pass' : 'fail'
                    ];
                    
                    // Test 5: Count products
                    if ($table_exists) {
                        $result = $conn->query("SELECT COUNT(*) as count FROM products");
                        $product_count = $result ? $result->fetch_assoc()['count'] : 0;
                        
                        $db_tests[] = [
                            'label' => 'Products in Table',
                            'value' => $product_count . ' records',
                            'status' => $product_count > 0 ? 'pass' : 'warning'
                        ];
                        
                        // Test 6: Count alerts
                        $alert_result = $conn->query("SELECT COUNT(*) as count FROM products WHERE quantity < 3 OR (quantity >= 3 AND quantity <= 10)");
                        $alert_count = $alert_result ? $alert_result->fetch_assoc()['count'] : 0;
                        
                        $db_tests[] = [
                            'label' => 'Products with Alerts',
                            'value' => $alert_count . ' products',
                            'status' => $alert_count > 0 ? 'pass' : 'warning'
                        ];
                    }
                }
                
                foreach ($db_tests as $test) {
                    echo '<div class="test-item">';
                    echo '<span class="test-label">' . htmlspecialchars($test['label']) . '</span>';
                    echo '<span class="test-status status-' . $test['status'] . '">' . htmlspecialchars($test['value']) . '</span>';
                    echo '</div>';
                }
                
            } else {
                echo '<div class="alert alert-danger">Database config file not found at: ' . htmlspecialchars($config_path) . '</div>';
            }
            ?>
        </div>
    </div>

    <!-- MODEL TEST -->
    <div class="test-section">
        <div class="section-header">📦 ALERT MODEL</div>
        <div class="section-content">
            <?php
            $model_path = dirname(dirname(__FILE__)) . '/app/models/Alert.php';
            $model_base_path = dirname(dirname(__FILE__)) . '/core/Model.php';
            
            $model_tests = [];
            
            // Test 1: Model file exists
            $model_exists = file_exists($model_path);
            $model_tests[] = [
                'label' => 'Alert Model File Exists',
                'value' => $model_exists ? 'Yes' : 'No',
                'status' => $model_exists ? 'pass' : 'fail'
            ];
            
            // Test 2: Base Model exists
            $base_exists = file_exists($model_base_path);
            $model_tests[] = [
                'label' => 'Base Model Class Exists',
                'value' => $base_exists ? 'Yes' : 'No',
                'status' => $base_exists ? 'pass' : 'fail'
            ];
            
            if ($model_exists && $base_exists) {
                require_once $model_base_path;
                require_once $model_path;
                
                if (class_exists('Alert')) {
                    $model_tests[] = [
                        'label' => 'Alert Class Can Be Instantiated',
                        'value' => 'Yes',
                        'status' => 'pass'
                    ];
                    
                    // Try to instantiate and test
                    try {
                        $alert = new Alert();
                        
                        // Test getAll()
                        $alerts = $alert->getAll();
                        $model_tests[] = [
                            'label' => 'getAll() Method',
                            'value' => 'OK (' . count($alerts) . ' alerts)',
                            'status' => is_array($alerts) ? 'pass' : 'fail'
                        ];
                        
                        // Test getCount()
                        $count = $alert->getCount();
                        $model_tests[] = [
                            'label' => 'getCount() Method',
                            'value' => $count . ' alerts',
                            'status' => is_numeric($count) ? 'pass' : 'fail'
                        ];
                        
                    } catch (Exception $e) {
                        $model_tests[] = [
                            'label' => 'Model Instantiation Error',
                            'value' => $e->getMessage(),
                            'status' => 'fail'
                        ];
                    }
                } else {
                    $model_tests[] = [
                        'label' => 'Alert Class Definition',
                        'value' => 'Not found',
                        'status' => 'fail'
                    ];
                }
            }
            
            foreach ($model_tests as $test) {
                echo '<div class="test-item">';
                echo '<span class="test-label">' . htmlspecialchars($test['label']) . '</span>';
                echo '<span class="test-status status-' . $test['status'] . '">' . htmlspecialchars($test['value']) . '</span>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <!-- CONTROLLER TEST -->
    <div class="test-section">
        <div class="section-header">🎮 ALERT CONTROLLER</div>
        <div class="section-content">
            <?php
            $controller_path = dirname(dirname(__FILE__)) . '/app/controllers/AlertController.php';
            $base_controller_path = dirname(dirname(__FILE__)) . '/core/Controller.php';
            
            $controller_tests = [];
            
            // Test 1: Controller file exists
            $controller_exists = file_exists($controller_path);
            $controller_tests[] = [
                'label' => 'AlertController File Exists',
                'value' => $controller_exists ? 'Yes' : 'No',
                'status' => $controller_exists ? 'pass' : 'fail'
            ];
            
            // Test 2: Base Controller exists
            $base_controller_exists = file_exists($base_controller_path);
            $controller_tests[] = [
                'label' => 'Base Controller Class Exists',
                'value' => $base_controller_exists ? 'Yes' : 'No',
                'status' => $base_controller_exists ? 'pass' : 'fail'
            ];
            
            if ($controller_exists && $base_controller_exists) {
                require_once $base_controller_path;
                require_once $controller_path;
                
                if (class_exists('AlertController')) {
                    $controller_tests[] = [
                        'label' => 'AlertController Class Exists',
                        'value' => 'Yes',
                        'status' => 'pass'
                    ];
                    
                    // Check methods
                    $methods = ['getAlerts', 'getCount'];
                    foreach ($methods as $method) {
                        $has_method = method_exists('AlertController', $method);
                        $controller_tests[] = [
                            'label' => 'Method: ' . $method . '()',
                            'value' => $has_method ? 'Exists' : 'Missing',
                            'status' => $has_method ? 'pass' : 'fail'
                        ];
                    }
                } else {
                    $controller_tests[] = [
                        'label' => 'AlertController Class Definition',
                        'value' => 'Not found',
                        'status' => 'fail'
                    ];
                }
            }
            
            foreach ($controller_tests as $test) {
                echo '<div class="test-item">';
                echo '<span class="test-label">' . htmlspecialchars($test['label']) . '</span>';
                echo '<span class="test-status status-' . $test['status'] . '">' . htmlspecialchars($test['value']) . '</span>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <!-- ROUTING TEST -->
    <div class="test-section">
        <div class="section-header">🛣️ ROUTING & ACCESS CONTROL</div>
        <div class="section-content">
            <?php
            $router_path = dirname(dirname(__FILE__)) . '/core/Router.php';
            
            $routing_tests = [];
            
            // Test 1: Router exists
            $router_exists = file_exists($router_path);
            $routing_tests[] = [
                'label' => 'Router Class Exists',
                'value' => $router_exists ? 'Yes' : 'No',
                'status' => $router_exists ? 'pass' : 'fail'
            ];
            
            // Test 2: Check if alert is in public controllers
            if ($router_exists) {
                $router_content = file_get_contents($router_path);
                $has_alert_check = strpos($router_content, "'alert'") !== false;
                
                $routing_tests[] = [
                    'label' => 'Alert in Public Controllers',
                    'value' => $has_alert_check ? 'Yes' : 'No',
                    'status' => $has_alert_check ? 'pass' : 'warning'
                ];
                
                // Test 3: Role check for protected routes
                $has_role_check = strpos($router_content, 'Admin') !== false || strpos($router_content, 'Manager') !== false;
                $routing_tests[] = [
                    'label' => 'Role-Based Access Control',
                    'value' => $has_role_check ? 'Enabled' : 'Not configured',
                    'status' => $has_role_check ? 'pass' : 'warning'
                ];
            }
            
            // Test 4: Current user can access alert API
            $can_access = !empty($_SESSION['username']) && in_array($_SESSION['role'] ?? '', ['Admin', 'Manager']);
            $routing_tests[] = [
                'label' => 'Current User Can Access Alert API',
                'value' => $can_access ? 'Yes (' . $role . ')' : 'No (Need Admin/Manager role)',
                'status' => $can_access ? 'pass' : 'fail'
            ];
            
            foreach ($routing_tests as $test) {
                echo '<div class="test-item">';
                echo '<span class="test-label">' . htmlspecialchars($test['label']) . '</span>';
                echo '<span class="test-status status-' . $test['status'] . '">' . htmlspecialchars($test['value']) . '</span>';
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <!-- API ENDPOINT TEST -->
    <div class="test-section">
        <div class="section-header">🌐 API ENDPOINTS</div>
        <div class="section-content">
            <p style="color: #666; margin-bottom: 15px;">
                These are the API endpoints the JavaScript frontend uses. Click the buttons below to test them directly.
            </p>
            
            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 15px;">
                <button class="btn btn-primary btn-sm" onclick="testAPIEndpoint('alert/getAlerts')">Test getAlerts()</button>
                <button class="btn btn-primary btn-sm" onclick="testAPIEndpoint('alert/getCount')">Test getCount()</button>
            </div>
            
            <div id="api-test-result" class="api-test-result" style="display: none;"></div>
            
            <script>
                function testAPIEndpoint(endpoint) {
                    const resultDiv = document.getElementById('api-test-result');
                    resultDiv.innerHTML = '<strong>Testing:</strong> ' + endpoint + '<br><strong>Loading...</strong>';
                    resultDiv.style.display = 'block';
                    
                    fetch('?url=' + endpoint)
                        .then(response => {
                            let text = '<strong>Status:</strong> ' + response.status + ' ' + response.statusText + '<br>';
                            text += '<strong>Headers:</strong><br>';
                            text += '  Content-Type: ' + response.headers.get('content-type') + '<br>';
                            
                            return response.text().then(body => {
                                text += '<strong>Response Body:</strong><br>';
                                text += body;
                                
                                resultDiv.innerHTML = text;
                                resultDiv.style.display = 'block';
                            });
                        })
                        .catch(error => {
                            resultDiv.innerHTML = '<strong style="color:red;">Error:</strong> ' + error.message;
                            resultDiv.style.display = 'block';
                        });
                }
            </script>
        </div>
    </div>

    <!-- SAMPLE DATA TEST -->
    <div class="test-section">
        <div class="section-header">📊 SAMPLE DATA FROM DATABASE</div>
        <div class="section-content">
            <?php
            if (isset($conn) && $conn instanceof mysqli) {
                $result = $conn->query("SELECT * FROM products LIMIT 10");
                
                if ($result && $result->num_rows > 0) {
                    echo '<p style="color: #666; margin-bottom: 10px;">Found ' . $result->num_rows . ' products:</p>';
                    echo '<table border="1">';
                    echo '<tr><th>ID</th><th>Name</th><th>Qty</th><th>Status</th><th>Alert Type</th></tr>';
                    
                    while ($row = $result->fetch_assoc()) {
                        $qty = $row['quantity'];
                        if ($qty < 3) {
                            $status = '<span style="color: red;">OUT OF STOCK</span>';
                            $alert_type = 'Out of Stock';
                        } elseif ($qty >= 3 && $qty <= 10) {
                            $status = '<span style="color: orange;">LOW STOCK</span>';
                            $alert_type = 'Low Stock';
                        } else {
                            $status = '<span style="color: green;">OK</span>';
                            $alert_type = 'Normal';
                        }
                        
                        echo '<tr>';
                        echo '<td>' . $row['id'] . '</td>';
                        echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                        echo '<td>' . $qty . '</td>';
                        echo '<td>' . $status . '</td>';
                        echo '<td>' . $alert_type . '</td>';
                        echo '</tr>';
                    }
                    
                    echo '</table>';
                } else {
                    echo '<div class="alert alert-warning">⚠️ No products found in database</div>';
                }
            }
            ?>
        </div>
    </div>

    <!-- JAVASCRIPT TEST -->
    <div class="test-section">
        <div class="section-header">✅ JAVASCRIPT VERIFICATION</div>
        <div class="section-content">
            <p style="color: #666; margin-bottom: 15px;">
                Check if alert system JavaScript functions are available:
            </p>
            
            <div id="js-test-results"></div>
            
            <script>
                const jsTests = [
                    { name: 'window.loadAlerts', fn: 'typeof window.loadAlerts' },
                    { name: 'window.updateAlertBadge', fn: 'typeof window.updateAlertBadge' },
                    { name: 'window.populateAlertDropdown', fn: 'typeof window.populateAlertDropdown' },
                    { name: 'window.escapeHtml', fn: 'typeof window.escapeHtml' },
                    { name: '#alertBellBtn element', fn: 'document.getElementById("alertBellBtn") ? "found" : "not found"' },
                    { name: '#alertDropdown element', fn: 'document.getElementById("alertDropdown") ? "found" : "not found"' },
                    { name: '#alertBadge element', fn: 'document.getElementById("alertBadge") ? "found" : "not found"' }
                ];
                
                const results = document.getElementById('js-test-results');
                let html = '';
                
                jsTests.forEach(test => {
                    let result, status;
                    try {
                        result = eval(test.fn);
                        if (result === 'function') {
                            status = 'pass';
                            result = 'Function available';
                        } else if (result === 'found') {
                            status = 'pass';
                        } else if (result === 'not found') {
                            status = 'fail';
                        } else if (result === 'undefined') {
                            status = 'fail';
                            result = 'Undefined';
                        } else {
                            status = 'info';
                        }
                    } catch (e) {
                        status = 'fail';
                        result = 'Error: ' + e.message;
                    }
                    
                    html += '<div class="test-item">';
                    html += '<span class="test-label">' + test.name + '</span>';
                    html += '<span class="test-status status-' + status + '">' + result + '</span>';
                    html += '</div>';
                });
                
                results.innerHTML = html;
            </script>
        </div>
    </div>

    <!-- RECOMMENDATIONS -->
    <div class="test-section">
        <div class="section-header">💡 RECOMMENDATIONS</div>
        <div class="section-content">
            <div style="line-height: 1.8;">
                <p><strong>1. If badge shows 0 and no errors above:</strong></p>
                <ul>
                    <li>Database `ims_database.sql` might not have been imported</li>
                    <li>Import it via phpMyAdmin or command line</li>
                    <li>Then refresh this page to verify</li>
                </ul>
                
                <p><strong>2. If you see "Cannot instantiate Alert model":</strong></p>
                <ul>
                    <li>Database connection in Model.php might be failing</li>
                    <li>Check config/database.php for correct credentials</li>
                </ul>
                
                <p><strong>3. If API returns 403 Forbidden:</strong></p>
                <ul>
                    <li>Your user role might not be 'Admin' or 'Manager'</li>
                    <li>Contact system administrator to upgrade role</li>
                    <li>Or modify Router.php to allow all authenticated users</li>
                </ul>
                
                <p><strong>4. If JavaScript functions are undefined:</strong></p>
                <ul>
                    <li>layout.js might not be loaded</li>
                    <li>Check browser DevTools Network tab</li>
                    <li>Check browser console for JavaScript errors</li>
                </ul>
                
                <p><strong>5. Next Steps:</strong></p>
                <ul>
                    <li>Check browser DevTools Console (F12 → Console)</li>
                    <li>Check browser DevTools Network (F12 → Network)</li>
                    <li>Run diagnostic tests above</li>
                    <li>Make API test calls using buttons above</li>
                </ul>
            </div>
        </div>
    </div>

</div>

</body>
</html>
