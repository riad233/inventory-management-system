<?php
if (empty($_SESSION['username'])) {
    header("Location: ?url=auth/login");
    exit;
}

// Only show to Admin/Manager
$role = $_SESSION['role'] ?? '';
if (!in_array($role, ['Admin', 'Manager'], true)) {
    echo "<div class='alert alert-danger'>Access Denied. This page is for Admins and Managers only.</div>";
    exit;
}

$stockAlerts = $data['stock_alerts'] ?? [];
$lowItems = $stockAlerts['low_items'] ?? [];
$outItems = $stockAlerts['out_items'] ?? [];
?>

<div class="container-fluid">
    <div class="page-header mb-4">
        <div class="page-title">
            <i class="fas fa-exclamation-circle" style="color: #ff9800;"></i>
            Stock Alerts
        </div>
        <p class="text-muted">Manage low and out-of-stock inventory items</p>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-danger">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-times-circle text-danger"></i> Out of Stock
                    </h5>
                    <h2 class="text-danger"><?php echo e($stockAlerts['total_out']); ?></h2>
                    <small class="text-muted">Items with quantity < 3 (EMERGENCY)</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-warning">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-exclamation-triangle text-warning"></i> Low Stock
                    </h5>
                    <h2 class="text-warning"><?php echo e($stockAlerts['total_low']); ?></h2>
                    <small class="text-muted">Items with quantity 3-10</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Out of Stock Section -->
    <?php if (!empty($outItems)): ?>
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0">
                <i class="fas fa-times-circle"></i> Out of Stock Items - EMERGENCY (<?php echo count($outItems); ?>)
            </h5>
        </div>
        <div class="card-body">
            <?php foreach ($outItems as $item): ?>
            <div class="stock-alert-item out-of-stock">
                <div class="stock-alert-info">
                    <div class="stock-alert-name">
                        <?php echo e($item['Item_Name']); ?>
                    </div>
                    <div class="stock-alert-details">
                        <span><strong>Item ID:</strong> <?php echo e($item['Item_ID']); ?></span>
                        <span><strong>SKU:</strong> <?php echo e($item['SKU']); ?></span>
                        <span><strong>Category:</strong> <?php echo e($item['Category']); ?></span>
                    </div>
                </div>
                <div class="stock-alert-quantity">Qty: <?php echo e($item['Quantity']); ?></div>
                <div class="stock-alert-actions">
                    <a href="?url=request/create" class="btn-request" title="Create Emergency Request" style="background-color: #dc3545; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-weight: bold;">
                        <i class="fas fa-bell"></i> Emergency Request
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-times-circle"></i> Out of Stock Items</h5>
        </div>
        <div class="card-body text-center text-muted">
            <i class="fas fa-check-circle" style="font-size: 3em; color: #28a745;"></i>
            <p class="mt-3">No out-of-stock items. Great!</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Low Stock Section -->
    <?php if (!empty($lowItems)): ?>
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="fas fa-exclamation-triangle"></i> Low Stock Items (<?php echo count($lowItems); ?>)
            </h5>
        </div>
        <div class="card-body">
            <?php foreach ($lowItems as $item): ?>
            <div class="stock-alert-item low-stock">
                <div class="stock-alert-info">
                    <div class="stock-alert-name">
                        <?php echo e($item['Item_Name']); ?>
                    </div>
                    <div class="stock-alert-details">
                        <span><strong>Item ID:</strong> <?php echo e($item['Item_ID']); ?></span>
                        <span><strong>SKU:</strong> <?php echo e($item['SKU']); ?></span>
                        <span><strong>Current Qty:</strong> <?php echo e($item['Quantity']); ?></span>
                    </div>
                </div>
                <div class="stock-alert-quantity">Qty: <?php echo e($item['Quantity']); ?></div>
                <div class="stock-alert-actions">
                    <a href="?url=request/create" class="btn-request" title="Create Request" style="background-color: #ff9800; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none;">
                        <i class="fas fa-plus"></i> Request
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Low Stock Items (3-10)</h5>
        </div>
        <div class="card-body text-center text-muted">
            <i class="fas fa-check-circle" style="font-size: 3em; color: #28a745;"></i>
            <p class="mt-3">All items are well-stocked!</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Table View (Alternative) -->
    <?php if (!empty($lowItems) || !empty($outItems)): ?>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-table"></i> Stock Alert Summary Table</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Item Name</th>
                        <th>Item ID</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Alert Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $allItems = array_merge(
                        array_map(fn($i) => array_merge($i, ['alert_status' => 'out']), $outItems),
                        array_map(fn($i) => array_merge($i, ['alert_status' => 'low']), $lowItems)
                    );
                    
                    foreach ($allItems as $item):
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo e($item['Item_Name']); ?></strong>
                        </td>
                        <td><?php echo e($item['Item_ID']); ?></td>
                        <td><?php echo e($item['SKU']); ?></td>
                        <td><?php echo e($item['Category']); ?></td>
                        <td>
                            <span class="badge" style="background-color: <?php echo ($item['Quantity'] < 3) ? '#dc3545' : '#ff9800'; ?>; color: white;">
                                <?php echo e($item['Quantity']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($item['alert_status'] === 'out'): ?>
                                <span class="badge bg-danger">🚨 Out of Stock (< 3)</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">⚠️ Low Stock (3-10)</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($item['alert_status'] === 'out'): ?>
                                <!-- Emergency Request button for out of stock -->
                                <a href="?url=request/create" class="btn btn-sm btn-danger" title="Create Emergency Request">
                                    <i class="fas fa-bell"></i> Emergency
                                </a>
                            <?php else: ?>
                                <!-- Request button only for low stock -->
                                <a href="?url=request/create" class="btn btn-sm btn-warning" title="Create Request">
                                    <i class="fas fa-plus"></i> Request
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
