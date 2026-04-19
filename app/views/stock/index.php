<?php
if (empty($_SESSION['username'])) {
    header("Location: ?url=auth/login");
    exit;
}

$products = $data['products'] ?? [];
$alerts = $data['alerts'] ?? ['out_of_stock' => [], 'low_stock' => [], 'total_out' => 0, 'total_low' => 0];
$error = $data['error'] ?? null;
$success = $data['success'] ?? null;
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Stock Management</h2>
            <a href="?url=stock/add" class="btn btn-primary">+ Add Product</a>
        </div>
    </div>

    <!-- Alerts Section -->
    <?php if (!empty($alerts['out_of_stock']) || !empty($alerts['low_stock'])): ?>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-danger">
                <div class="card-body">
                    <h5 class="card-title text-danger">Out of Stock</h5>
                    <h2><?php echo e($alerts['total_out']); ?></h2>
                    <small class="text-muted">Products with 0 quantity</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-warning">
                <div class="card-body">
                    <h5 class="card-title text-warning">Low Stock</h5>
                    <h2><?php echo e($alerts['total_low']); ?></h2>
                    <small class="text-muted">Products with 1-9 quantity</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts Table -->
    <?php if (!empty($alerts['out_of_stock']) || !empty($alerts['low_stock'])): ?>
    <div class="card mb-4">
        <div class="card-header">
            <h5>Stock Alerts</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $allAlerts = array_merge($alerts['out_of_stock'], $alerts['low_stock']);
                    foreach ($allAlerts as $item):
                    ?>
                    <tr>
                        <td><?php echo e($item['name']); ?></td>
                        <td><?php echo e($item['quantity']); ?></td>
                        <td>
                            <span class="badge <?php echo $item['quantity'] == 0 ? 'bg-danger' : 'bg-warning'; ?>">
                                <?php echo $item['quantity'] == 0 ? 'Out of Stock' : 'Low Stock'; ?>
                            </span>
                        </td>
                        <td>
                            <a href="?url=stock-request/create&product_id=<?php echo $item['id']; ?>&source=alert&type=<?php echo $item['quantity'] == 0 ? 'emergency' : 'normal'; ?>" 
                               class="btn btn-sm btn-primary">
                                Request
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- No Alerts Message -->
    <?php if (empty($alerts['out_of_stock']) && empty($alerts['low_stock'])): ?>
    <div class="alert alert-info text-center">
        <i class="fas fa-check-circle"></i> No alerts available. All stock is healthy!
    </div>
    <?php endif; ?>

    <!-- All Products Table -->
    <div class="card">
        <div class="card-header">
            <h5>All Products</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo e($product['id']); ?></td>
                            <td><?php echo e($product['name']); ?></td>
                            <td><?php echo e($product['quantity']); ?></td>
                            <td><?php echo e($product['Department_Name'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="badge <?php 
                                    if ($product['quantity'] == 0) {
                                        echo 'bg-danger';
                                    } elseif ($product['quantity'] < 10) {
                                        echo 'bg-warning';
                                    } else {
                                        echo 'bg-success';
                                    }
                                ?>">
                                    <?php 
                                        if ($product['quantity'] == 0) {
                                            echo 'Out of Stock';
                                        } elseif ($product['quantity'] < 10) {
                                            echo 'Low Stock';
                                        } else {
                                            echo 'In Stock';
                                        }
                                    ?>
                                </span>
                            </td>
                            <td>
                                <a href="?url=stock/edit&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="?url=stock/delete&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete product?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No products found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
