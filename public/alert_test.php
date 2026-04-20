<?php
/**
 * Alert System Test Page
 * Quick diagnostic to verify alert system is working
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
}

require_once ROOT_PATH . "/config/config.php";
require_once ROOT_PATH . "/config/database.php";
require_once ROOT_PATH . "/app/models/Alert.php";

// Check if user is logged in
if (empty($_SESSION['username'])) {
    die('Please login first');
}

echo "<h1>Alert System Diagnostic Test</h1>";
echo "<hr>";

// Test 1: Database connection
echo "<h2>1. Database Connection</h2>";
if ($conn) {
    echo "<span style='color:green;'>✓ Database connected</span><br>";
} else {
    echo "<span style='color:red;'>✗ Database connection failed</span><br>";
    die();
}

// Test 2: Check if products table exists
echo "<h2>2. Products Table Check</h2>";
$result = $conn->query("SELECT COUNT(*) as count FROM products");
if ($result) {
    $row = $result->fetch_assoc();
    echo "<span style='color:green;'>✓ Products table exists</span><br>";
    echo "Total products: " . $row['count'] . "<br>";
} else {
    echo "<span style='color:red;'>✗ Products table not found</span><br>";
    echo "Error: " . $conn->error . "<br>";
}

// Test 3: Alert Model - Get All
echo "<h2>3. Alert Model - getAll()</h2>";
try {
    $alertModel = new Alert();
    $alerts = $alertModel->getAll();
    echo "<span style='color:green;'>✓ getAll() executed</span><br>";
    echo "Alerts count: " . count($alerts) . "<br>";
    if (count($alerts) > 0) {
        echo "<pre>";
        print_r($alerts);
        echo "</pre>";
    } else {
        echo "No alerts found (all products have normal stock)<br>";
    }
} catch (Exception $e) {
    echo "<span style='color:red;'>✗ Error: " . $e->getMessage() . "</span><br>";
}

// Test 4: Alert Model - Get Count
echo "<h2>4. Alert Model - getCount()</h2>";
try {
    $alertModel = new Alert();
    $count = $alertModel->getCount();
    echo "<span style='color:green;'>✓ getCount() executed</span><br>";
    echo "Alert count: " . $count . "<br>";
} catch (Exception $e) {
    echo "<span style='color:red;'>✗ Error: " . $e->getMessage() . "</span><br>";
}

// Test 5: API Endpoint
echo "<h2>5. API Endpoint Test</h2>";
echo "<a href='?url=alert/getAlerts' target='_blank'>Click here to test getAlerts API</a><br>";
echo "<a href='?url=alert/getCount' target='_blank'>Click here to test getCount API</a><br>";

// Test 6: Sample Products
echo "<h2>6. Sample Products Data</h2>";
$result = $conn->query("SELECT id, name, quantity FROM products ORDER BY quantity ASC LIMIT 10");
if ($result) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Quantity</th><th>Alert Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $status = '';
        if ($row['quantity'] < 3) {
            $status = '<span style="color:red;">Out of Stock</span>';
        } elseif ($row['quantity'] <= 10) {
            $status = '<span style="color:orange;">Low Stock</span>';
        } else {
            $status = '<span style="color:green;">Normal</span>';
        }
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . $row['quantity'] . "</td>";
        echo "<td>" . $status . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error fetching products: " . $conn->error;
}

echo "<hr>";
echo "<a href='?url=dashboard/index'>Back to Dashboard</a>";
?>
