<?php
// Session already started in layout
$requests = isset($data['requests']) && is_array($data['requests']) ? $data['requests'] : [];

$search       = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');
$typeFilter   = trim($_GET['type'] ?? '');

if ($search !== '' || $statusFilter !== '' || $typeFilter !== '') {
    $requests = array_filter($requests, function($r) use ($search, $statusFilter, $typeFilter) {
        $matchSearch = $search === '' ||
            stripos($r['Name'] ?? '', $search) !== false ||
            stripos((string)$r['Request_ID'], $search) !== false;
        $matchStatus = $statusFilter === '' || ($r['Status'] ?? '') === $statusFilter;
        $matchType   = $typeFilter === '' || ($r['Equipment_Type'] ?? '') === $typeFilter;
        return $matchSearch && $matchStatus && $matchType;
    });
}
$requests = array_values($requests);
$total = count($requests);

$allTypes    = array_unique(array_filter(array_column(isset($data['requests'])?$data['requests']:[], 'Equipment_Type')));
$allStatuses = array_unique(array_filter(array_column(isset($data['requests'])?$data['requests']:[], 'Status')));
sort($allTypes); sort($allStatuses);

$pageSize   = max(1, (int)($_GET['page_size'] ?? 20));
$totalPages = $total > 0 ? (int)ceil($total / $pageSize) : 1;
$page       = max(1, min((int)($_GET['page'] ?? 1), $totalPages));
$offset     = ($page - 1) * $pageSize;
$pageRows   = array_slice($requests, $offset, $pageSize);
$from = $total > 0 ? $offset + 1 : 0;
$to   = min($offset + $pageSize, $total);
$queryParams = $_GET; unset($queryParams['page']);
$baseQuery = http_build_query($queryParams);
$baseQuery = $baseQuery ? $baseQuery . '&' : '';
?>

<div class="list-page-header">
    <h2><i class="fas fa-clipboard-list"></i> Equipment Requests</h2>
    <div class="list-header-actions">
        <a href="?url=request/create" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> New Request</a>
    </div>
</div>

