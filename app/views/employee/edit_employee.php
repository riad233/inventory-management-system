<div class="list-page-header">
    <h2><i class="fas fa-user-edit"></i> Edit Employee</h2>
    <div class="list-header-actions">
        <a href="?url=employee/index" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<?php require_once __DIR__ . '/../../../config/dropdown_helper.php'; ?>

<div class="row justify-content-center">
  <div class="col-md-7 col-lg-5">
    <div class="form-page-card">
      <div class="form-page-card-header" style="background:#fef9c3;">
        <i class="fas fa-user-edit" style="color:#92400e;"></i>
        <h5>Edit Employee</h5>
      </div>
      <div class="card-body p-4">
        <?php if(!isset($data['employee']) || empty($data['employee'])): ?>
            <div class="alert alert-danger">Employee not found!</div>
            <a href="?url=employee/index" class="btn btn-sm btn-secondary">Go Back</a>
        <?php else: $emp = $data['employee']; ?>
        <form method="post" action="?url=employee/edit/<?php echo e($emp['User_ID']); ?>">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control form-control-sm" value="<?php echo e($emp['Name']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Employee Category</label>
                <select name="designation" class="form-select form-select-sm" required>
                    <option value="">Select Category</option>
                    <option value="Administration" <?php echo ($emp['Designation'] == 'Administration') ? 'selected' : ''; ?>>Administration</option>
                    <option value="Faculty" <?php echo ($emp['Designation'] == 'Faculty') ? 'selected' : ''; ?>>Faculty</option>
                    <option value="Staff" <?php echo ($emp['Designation'] == 'Staff') ? 'selected' : ''; ?>>Staff</option>
                    <option value="Other" <?php echo ($emp['Designation'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Department</label>
                <select name="dept_id" class="form-select form-select-sm" required>
                    <option value="">Select Department</option>
                    <?php echo DropdownHelper::renderOptions('departments', $emp['Department_ID']); ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact" class="form-control form-control-sm" value="<?php echo e($emp['Contact_Number']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control form-control-sm" value="<?php echo e($emp['Email']); ?>" required>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <a href="?url=employee/index" class="btn btn-sm btn-light"><i class="fas fa-times"></i> Cancel</a>
                <button type="submit" name="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i> Update Employee</button>
            </div>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
