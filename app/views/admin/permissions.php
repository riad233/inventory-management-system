<?php
$grouped       = $data['grouped']       ?? [];
$rolePerms     = $data['rolePerms']     ?? [];
$editableRoles = $data['editableRoles'] ?? [];
$success       = $data['success']       ?? null;
$error         = $data['error']         ?? null;

$has = function(string $role, string $key) use ($rolePerms): bool {
    return in_array($key, $rolePerms[$role] ?? [], true);
};
?>

<div class="breadcrumb-nav">
    <a href="?url=dashboard/index">Dashboard</a> /
    <a href="?url=admin/index">Admin</a> / Permission Matrix
</div>

<div class="list-page-header">
    <h2><i class="fas fa-key"></i> Permission Matrix</h2>
    <p class="text-muted mb-0" style="font-size:.9rem">
        Toggle permissions for each role. <strong>SuperAdmin</strong> always has full access.
    </p>
</div>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle me-1"></i> <?php echo e($success); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle me-1"></i> <?php echo e($error); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form method="POST" action="">
    <?php echo csrf_field(); ?>

    <div class="perm-matrix-wrapper">
        <table class="perm-matrix-table">
            <thead>
                <tr>
                    <th class="perm-name-col">Permission</th>
                    <?php foreach ($editableRoles as $role): ?>
                    <th class="perm-role-col">
                        <span class="role-pill role-pill-<?php echo strtolower(e($role)); ?>">
                            <?php echo e($role); ?>
                        </span>
                    </th>
                    <?php endforeach; ?>
                    <th class="perm-role-col">
                        <span class="role-pill role-pill-superadmin">SuperAdmin</span>
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($grouped as $module => $perms): ?>
                <tr class="module-header-row">
                    <td colspan="<?php echo count($editableRoles) + 2; ?>">
                        <i class="fas fa-folder-open me-1"></i> <?php echo e($module); ?>
                    </td>
                </tr>
                <?php foreach ($perms as $perm): ?>
                <tr>
                    <td class="perm-name-cell">
                        <span class="perm-key"><?php echo e($perm['permission_key']); ?></span>
                        <small class="perm-label"><?php echo e($perm['label']); ?></small>
                    </td>
                    <?php foreach ($editableRoles as $role): ?>
                    <td class="perm-check-cell">
                        <label class="perm-checkbox-wrap">
                            <input type="checkbox"
                                   name="perms[<?php echo e($role); ?>][]"
                                   value="<?php echo e($perm['permission_key']); ?>"
                                   class="perm-checkbox"
                                   <?php echo $has($role, $perm['permission_key']) ? 'checked' : ''; ?>>
                        </label>
                    </td>
                    <?php endforeach; ?>
                    <td class="perm-check-cell">
                        <i class="fas fa-lock-open" style="color:#6f42c1;opacity:.7;"
                           title="SuperAdmin always has this permission"></i>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="perm-matrix-footer">
        <span style="font-size:.85rem;color:#6b7280;">
            <i class="fas fa-info-circle me-1"></i>Check a box to grant the permission to that role.
        </span>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Save Permissions
        </button>
    </div>
</form>

<style>
.perm-matrix-wrapper{overflow-x:auto;border:1px solid #e5e7eb;border-radius:10px;background:#fff;}
.perm-matrix-table{width:100%;border-collapse:collapse;font-size:.9rem;}
.perm-matrix-table thead tr{background:#f1f5f9;}
.perm-matrix-table th{padding:12px 14px;text-align:center;font-weight:600;color:#374151;border-bottom:2px solid #e5e7eb;}
.perm-name-col{text-align:left!important;min-width:240px;}
.perm-role-col{min-width:110px;}
.module-header-row td{background:#f8fafc;padding:8px 14px;font-weight:700;font-size:.82rem;
  text-transform:uppercase;letter-spacing:.5px;color:#64748b;border-top:2px solid #e5e7eb;}
.perm-matrix-table td{padding:8px 14px;border-bottom:1px solid #f1f5f9;vertical-align:middle;}
.perm-matrix-table tr:hover td{background:#fafafa;}
.perm-name-cell{display:flex;flex-direction:column;gap:2px;}
.perm-key{font-family:monospace;font-size:.82rem;color:#0369a1;}
.perm-label{font-size:.8rem;color:#6b7280;}
.perm-check-cell{text-align:center;}
.perm-checkbox-wrap{display:inline-flex;cursor:pointer;}
.perm-checkbox{width:18px;height:18px;accent-color:#6f42c1;cursor:pointer;}
.role-pill{display:inline-block;border-radius:20px;padding:3px 12px;font-weight:600;font-size:.8rem;}
.role-pill-admin    {background:#fdecea;color:#c0392b;border:1px solid #f5c6cb;}
.role-pill-manager  {background:#fff3cd;color:#856404;border:1px solid #ffc107;}
.role-pill-staff    {background:#e0f2fe;color:#0369a1;border:1px solid #bae6fd;}
.role-pill-employee {background:#d1ecf1;color:#0c5460;border:1px solid #bee5eb;}
.role-pill-superadmin{background:#ede9fe;color:#6d28d9;border:1px solid #c4b5fd;}
.perm-matrix-footer{display:flex;justify-content:space-between;align-items:center;
  margin-top:18px;flex-wrap:wrap;gap:10px;}
</style>
