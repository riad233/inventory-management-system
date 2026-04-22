<div class="admin-panel">
  <div class="breadcrumb-nav">
    <a href="?url=dashboard/index">Dashboard</a> / Admin Settings
  </div>

  <div class="admin-header">
    <h1><i class="fas fa-cog"></i> Admin Control Panel</h1>
    <p>Manage system settings, users, and monitor activity</p>
  </div>

  <div class="admin-stats">
    <div class="stat-card">
      <div class="stat-icon"><i class="fas fa-users"></i></div>
      <div class="stat-content">
        <div class="stat-value"><?php echo e($data['total_users'] ?? 0); ?></div>
        <div class="stat-label">Total Users</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fas fa-cube"></i></div>
      <div class="stat-content">
        <div class="stat-value"><?php echo e($data['total_assets'] ?? 0); ?></div>
        <div class="stat-label">Total Assets</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fas fa-user-tie"></i></div>
      <div class="stat-content">
        <div class="stat-value"><?php echo e($data['total_employees'] ?? 0); ?></div>
        <div class="stat-label">Total Employees</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon"><i class="fas fa-building"></i></div>
      <div class="stat-content">
        <div class="stat-value"><?php echo e($data['total_vendors'] ?? 0); ?></div>
        <div class="stat-label">Total Vendors</div>
      </div>
    </div>
  </div>

  <div class="admin-section">
    <h3><i class="fas fa-sliders-h"></i> System Settings</h3>
    <div class="settings-grid">
      <div class="setting-item">
        <label>Application Name</label>
        <p><?php echo e($data['system_settings']['app_name'] ?? 'IMS'); ?></p>
      </div>
      <div class="setting-item">
        <label>Version</label>
        <p><?php echo e($data['system_settings']['app_version'] ?? '1.0.0'); ?></p>
      </div>
      <div class="setting-item">
        <label>Session Timeout (seconds)</label>
        <p><?php echo e($data['system_settings']['session_timeout'] ?? 1800); ?> (30 minutes)</p>
      </div>
      <div class="setting-item">
        <label>Items Per Page</label>
        <p><?php echo e($data['system_settings']['items_per_page'] ?? 20); ?></p>
      </div>
      <div class="setting-item">
        <label>Maintenance Reminder (days)</label>
        <p><?php echo e($data['system_settings']['maintenance_threshold'] ?? 365); ?></p>
      </div>
    </div>
  </div>

  <div class="admin-section">
    <h3><i class="fas fa-link"></i> Quick Access</h3>
    <div class="admin-links">
      <a href="?url=admin/users" class="admin-link">
        <i class="fas fa-user-tie"></i>
        <span>User Management</span>
        <small>Add, edit, or remove users</small>
      </a>
      <a href="?url=admin/logs" class="admin-link">
        <i class="fas fa-history"></i>
        <span>Activity Logs</span>
        <small>Monitor system activity</small>
      </a>
      <a href="?url=dashboard/index" class="admin-link">
        <i class="fas fa-chart-line"></i>
        <span>Dashboard</span>
        <small>View system overview</small>
      </a>
    </div>
  </div>
</div>

<style>
.admin-panel {
  padding: 20px;
}

.admin-header {
  margin-bottom: 30px;
}

.admin-header h1 {
  font-size: 2rem;
  color: #333;
  margin-bottom: 5px;
}

.admin-header p {
  color: #666;
  font-size: 1.1rem;
}

.admin-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 40px;
}

.stat-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 20px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 15px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  transition: transform 0.3s;
}

.stat-card:hover {
  transform: translateY(-5px);
}

.stat-icon {
  font-size: 2rem;
}

.stat-value {
  font-size: 1.8rem;
  font-weight: bold;
}

.stat-label {
  font-size: 0.9rem;
  opacity: 0.9;
}

.admin-section {
  background: #f8f9fa;
  padding: 25px;
  border-radius: 8px;
  margin-bottom: 30px;
  border-left: 4px solid #667eea;
}

.admin-section h3 {
  margin-bottom: 20px;
  color: #333;
}

.settings-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.setting-item {
  background: white;
  padding: 15px;
  border-radius: 6px;
  border: 1px solid #dee2e6;
}

.setting-item label {
  display: block;
  font-weight: bold;
  color: #666;
  margin-bottom: 8px;
  font-size: 0.9rem;
}

.setting-item p {
  margin: 0;
  color: #333;
  font-size: 1.1rem;
}

.admin-links {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.admin-link {
  background: white;
  padding: 20px;
  border-radius: 8px;
  border: 2px solid #dee2e6;
  text-decoration: none;
  color: #333;
  transition: all 0.3s;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.admin-link:hover {
  border-color: #667eea;
  background: #f8f9ff;
  transform: translateY(-3px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

.admin-link i {
  font-size: 2rem;
  color: #667eea;
  margin-bottom: 10px;
}

.admin-link span {
  font-weight: bold;
  display: block;
  margin-bottom: 5px;
}

.admin-link small {
  color: #666;
  font-size: 0.9rem;
}
</style>
