<?php
if (empty($_SESSION['username'])) {
    header("Location: ?url=auth/login");
    exit;
}

$product = $data['product'] ?? null;
$products = $data['products'] ?? [];
$isAlert = $data['isAlert'] ?? false;
$type = $data['type'] ?? 'normal';
$error = $data['error'] ?? null;
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4><?php echo $isAlert ? 'Request from Alert' : 'Create Stock Request'; ?></h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo e($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="?url=stock-request/store">
                        <!-- Product Selection (only if not from alert) -->
                        <?php if (!$isAlert): ?>
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Select Product *</label>
                            <select class="form-control" id="product_id" name="product_id" required>
                                <option value="">-- Choose a product --</option>
                                <?php foreach ($products as $prod): ?>
                                <option value="<?php echo $prod['id']; ?>">
                                    <?php echo e($prod['name']); ?> (Qty: <?php echo e($prod['quantity']); ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php else: ?>
                        <!-- Auto-filled for alert -->
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <div class="mb-3">
                            <label class="form-label">Product</label>
                            <input type="text" class="form-control" value="<?php echo e($product['name']); ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Quantity</label>
                            <input type="text" class="form-control" value="<?php echo e($product['quantity']); ?>" disabled>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity Needed *</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" required>
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Request Type</label>
                            <select class="form-control" id="type" name="type">
                                <option value="normal" <?php echo $type === 'normal' ? 'selected' : ''; ?>>Normal</option>
                                <option value="emergency" <?php echo $type === 'emergency' ? 'selected' : ''; ?>>Emergency</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary">Submit Request</button>
                            <a href="?url=stock/index" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
