<div class="breadcrumb-nav">
    <a href="?url=dashboard/index">Dashboard</a> &gt; <span>Change Password</span>
</div>

<div class="page-title">
    <i class="fas fa-key"></i> Change Password
</div>

<?php if(!empty($data['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo e($data['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(!empty($data['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e($data['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow card-max-520">
    <div class="card-body">
        <form method="post" action="?url=auth/changePassword">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <div class="text-end">
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Password
                </button>
            </div>
        </form>
    </div>
</div>
