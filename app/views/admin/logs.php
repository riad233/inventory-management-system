<div class="admin-logs">
  <div class="breadcrumb-nav">
    <a href="?url=dashboard/index">Dashboard</a> / <a href="?url=admin/index">Admin</a> / Activity Logs
  </div>

  <div class="logs-header">
    <h1><i class="fas fa-history"></i> Activity Logs</h1>
    <p>Monitor all system activities and changes</p>
  </div>

  <div class="table-responsive">
    <table class="table table-hover table-striped">
      <thead class="table-dark">
        <tr>
          <th>Timestamp</th>
          <th>User</th>
          <th>Action</th>
          <th>Module</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($data['logs'])): ?>
          <?php foreach($data['logs'] as $log): ?>
            <tr>
              <td><?php echo e($log['timestamp']); ?></td>
              <td><strong><?php echo e($log['user']); ?></strong></td>
              <td><?php echo e($log['action']); ?></td>
              <td><?php echo e($log['module']); ?></td>
              <td>
                <span class="badge bg-<?php echo $log['status'] == 'success' ? 'success' : 'warning'; ?>">
                  <?php echo e(ucfirst($log['status'])); ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-center text-muted">No activity logs found</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<style>
.admin-logs {
  padding: 20px;
}

.logs-header {
  margin-bottom: 30px;
}

.logs-header h1 {
  color: #333;
  margin-bottom: 5px;
}

.logs-header p {
  color: #666;
  margin: 0;
}

.table {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
