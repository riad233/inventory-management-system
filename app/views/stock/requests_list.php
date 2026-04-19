<?php
if (empty($_SESSION['username'])) {
    header("Location: ?url=auth/login");
    exit;
}

$requests = $data['requests'] ?? [];
$error = $data['error'] ?? null;
$success = $data['success'] ?? null;
?>

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Stock Requests</h2>
            <a href="?url=stock-request/create" class="btn btn-primary">+ New Request</a>
        </div>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo e($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e($success); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h5>All Requests</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Requested By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($requests)): ?>
                        <?php foreach ($requests as $req): ?>
                        <tr>
                            <td><?php echo e($req['id']); ?></td>
                            <td><?php echo e($req['product_name'] ?? 'N/A'); ?></td>
                            <td><?php echo e($req['quantity']); ?></td>
                            <td>
                                <span class="badge <?php echo $req['type'] === 'emergency' ? 'bg-danger' : 'bg-info'; ?>">
                                    <?php echo e(ucfirst($req['type'])); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php 
                                    if ($req['status'] === 'pending') echo 'bg-warning';
                                    elseif ($req['status'] === 'approved') echo 'bg-success';
                                    elseif ($req['status'] === 'rejected') echo 'bg-danger';
                                    else echo 'bg-info';
                                ?>">
                                    <?php echo e(ucfirst($req['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo e($req['requested_by_name'] ?? 'N/A'); ?></td>
                            <td><?php echo e(date('M d, Y', strtotime($req['created_at']))); ?></td>
                            <td>
                                <?php if ($req['status'] === 'pending'): ?>
                                <a href="?url=stock-request/approve&id=<?php echo $req['id']; ?>" class="btn btn-sm btn-success">Approve</a>
                                <a href="?url=stock-request/reject&id=<?php echo $req['id']; ?>" class="btn btn-sm btn-danger">Reject</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">No requests found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
