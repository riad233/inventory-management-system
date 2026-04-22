<link href="css/dashboard.css" rel="stylesheet">

<div class="admin-dashboard">
  <div class="breadcrumb-nav">
    <a href="?url=home/index">Home</a> / Dashboard
  </div>

  <div class="dashboard-hero">
    <div>
      <h1>Admin Operations Dashboard</h1>
      <p>Monitor assets, assignments, and maintenance in one streamlined view.</p>
    </div>
  </div>

  <?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php echo e($_GET['msg']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="metric-grid">
    <div class="metric-card">
      <div>
        <div class="label">Total Assets</div>
        <p class="value"><?php echo e(isset($data['total_assets']) ? $data['total_assets'] : 0); ?></p>
      </div>
      <div class="icon"><i class="fas fa-cube"></i></div>
    </div>
    <div class="metric-card">
      <div>
        <div class="label">Active Assignments</div>
        <p class="value"><?php echo e(isset($data['total_assignments']) ? $data['total_assignments'] : 0); ?></p>
      </div>
      <div class="icon"><i class="fas fa-people-carry"></i></div>
    </div>
    <div class="metric-card">
      <div>
        <div class="label">Pending Returns</div>
        <p class="value"><?php echo e(isset($data['total_pending']) ? $data['total_pending'] : 0); ?></p>
      </div>
      <div class="icon"><i class="fas fa-rotate-left"></i></div>
    </div>
    <div class="metric-card">
      <div>
        <div class="label">Maintenance Pending</div>
        <p class="value"><?php echo e(isset($data['total_maintenance']) ? $data['total_maintenance'] : 0); ?></p>
      </div>
      <div class="icon"><i class="fas fa-screwdriver-wrench"></i></div>
    </div>
  </div>

  <!-- Recent Activity Section -->
  <div class="activity-grid">
    <!-- Recent Assets -->
    <div class="grid-section">
      <div class="grid-header">
        <h3><i class="fas fa-box"></i> Recent Assets</h3>
        <a href="?url=asset/index" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="table-responsive">
        <table class="table table-sm table-hover">
          <thead class="table-light">
            <tr>
              <th>Asset Name</th>
              <th>Status</th>
              <th>Purchase Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($data['recent_assets'])): ?>
              <?php foreach($data['recent_assets'] as $asset): ?>
                <tr>
                  <td><?php echo e($asset['Asset_Name']); ?></td>
                  <td>
                    <span class="badge bg-<?php echo $asset['Status'] == 'Active' ? 'success' : 'warning'; ?>">
                      <?php echo e($asset['Status']); ?>
                    </span>
                  </td>
                  <td><?php echo e(date('M d, Y', strtotime($asset['Purchase_Date']))); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="3" class="text-center text-muted">No recent assets</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Recent Assignments -->
    <div class="grid-section">
      <div class="grid-header">
        <h3><i class="fas fa-handshake"></i> Recent Assignments</h3>
        <a href="?url=assignment/index" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="table-responsive">
        <table class="table table-sm table-hover">
          <thead class="table-light">
            <tr>
              <th>Asset</th>
              <th>Employee</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($data['recent_assignments'])): ?>
              <?php foreach($data['recent_assignments'] as $assign): ?>
                <tr>
                  <td><?php echo e($assign['Asset_Name'] ?? 'N/A'); ?></td>
                  <td><?php echo e($assign['Employee_Name'] ?? 'N/A'); ?></td>
                  <td>
                    <span class="badge bg-<?php echo $assign['Actual_Return_Date'] ? 'secondary' : 'info'; ?>">
                      <?php echo $assign['Actual_Return_Date'] ? 'Returned' : 'Pending'; ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="3" class="text-center text-muted">No recent assignments</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Recent Maintenance -->
    <div class="grid-section">
      <div class="grid-header">
        <h3><i class="fas fa-tools"></i> Recent Maintenance</h3>
        <a href="?url=maintenance/index" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="table-responsive">
        <table class="table table-sm table-hover">
          <thead class="table-light">
            <tr>
              <th>Asset</th>
              <th>Status</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php if(!empty($data['recent_maintenance'])): ?>
              <?php foreach($data['recent_maintenance'] as $maint): ?>
                <tr>
                  <td><?php echo e($maint['Asset_Name'] ?? 'N/A'); ?></td>
                  <td>
                    <span class="badge bg-<?php echo $maint['Status'] == 'Pending' ? 'warning' : 'success'; ?>">
                      <?php echo e($maint['Status']); ?>
                    </span>
                  </td>
                  <td><?php echo e(date('M d, Y', strtotime($maint['Reported_Date']))); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="3" class="text-center text-muted">No recent maintenance</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

