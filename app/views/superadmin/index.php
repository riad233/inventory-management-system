<?php
$roleCounts  = $data['role_counts']  ?? [];
$totalUsers  = $data['total_users']  ?? 0;
$totalPerms  = $data['total_perms']  ?? 0;
?>

<div class="breadcrumb-nav">
    <a href="?url=dashboard/index">Dashboard</a> / SuperAdmin Panel
</div>

<div class="list-page-header">
    <h2><i class="fas fa-shield-alt"></i> SuperAdmin Control Panel</h2>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#6f42c1,#4b0082)">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-content">
                <div class="stat-value"><?php echo e($totalUsers); ?></div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>
    </div>
    <?php foreach ($roleCounts as $role => $count): ?>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#0ea5e9,#0369a1)">
            <div class="stat-icon"><i class="fas fa-user-tag"></i></div>
            <div class="stat-content">
                <div class="stat-value"><?php echo e($count); ?></div>
                <div class="stat-label"><?php echo e($role); ?> Users</div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <div class="col-sm-6 col-lg-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#10b981,#065f46)">
            <div class="stat-icon"><i class="fas fa-key"></i></div>
            <div class="stat-content">
                <div class="stat-value"><?php echo e($totalPerms); ?></div>
                <div class="stat-label">Permissions Defined</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6 col-lg-4">
        <a href="?url=superadmin/permissions" class="admin-action-card">
            <i class="fas fa-key fa-2x mb-2" style="color:#6f42c1;"></i>
            <h5>Permission Matrix</h5>
            <p>Set which permissions each role (Admin, Manager, Employee) has</p>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="?url=superadmin/users" class="admin-action-card">
            <i class="fas fa-users-cog fa-2x mb-2" style="color:#0369a1;"></i>
            <h5>User Management</h5>
            <p>Create, edit and delete all system users including Admins</p>
        </a>
    </div>
    <div class="col-md-6 col-lg-4">
        <a href="?url=admin/logs" class="admin-action-card">
            <i class="fas fa-history fa-2x mb-2" style="color:#059669;"></i>
            <h5>Activity Logs</h5>
            <p>Monitor all system activities and security events</p>
        </a>
    </div>
</div>

<style>
.admin-action-card{display:flex;flex-direction:column;align-items:center;text-align:center;
  padding:32px 24px;background:#fff;border:1px solid #e5e7eb;border-radius:12px;
  text-decoration:none;color:#374151;transition:all .2s;cursor:pointer;}
.admin-action-card:hover{box-shadow:0 8px 24px rgba(0,0,0,.1);transform:translateY(-4px);color:#111;}
.admin-action-card h5{font-weight:600;margin-bottom:6px;}
.admin-action-card p{font-size:.88rem;color:#6b7280;margin:0;}
</style>
