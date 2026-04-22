<div class="admin-users">
  <div class="breadcrumb-nav">
    <a href="?url=dashboard/index">Dashboard</a> / <a href="?url=admin/index">Admin</a> / User Management
  </div>

  <div class="users-header">
    <h1><i class="fas fa-user-tie"></i> User Management</h1>
    <a href="?url=admin/addUser" class="btn btn-primary">
      <i class="fas fa-plus"></i> Add New User
    </a>
  </div>

  <?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php echo e($_GET['msg']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="table-responsive">
    <table class="table table-hover table-striped">
      <thead class="table-dark">
        <tr>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($data['users'])): ?>
          <?php foreach($data['users'] as $user): ?>
            <tr>
              <td>
                <strong><?php echo e($user['Username']); ?></strong>
              </td>
              <td><?php echo e($user['Email']); ?></td>
              <td>
                <span class="badge bg-<?php echo $user['Role'] == 'Admin' ? 'danger' : ($user['Role'] == 'Manager' ? 'warning' : 'info'); ?>">
                  <?php echo e($user['Role']); ?>
                </span>
              </td>
              <td style="width: 120px;">
                <a href="?url=admin/editUser/<?php echo e($user['User_ID']); ?>" class="btn btn-sm btn-warning" title="Edit">
                  <i class="fas fa-edit"></i> Edit
                </a>
                <form method="POST" action="?url=admin/deleteUser/<?php echo e($user['User_ID']); ?>" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                  <?php echo csrf_field(); ?>
                  <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4" class="text-center text-muted">No users found</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<style>
.admin-users {
  padding: 20px;
}

.users-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}

.users-header h1 {
  margin: 0;
  color: #333;
}

.table {
  background: white;
  border-radius: 8px;
  overflow: hidden;
}

.table thead {
  background: #2c3e50;
}

.table th {
  color: white;
  font-weight: 600;
  padding: 15px;
  border-bottom: 2px solid #34495e;
}

.table td {
  padding: 12px 15px;
  border-bottom: 1px solid #dee2e6;
}

.table tbody tr:hover {
  background-color: #f8f9fa;
}

.badge {
  font-size: 0.85rem;
  padding: 5px 10px;
}
</style>
