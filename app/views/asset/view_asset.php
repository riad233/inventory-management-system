<?php
// Session already started in layout
$assets = isset($data['assets']) && is_array($data['assets']) ? $data['assets'] : [];

// Filter logic
$search  = trim($_GET['search'] ?? '');
$catFilter    = trim($_GET['category'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');

if ($search !== '' || $catFilter !== '' || $statusFilter !== '') {
    $assets = array_filter($assets, function($a) use ($search, $catFilter, $statusFilter) {
        $matchSearch = $search === '' ||
            stripos($a['Asset_Name'], $search) !== false ||
            stripos($a['Asset_ID'], $search) !== false ||
            stripos($a['Serial_Number'] ?? '', $search) !== false;
        $matchCat    = $catFilter === '' || $a['Category'] === $catFilter;
        $matchStatus = $statusFilter === '' || $a['Status'] === $statusFilter;
        return $matchSearch && $matchCat && $matchStatus;
    });
}
$assets = array_values($assets);
$total  = count($assets);

// Collect unique categories and statuses for dropdowns
$allCategories = array_unique(array_column(isset($data['assets']) ? $data['assets'] : [], 'Category'));
$allStatuses   = array_unique(array_column(isset($data['assets']) ? $data['assets'] : [], 'Status'));
sort($allCategories); sort($allStatuses);

// Pagination
$pageSize   = max(1, (int)($_GET['page_size'] ?? 20));
$totalPages = $total > 0 ? (int)ceil($total / $pageSize) : 1;
$page       = max(1, min((int)($_GET['page'] ?? 1), $totalPages));
$offset     = ($page - 1) * $pageSize;
$pageAssets = array_slice($assets, $offset, $pageSize);
$from       = $total > 0 ? $offset + 1 : 0;
$to         = min($offset + $pageSize, $total);

// Build base query string without page
$queryParams = $_GET;
unset($queryParams['page']);
$baseQuery = http_build_query($queryParams);
$baseQuery = $baseQuery ? $baseQuery . '&' : '';
?>

<!-- Page header -->
<div class="list-page-header">
    <h2><i class="fas fa-cube"></i> Assets</h2>
    <div class="list-header-actions">
        <a href="?url=asset/add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Asset</a>
        <a href="?url=asset/index&export=csv<?php echo $baseQuery ? '&' . http_build_query(array_diff_key($_GET, ['export'=>''])) : ''; ?>" class="btn btn-success btn-sm"><i class="fas fa-file-csv"></i> Export CSV</a>
    </div>
</div>

<?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo e($_GET['msg']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Filter Bar -->
<form method="GET" action="" id="assetFilterForm">
    <input type="hidden" name="url" value="asset/index">
    <div class="filter-bar">
        <div class="filter-controls">
            <input type="text" name="search" class="filter-search" placeholder="Search by name, ID, serial..." value="<?php echo e($search); ?>">
            <select name="category" class="filter-select" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <?php foreach($allCategories as $cat): ?>
                    <option value="<?php echo e($cat); ?>" <?php echo $catFilter === $cat ? 'selected' : ''; ?>><?php echo e($cat); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <?php foreach($allStatuses as $st): ?>
                    <option value="<?php echo e($st); ?>" <?php echo $statusFilter === $st ? 'selected' : ''; ?>><?php echo e($st); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="page_size" class="filter-select" onchange="this.form.submit()" style="min-width:80px;">
                <?php foreach([10,20,50,100] as $ps): ?>
                    <option value="<?php echo $ps; ?>" <?php echo $pageSize == $ps ? 'selected' : ''; ?>>Show <?php echo $ps; ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-search"></i> Search</button>
            <?php if($search !== '' || $catFilter !== '' || $statusFilter !== ''): ?>
                <a href="?url=asset/index" class="btn btn-outline-secondary btn-sm">Clear</a>
            <?php endif; ?>
        </div>
    </div>
</form>

<!-- Active Filters -->
<?php if($search !== '' || $catFilter !== '' || $statusFilter !== ''): ?>
<div class="active-filters-bar">
    <span class="active-filters-label">Active Filters:</span>
    <?php if($search !== ''): ?>
        <span class="filter-chip">Search: <?php echo e($search); ?> <a href="?url=asset/index&<?php echo http_build_query(array_merge($_GET, ['search'=>'', 'url'=>'asset/index'])); ?>" class="filter-chip-remove">×</a></span>
    <?php endif; ?>
    <?php if($catFilter !== ''): ?>
        <span class="filter-chip">Category: <?php echo e($catFilter); ?> <a href="?url=asset/index&<?php echo http_build_query(array_merge($_GET, ['category'=>'', 'url'=>'asset/index'])); ?>" class="filter-chip-remove">×</a></span>
    <?php endif; ?>
    <?php if($statusFilter !== ''): ?>
        <span class="filter-chip">Status: <?php echo e($statusFilter); ?> <a href="?url=asset/index&<?php echo http_build_query(array_merge($_GET, ['status'=>'', 'url'=>'asset/index'])); ?>" class="filter-chip-remove">×</a></span>
    <?php endif; ?>
    <a href="?url=asset/index" class="clear-all-btn">Clear All</a>
</div>
<?php endif; ?>

<!-- Result Count -->
<div class="result-count"><?php echo $total; ?> asset(s) found</div>

<!-- Table -->
<div class="list-table-container">
    <div class="list-table-wrapper">
        <table class="list-table" id="assetTable">
            <thead>
                <tr>
                    <th class="th-checkbox"><input type="checkbox" class="bulk-checkbox" id="selectAll" title="Select All"></th>
                    <th class="th-sl">SL</th>
                    <th>Asset ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Serial #</th>
                    <th>Status</th>
                    <th class="th-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if($pageAssets): ?>
                <?php foreach($pageAssets as $i => $asset): ?>
                <tr>
                    <td class="td-checkbox"><input type="checkbox" class="bulk-checkbox row-checkbox" value="<?php echo e($asset['Asset_ID']); ?>"></td>
                    <td class="td-sl"><?php echo $offset + $i + 1; ?></td>
                    <td><strong><?php echo e($asset['Asset_ID']); ?></strong></td>
                    <td><?php echo e($asset['Asset_Name']); ?></td>
                    <td><?php echo e($asset['Category']); ?></td>
                    <td><?php echo e($asset['Brand']); ?></td>
                    <td><?php echo e($asset['Model'] ?? '—'); ?></td>
                    <td><?php echo e($asset['Serial_Number']); ?></td>
                    <td>
                        <?php
                        $s = strtolower($asset['Status'] ?? '');
                        $cls = 'status-default';
                        if ($s === 'active') $cls = 'status-active';
                        elseif ($s === 'assigned') $cls = 'status-assigned';
                        elseif ($s === 'inactive') $cls = 'status-inactive';
                        elseif (strpos($s,'maintenance') !== false) $cls = 'status-under-maintenance';
                        ?>
                        <span class="status-badge <?php echo $cls; ?>"><?php echo e($asset['Status']); ?></span>
                    </td>
                    <td class="td-actions">
                        <a href="?url=asset/edit/<?php echo e($asset['Asset_ID']); ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="10" class="text-center py-4 text-muted">No assets found</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Table Footer: Pagination -->
    <div class="list-table-footer">
        <div class="page-size-control">
            Page Size:
            <select onchange="window.location='?url=asset/index&<?php echo http_build_query(array_diff_key($_GET, ['page_size'=>'','page'=>'','url'=>''])); ?>&page_size='+this.value">
                <?php foreach([10,20,50,100] as $ps): ?>
                    <option value="<?php echo $ps; ?>" <?php echo $pageSize == $ps ? 'selected' : ''; ?>><?php echo $ps; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="record-range"><?php echo $from; ?> to <?php echo $to; ?> of <?php echo $total; ?></div>
        <div class="list-pagination">
            <a href="?url=asset/index&<?php echo $baseQuery; ?>page=1" class="<?php echo $page<=1?'disabled':''; ?>" title="First">«</a>
            <a href="?url=asset/index&<?php echo $baseQuery; ?>page=<?php echo max(1,$page-1); ?>" class="<?php echo $page<=1?'disabled':''; ?>" title="Prev">‹</a>
            <?php for($p=max(1,$page-2); $p<=min($totalPages,$page+2); $p++): ?>
                <a href="?url=asset/index&<?php echo $baseQuery; ?>page=<?php echo $p; ?>" class="<?php echo $p==$page?'active':''; ?>"><?php echo $p; ?></a>
            <?php endfor; ?>
            <a href="?url=asset/index&<?php echo $baseQuery; ?>page=<?php echo min($totalPages,$page+1); ?>" class="<?php echo $page>=$totalPages?'disabled':''; ?>" title="Next">›</a>
            <a href="?url=asset/index&<?php echo $baseQuery; ?>page=<?php echo $totalPages; ?>" class="<?php echo $page>=$totalPages?'disabled':''; ?>" title="Last">»</a>
        </div>
    </div>
</div>

<script>
// Bulk select all
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
});
</script>
