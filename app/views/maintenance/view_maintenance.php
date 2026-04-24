<?php
// Session already started in layout
$maintenance = isset($data['maintenance']) && is_array($data['maintenance']) ? $data['maintenance'] : [];

$search       = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');

if ($search !== '' || $statusFilter !== '') {
    $maintenance = array_filter($maintenance, function($m) use ($search, $statusFilter) {
        $matchSearch = $search === '' ||
            stripos($m['Asset_Name'] ?? '', $search) !== false ||
            stripos((string)$m['Maintenance_ID'], $search) !== false;
        $matchStatus = $statusFilter === '' || ($m['Status'] ?? '') === $statusFilter;
        return $matchSearch && $matchStatus;
    });
}
$maintenance = array_values($maintenance);
$total = count($maintenance);

$allStatuses = array_unique(array_filter(array_column(isset($data['maintenance'])?$data['maintenance']:[], 'Status')));
sort($allStatuses);

$pageSize   = max(1, (int)($_GET['page_size'] ?? 20));
$totalPages = $total > 0 ? (int)ceil($total / $pageSize) : 1;
$page       = max(1, min((int)($_GET['page'] ?? 1), $totalPages));
$offset     = ($page - 1) * $pageSize;
$pageRows   = array_slice($maintenance, $offset, $pageSize);
$from = $total > 0 ? $offset + 1 : 0;
$to   = min($offset + $pageSize, $total);
$queryParams = $_GET; unset($queryParams['page']);
$baseQuery = http_build_query($queryParams);
$baseQuery = $baseQuery ? $baseQuery . '&' : '';
?>

<div class="list-page-header">
    <h2><i class="fas fa-wrench"></i> Maintenance Records</h2>
    <div class="list-header-actions">
        <a href="?url=maintenance/add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add Maintenance</a>
    </div>
</div>

<?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo e($_GET['msg']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form method="GET" action="">
    <input type="hidden" name="url" value="maintenance/index">
    <div class="filter-bar">
        <div class="filter-controls">
            <input type="text" name="search" class="filter-search" placeholder="Search by asset name, ID..." value="<?php echo e($search); ?>">
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <?php foreach($allStatuses as $st): ?><option value="<?php echo e($st); ?>" <?php echo $statusFilter===$st?'selected':''; ?>><?php echo e($st); ?></option><?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-search"></i> Search</button>
            <?php if($search !== '' || $statusFilter !== ''): ?><a href="?url=maintenance/index" class="btn btn-outline-secondary btn-sm">Clear</a><?php endif; ?>
        </div>
    </div>
</form>

<?php if($search !== '' || $statusFilter !== ''): ?>
<div class="active-filters-bar">
    <span class="active-filters-label">Active Filters:</span>
    <?php if($search !== ''): ?><span class="filter-chip">Search: <?php echo e($search); ?> <a href="?url=maintenance/index&<?php echo http_build_query(array_merge($_GET,['search'=>'','url'=>'maintenance/index'])); ?>" class="filter-chip-remove">×</a></span><?php endif; ?>
    <?php if($statusFilter !== ''): ?><span class="filter-chip">Status: <?php echo e($statusFilter); ?> <a href="?url=maintenance/index&<?php echo http_build_query(array_merge($_GET,['status'=>'','url'=>'maintenance/index'])); ?>" class="filter-chip-remove">×</a></span><?php endif; ?>
    <a href="?url=maintenance/index" class="clear-all-btn">Clear All</a>
</div>
<?php endif; ?>

<div class="result-count"><?php echo $total; ?> record(s) found</div>

<div class="list-table-container">
    <div class="list-table-wrapper">
        <table class="list-table" id="maintenanceTable">
            <thead>
                <tr>
                    <th class="th-checkbox"><input type="checkbox" class="bulk-checkbox" id="selectAll"></th>
                    <th class="th-sl">SL</th>
                    <th>Maintenance ID</th>
                    <th>Asset</th>
                    <th>Reported Date</th>
                    <th>Resolved Date</th>
                    <th>Cost</th>
                    <th>Status</th>
                    <th class="th-actions" style="width:220px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if($pageRows): ?>
                <?php foreach($pageRows as $i => $maint): ?>
                <tr>
                    <td class="td-checkbox"><input type="checkbox" class="bulk-checkbox row-checkbox" value="<?php echo e($maint['Maintenance_ID']); ?>"></td>
                    <td class="td-sl"><?php echo $offset + $i + 1; ?></td>
                    <td><strong><?php echo e($maint['Maintenance_ID']); ?></strong></td>
                    <td><?php echo e($maint['Asset_Name'] ?? '—'); ?></td>
                    <td><?php echo e($maint['Reported_Date']); ?></td>
                    <td><?php echo !empty($maint['Repair_End_Date']) ? e($maint['Repair_End_Date']) : '—'; ?></td>
                    <td><?php echo $maint['Cost'] ? 'BDT ' . number_format($maint['Cost'], 2) : '—'; ?></td>
                    <td>
                        <?php
                        $ms = strtolower($maint['Status'] ?? '');
                        $cls = 'status-default';
                        if ($ms === 'pending') $cls = 'status-pending';
                        elseif ($ms === 'completed' || $ms === 'resolved') $cls = 'status-completed';
                        elseif (strpos($ms,'progress') !== false) $cls = 'status-assigned';
                        ?>
                        <span class="status-badge <?php echo $cls; ?>"><?php echo e($maint['Status']); ?></span>
                    </td>
                    <td class="td-actions">
                        <button type="button" class="btn-status"
                            onclick="openStatusModal(
                                '<?php echo e($maint['Maintenance_ID']); ?>',
                                '<?php echo addslashes(e($maint['Asset_Name'] ?? 'Record #'.$maint['Maintenance_ID'])); ?>',
                                '<?php echo e($maint['Status']); ?>'
                            )">
                            <i class="fas fa-sync-alt"></i> Status
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9" class="text-center py-4 text-muted">No maintenance records found</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="list-table-footer">
        <div class="page-size-control">Page Size:
            <select onchange="window.location='?url=maintenance/index&<?php echo http_build_query(array_diff_key($_GET,['page_size'=>'','page'=>'','url'=>''])); ?>&page_size='+this.value">
                <?php foreach([10,20,50,100] as $ps): ?><option value="<?php echo $ps; ?>" <?php echo $pageSize==$ps?'selected':''; ?>><?php echo $ps; ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="record-range"><?php echo $from; ?> to <?php echo $to; ?> of <?php echo $total; ?></div>
        <div class="list-pagination">
            <a href="?url=maintenance/index&<?php echo $baseQuery; ?>page=1" class="<?php echo $page<=1?'disabled':''; ?>">«</a>
            <a href="?url=maintenance/index&<?php echo $baseQuery; ?>page=<?php echo max(1,$page-1); ?>" class="<?php echo $page<=1?'disabled':''; ?>">‹</a>
            <?php for($p=max(1,$page-2);$p<=min($totalPages,$page+2);$p++): ?><a href="?url=maintenance/index&<?php echo $baseQuery; ?>page=<?php echo $p; ?>" class="<?php echo $p==$page?'active':''; ?>"><?php echo $p; ?></a><?php endfor; ?>
            <a href="?url=maintenance/index&<?php echo $baseQuery; ?>page=<?php echo min($totalPages,$page+1); ?>" class="<?php echo $page>=$totalPages?'disabled':''; ?>">›</a>
            <a href="?url=maintenance/index&<?php echo $baseQuery; ?>page=<?php echo $totalPages; ?>" class="<?php echo $page>=$totalPages?'disabled':''; ?>">»</a>
        </div>
    </div>
