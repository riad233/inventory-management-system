<?php
/**
 * ALERT SYSTEM - CRITICAL DATABASE CHECK
 * Checks what's actually in your database
 */

session_start();

// Get database connection
require_once dirname(dirname(__FILE__)) . '/config/database.php';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Database State Check</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f5f5f5; padding: 20px; }
        .container { max-width: 1000px; background: white; border-radius: 8px; padding: 20px; }
        table { margin: 20px 0; }
        .alert-section { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>

<div class="container">
    <h1>🔍 Database State Check</h1>

    <?php
    if (!isset($conn)) {
        die('<div class="alert alert-danger">Database connection failed</div>');
    }

    echo '<div class="alert alert-info">Checking database state...</div>';

    // Check tables
    echo '<h3>Tables in Database:</h3>';
    $result = $conn->query("SHOW TABLES");
    echo '<ul>';
    while ($row = $result->fetch_array()) {
        echo '<li>' . $row[0] . '</li>';
    }
    echo '</ul>';

    // Check Products Table
    echo '<div class="alert-section">';
    echo '<h3>Products Table (Used by Alert System):</h3>';
    
    $result = $conn->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA='ims_db' AND TABLE_NAME='products'");
    
    if ($result && $result->num_rows > 0) {
        echo '<p style="color: green;"><strong>✅ Products table EXISTS</strong></p>';
        
        $result = $conn->query("SELECT * FROM products");
        if ($result && $result->num_rows > 0) {
            echo '<p><strong>Products in table: ' . $result->num_rows . '</strong></p>';
            
            echo '<table class="table table-bordered">';
            echo '<thead><tr><th>ID</th><th>Name</th><th>Quantity</th><th>Alert Status</th></tr></thead><tbody>';
            
            while ($row = $result->fetch_assoc()) {
                $qty = $row['quantity'];
                if ($qty < 3) {
                    $status = '<span style="color: red;">🔴 OUT OF STOCK</span>';
                } elseif ($qty >= 3 && $qty <= 10) {
                    $status = '<span style="color: orange;">🟡 LOW STOCK</span>';
                } else {
                    $status = '<span style="color: green;">🟢 OK</span>';
                }
                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                echo '<td>' . $qty . '</td>';
                echo '<td>' . $status . '</td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
            
            // Count alerts
            $result = $conn->query("SELECT COUNT(*) as count FROM products WHERE quantity < 3 OR (quantity >= 3 AND quantity <= 10)");
            $row = $result->fetch_assoc();
            $alert_count = $row['count'];
            
            echo '<p><strong>Products needing alerts: ' . $alert_count . '</strong></p>';
            if ($alert_count == 0) {
                echo '<div class="alert alert-warning">⚠️ No products in alert range</div>';
            }
        } else {
            echo '<p style="color: red;"><strong>❌ Products table is EMPTY</strong></p>';
        }
    } else {
        echo '<p style="color: red;"><strong>❌ Products table DOES NOT EXIST</strong></p>';
        echo '<p>You need to import the database. Go to: <code>/public/setup_database.php</code></p>';
    }
    
    echo '</div>';

    // Check Asset Table
    echo '<div class="alert-section">';
    echo '<h3>Assets Table (Your current data):</h3>';
    
    $result = $conn->query("SELECT * FROM asset LIMIT 10");
    
    if ($result && $result->num_rows > 0) {
        echo '<p><strong>Assets in table: ' . $result->num_rows . '</strong></p>';
        
        echo '<table class="table table-bordered">';
        echo '<thead><tr>';
        while ($field = $result->fetch_field()) {
            echo '<th>' . $field->name . '</th>';
        }
        echo '</tr></thead><tbody>';
        
        $result = $conn->query("SELECT * FROM asset LIMIT 10");
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            foreach ($row as $value) {
                echo '<td>' . htmlspecialchars($value) . '</td>';
            }
            echo '</tr>';
        }
        
        echo '</tbody></table>';
    } else {
        echo '<p>No assets found</p>';
    }
    
    echo '</div>';

    // Alert API Test
    echo '<div class="alert-section">';
    echo '<h3>Alert API Test:</h3>';
    echo '<button class="btn btn-primary" onclick="testAPI()">Test API Endpoint</button>';
    echo '<pre id="api-result" style="display:none; background: #f5f5f5; padding: 10px; margin-top: 10px; max-height: 300px; overflow-y: auto;"></pre>';
    echo '</div>';
    ?>

</div>

<script>
function testAPI() {
    const resultDiv = document.getElementById('api-result');
    resultDiv.innerHTML = 'Testing...';
    resultDiv.style.display = 'block';
    
    fetch('?url=alert/getAlerts')
        .then(response => {
            resultDiv.innerHTML += '\n✅ Status: ' + response.status + '\n';
            return response.text();
        })
        .then(text => {
            resultDiv.innerHTML += '\nResponse:\n' + text;
            try {
                const data = JSON.parse(text);
                resultDiv.innerHTML += '\n\n✅ Valid JSON!';
            } catch (e) {
                resultDiv.innerHTML += '\n\n❌ NOT Valid JSON: ' + e.message;
            }
        })
        .catch(error => {
            resultDiv.innerHTML = '❌ Error: ' + error.message;
        });
}
</script>

</body>
</html>
