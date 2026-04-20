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
    <div class="hero-actions">
      <a class="btn btn-outline-secondary" href="?url=asset/add"><i class="fas fa-plus"></i> New Asset</a>
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
</div>
