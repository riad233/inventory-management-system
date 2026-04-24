<?php
// Session already started in layout
$employees = isset($data['employees']) && is_array($data['employees']) ? $data['employees'] : [];

$search     = trim($_GET['search'] ?? '');
$deptFilter = trim($_GET['department'] ?? '');
$desigFilter= trim($_GET['designation'] ?? '');

if ($search !== '' || $deptFilter !== '' || $desigFilter !== '') {
    $employees = array_filter($employees, function($e) use ($search, $deptFilter, $desigFilter) {
        $matchSearch = $search === '' ||
            stripos($e['Name'], $search) !== false ||
            stripos($e['Email'], $search) !== false ||
            stripos($e['Contact_Number'] ?? '', $search) !== false;
        $matchDept  = $deptFilter === '' || ($e['Department_Name'] ?? '') === $deptFilter;
        $matchDesig = $desigFilter === '' || ($e['Designation'] ?? '') === $desigFilter;
        return $matchSearch && $matchDept && $matchDesig;
    });
}
$employees = array_values($employees);
$total     = count($employees);

$allDepts  = array_unique(array_filter(array_column(isset($data['employees']) ? $data['employees'] : [], 'Department_Name')));
$allDesigs = array_unique(array_filter(array_column(isset($data['employees']) ? $data['employees'] : [], 'Designation')));
sort($allDepts); sort($allDesigs);

$pageSize   = max(1, (int)($_GET['page_size'] ?? 20));
$totalPages = $total > 0 ? (int)ceil($total / $pageSize) : 1;
$page       = max(1, min((int)($_GET['page'] ?? 1), $totalPages));
$offset     = ($page - 1) * $pageSize;
$pageRows   = array_slice($employees, $offset, $pageSize);
$from = $total > 0 ? $offset + 1 : 0;
$to   = min($offset + $pageSize, $total);
$queryParams = $_GET; unset($queryParams['page']);
$baseQuery = http_build_query($queryParams);
$baseQuery = $baseQuery ? $baseQuery . '&' : '';
?>

<div class="list-page-header">
    <h2><i class="fas fa-users"></i> Employees</h2>
    <div class="list-header-actions">
        <a href="?url=employee/add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Employee</a>
    </div>
</div>

<?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo e($_GET['msg']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form method="GET" action="">
    <input type="hidden" name="url" value="employee/index">
    <div class="filter-bar">
        <div class="filter-controls">
            <input type="text" name="search" class="filter-search" placeholder="Search by name, email, contact..." value="<?php echo e($search); ?>">
            <select name="department" class="filter-select" onchange="this.form.submit()">
                <option value="">All Departments</option>
                <?php foreach($allDepts as $d): ?>
                    <option value="<?php echo e($d); ?>" <?php echo $deptFilter===$d?'selected':''; ?>><?php echo e($d); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="designation" class="filter-select" onchange="this.form.submit()">
                <option value="">All Designations</option>
                <?php foreach($allDesigs as $dg): ?>
                    <option value="<?php echo e($dg); ?>" <?php echo $desigFilter===$dg?'selected':''; ?>><?php echo e($dg); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-search"></i> Search</button>
            <?php if($search !== '' || $deptFilter !== '' || $desigFilter !== ''): ?>
                <a href="?url=employee/index" class="btn btn-outline-secondary btn-sm">Clear</a>
            <?php endif; ?>
        </div>
    </div>
</form>

<?php if($search !== '' || $deptFilter !== '' || $desigFilter !== ''): ?>
<div class="active-filters-bar">
    <span class="active-filters-label">Active Filters:</span>
    <?php if($search !== ''): ?><span class="filter-chip">Search: <?php echo e($search); ?> <a href="?url=employee/index&<?php echo http_build_query(array_merge($_GET,['search'=>'','url'=>'employee/index'])); ?>" class="filter-chip-remove">×</a></span><?php endif; ?>
    <?php if($deptFilter !== ''): ?><span class="filter-chip">Dept: <?php echo e($deptFilter); ?> <a href="?url=employee/index&<?php echo http_build_query(array_merge($_GET,['department'=>'','url'=>'employee/index'])); ?>" class="filter-chip-remove">×</a></span><?php endif; ?>
    <?php if($desigFilter !== ''): ?><span class="filter-chip">Designation: <?php echo e($desigFilter); ?> <a href="?url=employee/index&<?php echo http_build_query(array_merge($_GET,['designation'=>'','url'=>'employee/index'])); ?>" class="filter-chip-remove">×</a></span><?php endif; ?>
    <a href="?url=employee/index" class="clear-all-btn">Clear All</a>
</div>
<?php endif; ?>

<div class="result-count"><?php echo $total; ?> employee(s) found</div>

<div class="list-table-container">
    <div class="list-table-wrapper">
        <table class="list-table" id="employeeTable">
            <thead>
                <tr>
                    <th class="th-checkbox"><input type="checkbox" class="bulk-checkbox" id="selectAll"></th>
                    <th class="th-sl">SL</th>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Department</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th class="th-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if($pageRows): ?>
                <?php foreach($pageRows as $i => $emp): ?>
                <tr>
                    <td class="td-checkbox"><input type="checkbox" class="bulk-checkbox row-checkbox" value="<?php echo e($emp['User_ID']); ?>"></td>
                    <td class="td-sl"><?php echo $offset + $i + 1; ?></td>
                    <td><strong><?php echo e($emp['User_ID']); ?></strong></td>
                    <td><?php echo e($emp['Name']); ?></td>
                    <td><?php echo e($emp['Designation']); ?></td>
                    <td><?php echo e($emp['Department_Name'] ?? '—'); ?></td>
                    <td><?php echo e($emp['Contact_Number']); ?></td>
                    <td><?php echo e($emp['Email']); ?></td>
                    <td class="td-actions">
                        <a href="?url=employee/edit/<?php echo e($emp['User_ID']); ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9" class="text-center py-4 text-muted">No employees found</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="list-table-footer">
        <div class="page-size-control">Page Size:
            <select onchange="window.location='?url=employee/index&<?php echo http_build_query(array_diff_key($_GET,['page_size'=>'','page'=>'','url'=>''])); ?>&page_size='+this.value">
                <?php foreach([10,20,50,100] as $ps): ?><option value="<?php echo $ps; ?>" <?php echo $pageSize==$ps?'selected':''; ?>><?php echo $ps; ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="record-range"><?php echo $from; ?> to <?php echo $to; ?> of <?php echo $total; ?></div>
        <div class="list-pagination">
            <a href="?url=employee/index&<?php echo $baseQuery; ?>page=1" class="<?php echo $page<=1?'disabled':''; ?>">«</a>
            <a href="?url=employee/index&<?php echo $baseQuery; ?>page=<?php echo max(1,$page-1); ?>" class="<?php echo $page<=1?'disabled':''; ?>">‹</a>
            <?php for($p=max(1,$page-2);$p<=min($totalPages,$page+2);$p++): ?>
                <a href="?url=employee/index&<?php echo $baseQuery; ?>page=<?php echo $p; ?>" class="<?php echo $p==$page?'active':''; ?>"><?php echo $p; ?></a>
            <?php endfor; ?>
            <a href="?url=employee/index&<?php echo $baseQuery; ?>page=<?php echo min($totalPages,$page+1); ?>" class="<?php echo $page>=$totalPages?'disabled':''; ?>">›</a>
            <a href="?url=employee/index&<?php echo $baseQuery; ?>page=<?php echo $totalPages; ?>" class="<?php echo $page>=$totalPages?'disabled':''; ?>">»</a>
        </div>
    </div>
</div>

<script>
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
});
</script>
