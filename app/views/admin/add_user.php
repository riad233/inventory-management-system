<div class="list-page-header">
    <h2><i class="fas fa-user-plus"></i> Add New User</h2>
    <div class="list-header-actions">
        <a href="?url=admin/users" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<?php if(!empty($data['errors'])): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <strong>Please fix the following errors:</strong>
    <ul class="mb-0 mt-1">
        <?php foreach($data['errors'] as $field => $error): ?><li><?php echo e($error); ?></li><?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row justify-content-center">
  <div class="col-md-7 col-lg-5">
    <div class="form-page-card">
      <div class="form-page-card-header" style="background:#f0f9ff;">
        <i class="fas fa-user-plus" style="color:#0369a1;"></i>
        <h5>New System User</h5>
      </div>
      <div class="card-body p-4">
        <form method="POST" action="">
          <?php echo csrf_field(); ?>
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control form-control-sm" placeholder="Minimum 3 characters" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control form-control-sm" placeholder="user@example.com" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control form-control-sm" placeholder="Minimum 6 characters" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control form-control-sm" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select form-select-sm" required>
              <?php foreach($data['roles'] as $role): ?>
                <option value="<?php echo e($role); ?>"><?php echo e($role); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <a href="?url=admin/users" class="btn btn-sm btn-light"><i class="fas fa-times"></i> Cancel</a>
            <button type="submit" name="submit" class="btn btn-primary btn-sm">
              <i class="fas fa-save me-1"></i> Add User
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
