<?php
$users = isset($data['users']) && is_array($data['users']) ? $data['users'] : [];

$search     = trim($_GET['search'] ?? '');
$roleFilter = trim($_GET['role'] ?? '');

if ($search !== '' || $roleFilter !== '') {
    $users = array_filter($users, function($u) use ($search, $roleFilter) {
        $matchSearch = $search === '' ||
            stripos($u['Username'], $search) !== false ||
            stripos($u['Email'], $search) !== false;
        $matchRole = $roleFilter === '' || ($u['Role'] ?? '') === $roleFilter;
        return $matchSearch && $matchRole;
    });
}
$users = array_values($users);
$total = count($users);

$allRoles = array_unique(array_filter(array_column(isset($data['users'])?$data['users']:[], 'Role')));
sort($allRoles);

$pageSize   = max(1, (int)($_GET['page_size'] ?? 20));
$totalPages = $total > 0 ? (int)ceil($total / $pageSize) : 1;
$page       = max(1, min((int)($_GET['page'] ?? 1), $totalPages));
$offset     = ($page - 1) * $pageSize;
$pageRows   = array_slice($users, $offset, $pageSize);
$from = $total > 0 ? $offset + 1 : 0;
$to   = min($offset + $pageSize, $total);
$queryParams = $_GET; unset($queryParams['page']);
$baseQuery = http_build_query($queryParams);
$baseQuery = $baseQuery ? $baseQuery . '&' : '';
?>

<div class="breadcrumb-nav">
    <a href="?url=dashboard/index">Dashboard</a> / <a href="?url=admin/index">Admin</a> / User Management
</div>

<div class="list-page-header">
    <h2><i class="fas fa-user-tie"></i> User Management</h2>
    <div class="list-header-actions">
        <a href="?url=admin/addUser" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add New User</a>
    </div>
</div>

<?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo e($_GET['msg']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form method="GET" action="">
    <input type="hidden" name="url" value="admin/users">
    <div class="filter-bar">
        <div class="filter-controls">
            <input type="text" name="search" class="filter-search" placeholder="Search by username or email..." value="<?php echo e($search); ?>">
            <select name="role" class="filter-select" onchange="this.form.submit()">
                <option value="">All Roles</option>
                <?php foreach($allRoles as $r): ?><option value="<?php echo e($r); ?>" <?php echo $roleFilter===$r?'selected':''; ?>><?php echo e($r); ?></option><?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-search"></i> Search</button>
            <?php if($search !== '' || $roleFilter !== ''): ?><a href="?url=admin/users" class="btn btn-outline-secondary btn-sm">Clear</a><?php endif; ?>
        </div>
    </div>
</form>

<?php if($search !== '' || $roleFilter !== ''): ?>
<div class="active-filters-bar">
    <span class="active-filters-label">Active Filters:</span>
    <?php if($search !== ''): ?><span class="filter-chip">Search: <?php echo e($search); ?> <a href="?url=admin/users&<?php echo http_build_query(array_merge($_GET,['search'=>'','url'=>'admin/users'])); ?>" class="filter-chip-remove">×</a></span><?php endif; ?>
    <?php if($roleFilter !== ''): ?><span class="filter-chip">Role: <?php echo e($roleFilter); ?> <a href="?url=admin/users&<?php echo http_build_query(array_merge($_GET,['role'=>'','url'=>'admin/users'])); ?>" class="filter-chip-remove">×</a></span><?php endif; ?>
    <a href="?url=admin/users" class="clear-all-btn">Clear All</a>
</div>
<?php endif; ?>

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
                    <th class="th-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if($pageRows): ?>
                <?php foreach($pageRows as $i => $user): ?>
                <tr>
                    <td class="td-sl"><?php echo $offset + $i + 1; ?></td>
                    <td><strong><?php echo e($user['Username']); ?></strong></td>
                    <td><?php echo e($user['Email']); ?></td>
                    <td>
                        <?php
                        $r = $user['Role'] ?? '';
                        $cls = 'status-default';
                        if ($r === 'Admin') $cls = 'status-rejected';
                        elseif ($r === 'Manager') $cls = 'status-pending';
                        else $cls = 'status-assigned';
                        ?>
                        <span class="status-badge <?php echo $cls; ?>"><?php echo e($r); ?></span>
                    </td>
                    <td class="td-actions">
                        <a href="?url=admin/editUser/<?php echo e($user['User_ID']); ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center py-4 text-muted">No users found</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="list-table-footer">
        <div class="page-size-control">Page Size:
            <select onchange="window.location='?url=admin/users&<?php echo http_build_query(array_diff_key($_GET,['page_size'=>'','page'=>'','url'=>''])); ?>&page_size='+this.value">
                <?php foreach([10,20,50,100] as $ps): ?><option value="<?php echo $ps; ?>" <?php echo $pageSize==$ps?'selected':''; ?>><?php echo $ps; ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="record-range"><?php echo $from; ?> to <?php echo $to; ?> of <?php echo $total; ?></div>
        <div class="list-pagination">
            <a href="?url=admin/users&<?php echo $baseQuery; ?>page=1" class="<?php echo $page<=1?'disabled':''; ?>">«</a>
            <a href="?url=admin/users&<?php echo $baseQuery; ?>page=<?php echo max(1,$page-1); ?>" class="<?php echo $page<=1?'disabled':''; ?>">‹</a>
            <?php for($p=max(1,$page-2);$p<=min($totalPages,$page+2);$p++): ?><a href="?url=admin/users&<?php echo $baseQuery; ?>page=<?php echo $p; ?>" class="<?php echo $p==$page?'active':''; ?>"><?php echo $p; ?></a><?php endfor; ?>
            <a href="?url=admin/users&<?php echo $baseQuery; ?>page=<?php echo min($totalPages,$page+1); ?>" class="<?php echo $page>=$totalPages?'disabled':''; ?>">›</a>
            <a href="?url=admin/users&<?php echo $baseQuery; ?>page=<?php echo $totalPages; ?>" class="<?php echo $page>=$totalPages?'disabled':''; ?>">»</a>
        </div>
    </div>
</div>