<?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo e($_GET['msg']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form method="GET" action="">
    <input type="hidden" name="url" value="request/index">
    <div class="filter-bar">
        <div class="filter-controls">
            <input type="text" name="search" class="filter-search" placeholder="Search by employee, request ID..." value="<?php echo e($search); ?>">
            <select name="type" class="filter-select" onchange="this.form.submit()">
                <option value="">All Types</option>
                <?php foreach($allTypes as $t): ?><option value="<?php echo e($t); ?>" <?php echo $typeFilter===$t?'selected':''; ?>><?php echo e($t); ?></option><?php endforeach; ?>
            </select>
            <select name="status" class="filter-select" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <?php foreach($allStatuses as $st): ?><option value="<?php echo e($st); ?>" <?php echo $statusFilter===$st?'selected':''; ?>><?php echo e($st); ?></option><?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-search"></i> Search</button>
            <?php if($search !== '' || $statusFilter !== '' || $typeFilter !== ''): ?><a href="?url=request/index" class="btn btn-outline-secondary btn-sm">Clear</a><?php endif; ?>
        </div>
    </div>
</form>

<?php if($search !== '' || $statusFilter !== '' || $typeFilter !== ''): ?>
<div class="active-filters-bar">
    <span class="active-filters-label">Active Filters:</span>
    <?php if($search !== ''): ?><span class="filter-chip">Search: <?php echo e($search); ?> <a href="?url=request/index&<?php echo http_build_query(array_merge($_GET,['search'=>'','url'=>'request/index'])); ?>" class="filter-chip-remove">×</a></span><?php endif; ?>
    <?php if($typeFilter !== ''): ?><span class="filter-chip">Type: <?php echo e($typeFilter); ?> <a href="?url=request/index&<?php echo http_build_query(array_merge($_GET,['type'=>'','url'=>'request/index'])); ?>" class="filter-chip-remove">×</a></span><?php endif; ?>
    <?php if($statusFilter !== ''): ?><span class="filter-chip">Status: <?php echo e($statusFilter); ?> <a href="?url=request/index&<?php echo http_build_query(array_merge($_GET,['status'=>'','url'=>'request/index'])); ?>" class="filter-chip-remove">×</a></span><?php endif; ?>
    <a href="?url=request/index" class="clear-all-btn">Clear All</a>
</div>
<?php endif; ?>

<div class="result-count"><?php echo $total; ?> request(s) found</div>

<div class="list-table-container">
    <div class="list-table-wrapper">
        <table class="list-table" id="requestTable">
            <thead>
                <tr>
                    <th class="th-checkbox"><input type="checkbox" class="bulk-checkbox" id="selectAll"></th>
                    <th class="th-sl">SL</th>
                    <th>Request ID</th>
                    <th>Employee</th>
                    <th>Equipment Type</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th class="th-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if($pageRows): ?>
                <?php foreach($pageRows as $i => $req): ?>
                <tr>
                    <td class="td-checkbox"><input type="checkbox" class="bulk-checkbox row-checkbox" value="<?php echo e($req['Request_ID']); ?>"></td>
                    <td class="td-sl"><?php echo $offset + $i + 1; ?></td>
                    <td><strong><?php echo e($req['Request_ID']); ?></strong></td>
                    <td><?php echo e($req['Name'] ?? '—'); ?></td>
                    <td><?php echo e($req['Equipment_Type']); ?></td>
                    <td><?php echo e($req['Request_Date']); ?></td>
                    <td>
                        <?php
                        $rs = $req['Status'] ?? '';
                        $cls = 'status-default';
                        if ($rs === 'Pending') $cls = 'status-pending';
                        elseif ($rs === 'Approved') $cls = 'status-approved';
                        elseif ($rs === 'Rejected') $cls = 'status-rejected';
                        ?>
                        <span class="status-badge <?php echo $cls; ?>"><?php echo e($rs); ?></span>
                    </td>
                    <td class="td-actions">
                        <?php if(($req['Status'] ?? '') === 'Pending'): ?>
                            <button type="button" class="btn-approve" onclick="openRequestModal('approve','<?php echo e($req['Request_ID']); ?>','<?php echo e(addslashes($req['Name'] ?? '')); ?>')"><i class="fas fa-check"></i> Approve</button>
                            <button type="button" class="btn-reject" onclick="openRequestModal('reject','<?php echo e($req['Request_ID']); ?>','<?php echo e(addslashes($req['Name'] ?? '')); ?>')"><i class="fas fa-times"></i> Reject</button>
                        <?php else: ?>
                            <button type="button" class="btn-status" onclick="openStatusUpdateModal('<?php echo e($req['Request_ID']); ?>','<?php echo e(addslashes($req['Status'] ?? '')); ?>','<?php echo e(addslashes($req['Name'] ?? '')); ?>')"><i class="fas fa-sync-alt"></i> Status</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" class="text-center py-4 text-muted">No requests found</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="list-table-footer">
        <div class="page-size-control">Page Size:
            <select onchange="window.location='?url=request/index&<?php echo http_build_query(array_diff_key($_GET,['page_size'=>'','page'=>'','url'=>''])); ?>&page_size='+this.value">
                <?php foreach([10,20,50,100] as $ps): ?><option value="<?php echo $ps; ?>" <?php echo $pageSize==$ps?'selected':''; ?>><?php echo $ps; ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="record-range"><?php echo $from; ?> to <?php echo $to; ?> of <?php echo $total; ?></div>
        <div class="list-pagination">
            <a href="?url=request/index&<?php echo $baseQuery; ?>page=1" class="<?php echo $page<=1?'disabled':''; ?>">«</a>
            <a href="?url=request/index&<?php echo $baseQuery; ?>page=<?php echo max(1,$page-1); ?>" class="<?php echo $page<=1?'disabled':''; ?>">‹</a>
            <?php for($p=max(1,$page-2);$p<=min($totalPages,$page+2);$p++): ?><a href="?url=request/index&<?php echo $baseQuery; ?>page=<?php echo $p; ?>" class="<?php echo $p==$page?'active':''; ?>"><?php echo $p; ?></a><?php endfor; ?>
            <a href="?url=request/index&<?php echo $baseQuery; ?>page=<?php echo min($totalPages,$page+1); ?>" class="<?php echo $page>=$totalPages?'disabled':''; ?>">›</a>
            <a href="?url=request/index&<?php echo $baseQuery; ?>page=<?php echo $totalPages; ?>" class="<?php echo $page>=$totalPages?'disabled':''; ?>">»</a>
        </div>
    </div>
</div>
<script>
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = this.checked);
});
function openRequestModal(action, id, name) {
    var isApprove = action === 'approve';
    document.getElementById('requestActionForm').action = '?url=request/' + action + '/' + id;
    document.getElementById('requestActionIcon').className = isApprove ? 'fas fa-check-circle' : 'fas fa-times-circle';
    document.getElementById('requestActionIcon').style.color = isApprove ? '#16a34a' : '#dc2626';
    document.getElementById('requestActionTitle').textContent = isApprove ? 'Approve Request?' : 'Reject Request?';
    document.getElementById('requestActionMsg').textContent = (isApprove ? 'Approve' : 'Reject') + ' request from "' + name + '"?';
    document.getElementById('requestActionBtn').className = isApprove ? 'btn btn-sm btn-success' : 'btn btn-sm btn-danger';
    document.getElementById('requestActionBtn').innerHTML = isApprove ? '<i class="fas fa-check me-1"></i>Approve' : '<i class="fas fa-times me-1"></i>Reject';
    new bootstrap.Modal(document.getElementById('requestActionModal')).show();
}
function openStatusUpdateModal(id, currentStatus, name) {
    document.getElementById('statusUpdateForm').action = '?url=request/updateStatus/' + id;
    document.getElementById('statusUpdateSelect').value = currentStatus;
    document.getElementById('statusUpdateEmployee').textContent = name;
    new bootstrap.Modal(document.getElementById('requestStatusUpdateModal')).show();
}
</script>

