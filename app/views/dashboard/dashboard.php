<?php
$assetCounts  = $data['asset_status_counts'] ?? [];
$maintCounts  = $data['maintenance_status_counts'] ?? [];
$assetLabels  = array_keys($assetCounts);
$assetValues  = array_values($assetCounts);
$maintLabels  = array_keys($maintCounts);
$maintValues  = array_values($maintCounts);
?>
<link href="css/dashboard.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<div class="list-page-header">
    <h2><i class="fas fa-chart-line"></i> Dashboard</h2>
    <div class="list-header-actions">
        <span class="dash-date"><i class="fas fa-calendar-alt me-1"></i><?php echo date('D, d M Y'); ?></span>
    </div>
</div>

<?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo e($_GET['msg']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="kpi-grid">
    <a href="?url=asset/index" class="kpi-card kpi-green">
        <div class="kpi-icon"><i class="fas fa-boxes"></i></div>
        <div class="kpi-body">
            <div class="kpi-value"><?php echo e($data['total_assets'] ?? 0); ?></div>
            <div class="kpi-label">Total Assets</div>
        </div>
    </a>
    <a href="?url=assignment/index" class="kpi-card kpi-blue">
        <div class="kpi-icon"><i class="fas fa-exchange-alt"></i></div>
        <div class="kpi-body">
            <div class="kpi-value"><?php echo e($data['total_assignments'] ?? 0); ?></div>
            <div class="kpi-label">Assignments</div>
        </div>
    </a>
    <a href="?url=assignment/index" class="kpi-card kpi-orange">
        <div class="kpi-icon"><i class="fas fa-hourglass-half"></i></div>
        <div class="kpi-body">
            <div class="kpi-value"><?php echo e($data['total_pending'] ?? 0); ?></div>
            <div class="kpi-label">Pending Returns</div>
        </div>
    </a>
    <a href="?url=maintenance/index" class="kpi-card kpi-red">
        <div class="kpi-icon"><i class="fas fa-wrench"></i></div>
        <div class="kpi-body">
            <div class="kpi-value"><?php echo e($data['total_maintenance'] ?? 0); ?></div>
            <div class="kpi-label">Pending Maintenance</div>
        </div>
    </a>
</div>

<div class="secondary-stats">
    <div class="stat-pill">
        <i class="fas fa-users"></i>
        <span class="stat-pill-val"><?php echo e($data['total_employees'] ?? 0); ?></span>
        <span class="stat-pill-lbl">Employees</span>
    </div>
    <div class="stat-pill">
        <i class="fas fa-building"></i>
        <span class="stat-pill-val"><?php echo e($data['total_vendors'] ?? 0); ?></span>
        <span class="stat-pill-lbl">Vendors</span>
    </div>
    <div class="stat-pill">
        <i class="fas fa-user-shield"></i>
        <span class="stat-pill-val"><?php echo e($data['total_users'] ?? 0); ?></span>
        <span class="stat-pill-lbl">System Users</span>
    </div>
</div>

<div class="charts-row">
    <div class="chart-card">
        <div class="chart-card-header">
            <h6><i class="fas fa-circle-dot me-1"></i> Asset Status Breakdown</h6>
        </div>
        <div class="chart-card-body">
            <?php if(!empty($assetLabels)): ?>
                <canvas id="assetDonut"></canvas>
            <?php else: ?>
                <p class="chart-empty">No asset data available</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="chart-card">
        <div class="chart-card-header">
            <h6><i class="fas fa-chart-bar me-1"></i> Maintenance Status</h6>
        </div>
        <div class="chart-card-body">
            <?php if(!empty($maintLabels)): ?>
                <canvas id="maintBar"></canvas>
            <?php else: ?>
                <p class="chart-empty">No maintenance data available</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="recent-grid">
    <div class="recent-card">
        <div class="recent-header">
            <span><i class="fas fa-box me-1"></i> Recent Assets</span>
            <a href="?url=asset/index" class="recent-view-all">View All</a>
        </div>
        <table class="recent-table">
            <thead><tr><th>Name</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
            <?php if(!empty($data['recent_assets'])): ?>
                <?php foreach($data['recent_assets'] as $asset): ?>
                <tr>
                    <td><?php echo e($asset['Asset_Name']); ?></td>
                    <td><?php
                        $st = $asset['Status'];
                        $sc = $st === 'Available' ? 'status-active' : ($st === 'Under Maintenance' ? 'status-pending' : 'status-assigned');
                        echo '<span class="status-badge '.$sc.'">'.e($st).'</span>';
                    ?></td>
                    <td><?php echo date('d M Y', strtotime($asset['Purchase_Date'])); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3" class="recent-empty">No recent assets</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="recent-card">
        <div class="recent-header">
            <span><i class="fas fa-handshake me-1"></i> Recent Assignments</span>
            <a href="?url=assignment/index" class="recent-view-all">View All</a>
        </div>
        <table class="recent-table">
            <thead><tr><th>Asset</th><th>Employee</th><th>Status</th></tr></thead>
            <tbody>
            <?php if(!empty($data['recent_assignments'])): ?>
                <?php foreach($data['recent_assignments'] as $a): ?>
                <tr>
                    <td><?php echo e($a['Asset_Name'] ?? '—'); ?></td>
                    <td><?php echo e($a['Employee_Name'] ?? '—'); ?></td>
                    <td><?php
                        if($a['Actual_Return_Date'])
                            echo '<span class="status-badge status-returned">Returned</span>';
                        else
                            echo '<span class="status-badge status-assigned">Assigned</span>';
                    ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3" class="recent-empty">No recent assignments</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="recent-card">
        <div class="recent-header">
            <span><i class="fas fa-tools me-1"></i> Recent Maintenance</span>
            <a href="?url=maintenance/index" class="recent-view-all">View All</a>
        </div>
        <table class="recent-table">
            <thead><tr><th>Asset</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
            <?php if(!empty($data['recent_maintenance'])): ?>
                <?php foreach($data['recent_maintenance'] as $m): ?>
                <tr>
                    <td><?php echo e($m['Asset_Name'] ?? '—'); ?></td>
                    <td><?php
                        $ms = $m['Status'];
                        $mc = $ms === 'Pending' ? 'status-pending' : ($ms === 'Completed' ? 'status-approved' : 'status-assigned');
                        echo '<span class="status-badge '.$mc.'">'.e($ms).'</span>';
                    ?></td>
                    <td><?php echo date('d M Y', strtotime($m['Reported_Date'])); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3" class="recent-empty">No recent maintenance</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if(!empty($assetLabels) || !empty($maintLabels)): ?>
<script>
(function(){
<?php if(!empty($assetLabels)): ?>
    var aCtx = document.getElementById('assetDonut').getContext('2d');
    new Chart(aCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($assetLabels); ?>,
            datasets: [{
                data: <?php echo json_encode($assetValues); ?>,
                backgroundColor: ['#22c55e','#3b82f6','#f59e0b','#ef4444','#8b5cf6','#6b7280'],
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 6
            }]
        },
        options: {
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { padding: 14, font: { size: 12 } } },
                tooltip: { callbacks: { label: function(c){ return ' ' + c.label + ': ' + c.parsed; } } }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
<?php endif; ?>
<?php if(!empty($maintLabels)): ?>
    var mCtx = document.getElementById('maintBar').getContext('2d');
    new Chart(mCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($maintLabels); ?>,
            datasets: [{
                label: 'Records',
                data: <?php echo json_encode($maintValues); ?>,
                backgroundColor: ['#f59e0b','#22c55e','#3b82f6','#ef4444','#8b5cf6'],
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
<?php endif; ?>
})();
</script>
<?php endif; ?>
