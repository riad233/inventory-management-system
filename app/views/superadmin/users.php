<?php
$users = $data['users'] ?? [];
$search = trim($_GET['search'] ?? '');
$roleFilter = trim($_GET['role'] ?? '');

if ($search !== '' || $roleFilter !== '') {
    $users = array_filter($users, function($u) use ($search, $roleFilter) {
        $matchSearch = $search === '' ||
            stripos($u['Username'], $search) !== false ||
            stripos($u['Email'], $search) !== false;
        $matchRole = $roleFilter === '' || ($u['Role'] ?? '') === $roleFilter;
        return $matchSearch && $matchRole;
    });
    $users = array_values($users);
}
$total = count($users);
?>

<div class="breadcrumb-nav">
    <a href="?url=dashboard/index">Dashboard</a> /
    <a href="?url=superadmin/index">SuperAdmin</a> / User Management
</div>

<div class="list-page-header">
    <h2><i class="fas fa-users-cog"></i> User Management</h2>
    <div class="list-header-actions">
        <a href="?url=superadmin/addUser" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add User
        </a>
    </div>
</div>

<?php if (isset($_GET['msg'])): ?>
<div class="alert alert-info alert-dismissible fade show">
    <?php echo e($_GET['msg']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form method="GET" class="mb-3">
    <input type="hidden" name="url" value="superadmin/users">
    <div class="filter-bar">
        <div class="filter-controls">
            <input type="text" name="search" class="filter-search"
                   placeholder="Search username or email…" value="<?php echo e($search); ?>">
            <select name="role" class="filter-select" onchange="this.form.submit()">
                <option value="">All Roles</option>
                <?php foreach (['SuperAdmin','Admin','Manager','Employee'] as $r): ?>
                <option value="<?php echo e($r); ?>" <?php echo $roleFilter===$r?'selected':''; ?>><?php echo e($r); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-search"></i></button>
            <?php if ($search !== '' || $roleFilter !== ''): ?>
            <a href="?url=superadmin/users" class="btn btn-outline-secondary btn-sm">Clear</a>
            <?php endif; ?>
        </div>
    </div>
</form>

<div class="result-count"><?php echo $total; ?> user(s) found</div>

<div class="list-table-container">
    <div class="list-table-wrapper">
        <table class="list-table">
            <thead>
                <tr>
                    <th class="th-sl">SL</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th class="th-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($users): ?>
                <?php foreach ($users as $i => $user): ?>
                <tr>
                    <td class="td-sl"><?php echo $i + 1; ?></td>
                    <td><strong><?php echo e($user['Username']); ?></strong></td>
                    <td><?php echo e($user['Email']); ?></td>
                    <td>
                        <?php
                        $r = $user['Role'] ?? '';
                        $cls = match($r) {
                            'SuperAdmin' => 'role-pill-superadmin',
                            'Admin'      => 'status-rejected',
                            'Manager'    => 'status-pending',
                            default      => 'status-assigned',
                        };
                        ?>
                        <span class="status-badge <?php echo $cls; ?>"><?php echo e($r); ?></span>
                    </td>
                    <td style="font-size:.85rem;color:#6b7280;">
                        <?php echo e(isset($user['Created_Date']) ? date('d M Y', strtotime($user['Created_Date'])) : '—'); ?>
                    </td>
                    <td class="td-actions">
                        <a href="?url=superadmin/editUser/<?php echo e($user['User_ID']); ?>" class="btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <?php if ((int)$user['User_ID'] !== (int)($_SESSION['user_id'] ?? 0)): ?>
                        <form method="POST" action="?url=superadmin/deleteUser/<?php echo e($user['User_ID']); ?>"
                              style="display:inline;"
                              onsubmit="return confirm('Delete user <?php echo e($user['Username']); ?>? This cannot be undone.')">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn-delete"><i class="fas fa-trash"></i> Delete</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center py-4 text-muted">No users found</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.role-pill-superadmin{display:inline-block;border-radius:20px;padding:2px 12px;
  background:#ede9fe;color:#6d28d9;border:1px solid #c4b5fd;font-weight:600;font-size:.82rem;}
</style>