</div>
<script>
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
});
</script>

<!-- ============================================================
     STATUS UPDATE MODAL
     ============================================================ -->
<div class="modal fade ims-modal" id="maintenanceStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">
                    <i class="fas fa-sync-alt text-primary"></i> Update Maintenance Status
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="modal-record-info" id="modal_record_info"></div>
                <form id="maintenanceStatusForm" method="POST" action="?url=maintenance/updateStatus">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="maintenance_id" id="modal_maint_id">
                    <div class="mb-3">
                        <label class="form-label">New Status</label>
                        <select name="status" id="modal_status" class="form-select form-select-sm" required>
                            <option value="">— Select Status —</option>
                            <option value="Pending">⏳ Pending</option>
                            <option value="In Progress">🔧 In Progress</option>
                            <option value="Approved">✅ Approved</option>
                            <option value="Rejected">❌ Rejected</option>
                            <option value="Completed">✔️ Completed</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">
                            End / Resolved Date
                            <span class="text-muted fw-normal" style="font-size:0.9em;">(optional)</span>
                        </label>
                        <input type="date" name="end_date" id="modal_end_date" class="form-control form-control-sm">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="maintenanceStatusForm" name="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-check me-1"></i>Update Status
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     DELETE CONFIRMATION MODAL
     ============================================================ -->
<div class="modal fade ims-confirm-modal" id="maintenanceDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:360px;">
        <div class="modal-content" style="border:none; border-radius:12px; box-shadow:0 20px 60px rgba(0,0,0,0.15); overflow:hidden;">
            <div class="modal-body">
                <div class="modal-confirm-icon" style="color:#dc2626;">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <div class="modal-confirm-title">Delete Maintenance Record?</div>
                <p class="modal-confirm-msg" id="deleteModalMessage"></p>
                <form id="maintenanceDeleteForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-confirm-actions">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash-alt me-1"></i>Delete
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openStatusModal(id, assetName, currentStatus) {
    document.getElementById('modal_maint_id').value = id;
    document.getElementById('modal_record_info').innerHTML =
        '<strong>Asset:</strong> ' + assetName +
        '&nbsp;&nbsp;|&nbsp;&nbsp;<strong>ID:</strong> #' + id +
        '&nbsp;&nbsp;|&nbsp;&nbsp;<strong>Current:</strong> ' + currentStatus;
    var sel = document.getElementById('modal_status');
    sel.value = currentStatus;
    document.getElementById('modal_end_date').value = '';
    new bootstrap.Modal(document.getElementById('maintenanceStatusModal')).show();
}

function openDeleteModal(id, assetName) {
    // delete removed from UI - kept for reference only
}
</script>
