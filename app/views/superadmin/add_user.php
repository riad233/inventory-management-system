<div class="breadcrumb-nav">
    <a href="?url=dashboard/index">Dashboard</a> /
    <a href="?url=superadmin/index">SuperAdmin</a> /
    <a href="?url=superadmin/users">Users</a> / Add User
</div>

<div class="list-page-header">
    <h2><i class="fas fa-user-plus"></i> Add New User</h2>
    <a href="?url=superadmin/users" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<?php if (!empty($data['errors']['general'])): ?>
<div class="alert alert-danger"><?php echo e($data['errors']['general']); ?></div>
<?php endif; ?>

<div class="row justify-content-center">
  <div class="col-md-7 col-lg-5">
    <div class="form-page-card">
      <div class="form-page-card-header" style="background:#f5f3ff;">
        <i class="fas fa-user-plus" style="color:#6d28d9;"></i>
        <h5>New System User</h5>
      </div>
      <div class="card-body p-4">
        <form method="POST" action="">
          <?php echo csrf_field(); ?>

          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control form-control-sm
                   <?php echo isset($data['errors']['username']) ? 'is-invalid' : ''; ?>"
                   placeholder="Min 3 characters" required>
            <?php if (isset($data['errors']['username'])): ?>
            <div class="invalid-feedback"><?php echo e($data['errors']['username']); ?></div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label class="form-label">Email Address</label>
            <input type="email" name="email" class="form-control form-control-sm
                   <?php echo isset($data['errors']['email']) ? 'is-invalid' : ''; ?>"
                   placeholder="user@example.com" required>
            <?php if (isset($data['errors']['email'])): ?>
            <div class="invalid-feedback"><?php echo e($data['errors']['email']); ?></div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select form-select-sm">
              <?php foreach ($data['roles'] as $role): ?>
              <option value="<?php echo e($role); ?>"><?php echo e($role); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control form-control-sm
                   <?php echo isset($data['errors']['password']) ? 'is-invalid' : ''; ?>"
                   placeholder="Min 6 characters" required>
            <?php if (isset($data['errors']['password'])): ?>
            <div class="invalid-feedback"><?php echo e($data['errors']['password']); ?></div>
            <?php endif; ?>
          </div>

          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control form-control-sm
                   <?php echo isset($data['errors']['confirm_password']) ? 'is-invalid' : ''; ?>"
                   required>
            <?php if (isset($data['errors']['confirm_password'])): ?>
            <div class="invalid-feedback"><?php echo e($data['errors']['confirm_password']); ?></div>
            <?php endif; ?>
          </div>

          <div class="d-flex justify-content-between mt-3">
            <a href="?url=superadmin/users" class="btn btn-sm btn-light">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" name="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save me-1"></i> Create User
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
