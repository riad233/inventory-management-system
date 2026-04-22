<div class="admin-add-user">
  <div class="breadcrumb-nav">
    <a href="?url=dashboard/index">Dashboard</a> / <a href="?url=admin/index">Admin</a> / <a href="?url=admin/users">Users</a> / Add User
  </div>

  <div class="form-header">
    <h1><i class="fas fa-user-plus"></i> Add New User</h1>
  </div>

  <div class="form-container">
    <?php if(!empty($data['errors'])): ?>
      <div class="alert alert-danger">
        <strong>Validation Errors:</strong>
        <ul>
          <?php foreach($data['errors'] as $error): ?>
            <li><?php echo e($error); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" class="user-form">
      <?php echo csrf_field(); ?>
      
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" class="form-control" required>
        <small class="form-text text-muted">Minimum 3 characters</small>
      </div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" required>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control" required>
        <small class="form-text text-muted">Minimum 6 characters</small>
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
      </div>

      <div class="form-group">
        <label for="role">Role</label>
        <select id="role" name="role" class="form-control" required>
          <?php foreach($data['roles'] as $role): ?>
            <option value="<?php echo e($role); ?>"><?php echo e($role); ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-actions">
        <button type="submit" name="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Add User
        </button>
        <a href="?url=admin/users" class="btn btn-secondary">
          <i class="fas fa-times"></i> Cancel
        </a>
      </div>
    </form>
  </div>
</div>

<style>
.admin-add-user {
  padding: 20px;
}

.form-header {
  margin-bottom: 30px;
}

.form-header h1 {
  color: #333;
}

.form-container {
  background: white;
  padding: 30px;
  border-radius: 8px;
  max-width: 500px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.user-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-weight: 600;
  margin-bottom: 8px;
  color: #333;
}

.form-group .form-control {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
}

.form-group .form-control:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-text {
  font-size: 0.85rem;
  margin-top: 5px;
}

.form-actions {
  display: flex;
  gap: 10px;
  margin-top: 20px;
}

.form-actions .btn {
  flex: 1;
  padding: 10px;
}

.alert {
  margin-bottom: 20px;
}

.alert ul {
  margin: 10px 0 0 20px;
}
</style>