<!-- Status Update Modal -->
<div class="modal fade ims-confirm-modal" id="requestStatusUpdateModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
    <div class="modal-content" style="border:none;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,0.15);overflow:hidden;">
      <div class="modal-body">
        <div class="modal-confirm-icon"><i class="fas fa-sync-alt" style="color:#7c3aed;"></i></div>
        <div class="modal-confirm-title">Update Request Status</div>
        <p class="modal-confirm-msg">Change status for request by <strong id="statusUpdateEmployee"></strong></p>
        <form id="statusUpdateForm" method="POST">
          <?php echo csrf_field(); ?>
          <div class="mb-3">
            <select name="status" id="statusUpdateSelect" class="form-select form-select-sm">
              <option value="Pending">Pending</option>
              <option value="Approved">Approved</option>
              <option value="Rejected">Rejected</option>
            </select>
          </div>
          <div class="modal-confirm-actions">
            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-sm btn-primary"><i class="fas fa-save me-1"></i>Update Status</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Approve/Reject Confirmation Modal -->
<div class="modal fade ims-confirm-modal" id="requestActionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:360px;">
    <div class="modal-content" style="border:none;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,0.15);overflow:hidden;">
      <div class="modal-body">
        <div class="modal-confirm-icon"><i id="requestActionIcon" class="fas fa-check-circle" style="color:#16a34a;"></i></div>
        <div class="modal-confirm-title" id="requestActionTitle">Approve Request?</div>
        <p class="modal-confirm-msg" id="requestActionMsg"></p>
        <form id="requestActionForm" method="POST">
          <?php echo csrf_field(); ?>
          <div class="modal-confirm-actions">
            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" id="requestActionBtn" class="btn btn-sm btn-success"><i class="fas fa-check me-1"></i>Approve</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

