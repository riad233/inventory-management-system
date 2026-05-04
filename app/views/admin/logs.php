<div class="breadcrumb-nav">
    <a href="?url=dashboard/index">Dashboard</a> / <a href="?url=admin/index">Admin</a> / Activity Logs
</div>

<div class="list-page-header">
    <h2><i class="fas fa-history"></i> Activity Logs</h2>
    <p class="text-muted mb-0" style="font-size:.9rem">
        <?php echo number_format($data['total'] ?? 0); ?> total entries
        <?php if (($data['total'] ?? 0) > 0): ?>
        &nbsp;·&nbsp; Showing page <?php echo (int)($data['page'] ?? 1); ?> of <?php echo (int)($data['totalPages'] ?? 1); ?>
        <?php endif; ?>
    </p>
</div>

<!-- Filter bar -->
<form method="GET" action="" class="mb-3">
    <input type="hidden" name="url" value="admin/logs">
    <div class="filter-bar">
        <div class="filter-controls">
            <input type="text" name="search" class="filter-search"
                   placeholder="Search message, user, timestamp..."
                   value="<?php echo e($data['search'] ?? ''); ?>">
            <select name="level" class="filter-select" onchange="this.form.submit()">
                <option value="">All Levels</option>
                <?php foreach (['info','warning','error','debug'] as $lvl): ?>
                <option value="<?php echo $lvl; ?>"
                    <?php echo ($data['levelFilter'] ?? '') === $lvl ? 'selected' : ''; ?>>
                    <?php echo ucfirst($lvl); ?>
                </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-secondary btn-sm"><i class="fas fa-search"></i> Search</button>
            <?php if (($data['search'] ?? '') !== '' || ($data['levelFilter'] ?? '') !== ''): ?>
            <a href="?url=admin/logs" class="btn btn-outline-secondary btn-sm">Clear</a>
            <?php endif; ?>
        </div>
    </div>
</form>

<div class="table-responsive" style="background:#fff;border-radius:10px;border:1px solid #e5e7eb;">
    <table class="table table-hover mb-0" style="font-size:.88rem;">
        <thead style="background:#f1f5f9;">
            <tr>
                <th style="width:160px;">Timestamp</th>
                <th style="width:80px;">Level</th>
                <th style="width:120px;">User</th>
                <th>Message</th>
                <th>Context</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($data['logs'])): ?>
            <?php foreach ($data['logs'] as $log): ?>
            <?php
                $lvl    = $log['level'] ?? 'info';
                $badge  = match($lvl) {
                    'error'   => 'danger',
                    'warning' => 'warning',
                    'debug'   => 'secondary',
                    default   => 'success',
                };
                $ctx = $log['context'] ?? [];
                $ctxStr = !empty($ctx) ? implode(', ', array_map(
                    fn($k,$v) => $k . ': ' . (is_string($v) ? $v : json_encode($v)),
                    array_keys($ctx), $ctx
                )) : '—';
            ?>
            <tr>
                <td class="text-muted"><?php echo e($log['timestamp'] ?? ''); ?></td>
                <td><span class="badge bg-<?php echo $badge; ?>"><?php echo strtoupper(e($lvl)); ?></span></td>
                <td><?php echo e($log['user'] ?? '—'); ?></td>
                <td><?php echo e($log['message'] ?? ''); ?></td>
                <td class="text-muted" style="font-size:.8rem;max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                    title="<?php echo e($ctxStr); ?>">
                    <?php echo e($ctxStr); ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" class="text-center text-muted py-4">
                <?php echo file_exists(ROOT_PATH . '/storage/logs/app.log') ? 'No log entries match your search.' : 'No log file found yet.'; ?>
            </td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Pagination
$total      = (int)($data['total']      ?? 0);
$page       = (int)($data['page']       ?? 1);
$totalPages = (int)($data['totalPages'] ?? 1);
$search     = $data['search']      ?? '';
$lvlFilter  = $data['levelFilter'] ?? '';
$baseQ = '?url=admin/logs' . ($search !== '' ? '&search=' . urlencode($search) : '') . ($lvlFilter !== '' ? '&level=' . urlencode($lvlFilter) : '');
if ($totalPages > 1): ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo $baseQ . '&page=' . ($page - 1); ?>">Previous</a>
        </li>
        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
            <a class="page-link" href="<?php echo $baseQ . '&page=' . $i; ?>"><?php echo $i; ?></a>
        </li>
        <?php endfor; ?>
        <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo $baseQ . '&page=' . ($page + 1); ?>">Next</a>
        </li>
    </ul>
</nav>
<?php endif; ?>
