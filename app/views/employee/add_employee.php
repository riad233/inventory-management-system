<div class="list-page-header">
    <h2><i class="fas fa-user-plus"></i> Add Employee</h2>
    <div class="list-header-actions">
        <a href="?url=employee/index" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<?php require_once __DIR__ . '/../../../config/dropdown_helper.php'; ?>

<?php if(isset($data['errors']) && !empty($data['errors'])): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <strong>Please fix the following errors:</strong>
    <ul class="mb-0 mt-1">
        <?php foreach($data['errors'] as $field => $message): ?><li><?php echo e($message); ?></li><?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row justify-content-center">
  <div class="col-md-7 col-lg-5">
    <div class="form-page-card">
      <div class="form-page-card-header" style="background:#f0fdf4;">
        <i class="fas fa-user-plus" style="color:#15803d;"></i>
        <h5>New Employee</h5>
      </div>
      <div class="card-body p-4">
        <form method="post" action="?url=employee/add">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control form-control-sm" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Employee Category</label>
                <select name="designation" class="form-select form-select-sm" required>
                    <option value="">Select Category</option>
                    <option value="Administration">Administration</option>
                    <option value="Faculty">Faculty</option>
                    <option value="Staff">Staff</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Department</label>
                <select name="dept_id" class="form-select form-select-sm" required>
                    <option value="">Select Department</option>
                    <?php echo DropdownHelper::renderOptions('departments'); ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact" class="form-control form-control-sm" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control form-control-sm" required>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <a href="?url=employee/index" class="btn btn-sm btn-light"><i class="fas fa-times"></i> Cancel</a>
                <button type="submit" name="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i> Save Employee</button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
