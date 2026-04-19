<?php
if (empty($_SESSION['username'])) {
    header("Location: ?url=auth/login");
    exit;
}

$product = $data['product'] ?? null;
$departments = $data['departments'] ?? [];
$error = $data['error'] ?? null;
$success = $data['success'] ?? null;

if (!$product) {
    echo "Product not found";
    exit;
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Product</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo e($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="?url=stock/update">
                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name *</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo e($product['name']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity *</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo e($product['quantity']); ?>" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label for="department_id" class="form-label">Department</label>
                            <select class="form-control" id="department_id" name="department_id">
                                <option value="">Select Department</option>
                                <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept['Department_ID']; ?>" <?php echo ($product['department_id'] == $dept['Department_ID']) ? 'selected' : ''; ?>>
                                    <?php echo e($dept['Department_Name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo e($product['description'] ?? ''); ?></textarea>
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary">Update Product</button>
                            <a href="?url=stock/index" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
