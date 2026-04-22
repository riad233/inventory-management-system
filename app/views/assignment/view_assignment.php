<?php
// Session already started in layout
$assignments = isset($data['assignments']) && is_array($data['assignments']) ? $data['assignments'] : [];

$search       = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');

if ($search !== '' || $statusFilter !== '') {
    $assignments = array_filter($assignments, function($a) use ($search, $statusFilter) {
        $returnStatus = $a['Actual_Return_Date'] ? 'Returned' : 'Assigned';
        $matchSearch = $search === '' ||
            stripos($a['Asset_Name'] ?? '', $search) !== false ||
            stripos($a['Name'] ?? '', $search) !== false ||
            stripos((string)$a['Assignment_ID'], $search) !== false;
        $matchStatus = $statusFilter === '' || $returnStatus === $statusFilter;
        return $matchSearch && $matchStatus;
    });
}
$assignments = array_values($assignments);
$total = count($assignments);

$pageSize   = max(1, (int)($_GET['page_size'] ?? 20));
$totalPages = $total > 0 ? (int)ceil($total / $pageSize) : 1;
$page       = max(1, min((int)($_GET['page'] ?? 1), $totalPages));
$offset     = ($page - 1) * $pageSize;
$pageRows   = array_slice($assignments, $offset, $pageSize);
$from = $total > 0 ? $offset + 1 : 0;
$to   = min($offset + $pageSize, $total);
$queryParams = $_GET; unset($queryParams['page']);
$baseQuery = http_build_query($queryParams);
$baseQuery = $baseQuery ? $baseQuery . '&' : '';
?>

<div class="list-page-header">
    <h2><i class="fas fa-exchange-alt"></i> Assignments</h2>
    <div class="list-header-actions">
        <a href="?url=assignment/assign" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Assign Asset</a>
        <a href="?url=assignment/returnAsset" class="btn btn-outline-secondary btn-sm"><i class="fas fa-undo"></i> Return Asset</a>
    </div>
</div>

<?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo e($_GET['msg']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form method="GET" action="">
    <input type="hidden" name="url" value="assignment/index">
    <div class="filter-bar">
        <div class="filter-controls">
            <input type="text" name="search" class="filter-search" placeholder="Search by asset, employee, ID..." value="<?php echo e($search); ?>">
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="Assigned" <?php echo $statusFilter==='Assigned'?'selected':''; ?>>Assigned</option>
                <option value="Returned" <?php echo $statusFilter==='Returned'?'selected':''; ?>>Returned</option>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-search"></i> Search</button>
            <?php if($search !== '' || $statusFilter !== ''): ?><a href="?url=assignment/index" class="btn btn-outline-secondary btn-sm">Clear</a><?php endif; ?>
        </div>
    </div>
</form>

<?php if($search !== '' || $statusFilter !== ''): ?>
<div class="active-filters-bar">
    <span class="active-filters-label">Active Filters:</span>
    <?php if($search !== ''): ?><span class="filter-chip">Search: <?php echo e($search); ?> <a href="?url=assignment/index&<?php echo http_build_query(array_merge($_GET,['search'=>'','url'=>'assignment/index'])); ?>" class="filter-chip-remove">×</a></span><?php endif; ?>
    <?php if($statusFilter !== ''): ?><span class="filter-chip">Status: <?php echo e($statusFilter); ?> <a href="?url=assignment/index&<?php echo http_build_query(array_merge($_GET,['status'=>'','url'=>'assignment/index'])); ?>" class="filter-chip-remove">×</a></span><?php endif; ?>
    <a href="?url=assignment/index" class="clear-all-btn">Clear All</a>
</div>
<?php endif; ?>

<div class="result-count"><?php echo $total; ?> assignment(s) found</div>

<div class="list-table-container">
    <div class="list-table-wrapper">
        <table class="list-table" id="assignmentTable">
            <thead>
                <tr>
                    <th class="th-checkbox"><input type="checkbox" class="bulk-checkbox" id="selectAll"></th>
                    <th class="th-sl">SL</th>
                    <th>Assignment ID</th>
                    <th>Asset</th>
                    <th>Employee</th>
                    <th>Assigned Date</th>
                    <th>Expected Return</th>
                    <th>Actual Return</th>
                    <th>Status</th>
                    <th class="th-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if($pageRows): ?>
                <?php foreach($pageRows as $i => $assign): ?>
                <tr>
                    <td class="td-checkbox"><input type="checkbox" class="bulk-checkbox row-checkbox" value="<?php echo e($assign['Assignment_ID']); ?>"></td>
                    <td class="td-sl"><?php echo $offset + $i + 1; ?></td>
                    <td><strong><?php echo e($assign['Assignment_ID']); ?></strong></td>
                    <td><?php echo e($assign['Asset_Name'] ?? '—'); ?></td>
                    <td><?php echo e($assign['Name'] ?? '—'); ?></td>
                    <td><?php echo e($assign['Assigned_Date']); ?></td>
                    <td><?php echo e($assign['Expected_Return_Date']); ?></td>
                    <td><?php echo $assign['Actual_Return_Date'] ? e($assign['Actual_Return_Date']) : '—'; ?></td>
                    <td>
                        <?php if($assign['Actual_Return_Date']): ?>
                            <span class="status-badge status-returned">Returned</span>
                        <?php else: ?>
                            <span class="status-badge status-assigned">Assigned</span>
                        <?php endif; ?>
                    </td>
                    <td class="td-actions">
                        <a href="?url=assignment/edit/<?php echo e($assign['Assignment_ID']); ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="10" class="text-center py-4 text-muted">No assignments found</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="list-table-footer">
        <div class="page-size-control">Page Size:
            <select onchange="window.location='?url=assignment/index&<?php echo http_build_query(array_diff_key($_GET,['page_size'=>'','page'=>'','url'=>''])); ?>&page_size='+this.value">
                <?php foreach([10,20,50,100] as $ps): ?><option value="<?php echo $ps; ?>" <?php echo $pageSize==$ps?'selected':''; ?>><?php echo $ps; ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="record-range"><?php echo $from; ?> to <?php echo $to; ?> of <?php echo $total; ?></div>
        <div class="list-pagination">
            <a href="?url=assignment/index&<?php echo $baseQuery; ?>page=1" class="<?php echo $page<=1?'disabled':''; ?>">«</a>
            <a href="?url=assignment/index&<?php echo $baseQuery; ?>page=<?php echo max(1,$page-1); ?>" class="<?php echo $page<=1?'disabled':''; ?>">‹</a>
            <?php for($p=max(1,$page-2);$p<=min($totalPages,$page+2);$p++): ?><a href="?url=assignment/index&<?php echo $baseQuery; ?>page=<?php echo $p; ?>" class="<?php echo $p==$page?'active':''; ?>"><?php echo $p; ?></a><?php endfor; ?>
            <a href="?url=assignment/index&<?php echo $baseQuery; ?>page=<?php echo min($totalPages,$page+1); ?>" class="<?php echo $page>=$totalPages?'disabled':''; ?>">›</a>
            <a href="?url=assignment/index&<?php echo $baseQuery; ?>page=<?php echo $totalPages; ?>" class="<?php echo $page>=$totalPages?'disabled':''; ?>">»</a>
        </div>
    </div>
</div>
<script>
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
});
</script>
