<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@31.2.1/styles/ag-grid.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@31.2.1/styles/ag-theme-quartz.css">
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">
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
      <a class="btn btn-primary" href="?url=product/index"><i class="fas fa-box-open"></i> Product Masterfile</a>
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

  <div class="grid-section">
    <div class="grid-header">
      <h3>Assets Overview</h3>
      <div class="grid-meta">Live inventory snapshot</div>
    </div>
    <div class="grid-toolbar" data-grid="asset">
      <div class="grid-search">
        <input type="search" class="grid-quick-filter" data-grid="asset" placeholder="Search assets...">
        <div class="chip-group" data-grid="asset">
          <button type="button" class="chip active" data-filter="all">All</button>
          <button type="button" class="chip" data-filter="Available">Available</button>
          <button type="button" class="chip" data-filter="In Use">In Use</button>
          <button type="button" class="chip" data-filter="Maintenance">Maintenance</button>
          <button type="button" class="chip" data-filter="Disposed">Disposed</button>
        </div>
      </div>
      <div class="grid-actions">
        <button type="button" class="btn btn-secondary grid-save-view" data-grid="asset">Save View</button>
        <button type="button" class="btn btn-secondary grid-reset-view" data-grid="asset">Reset View</button>
        <button type="button" class="btn btn-outline-secondary grid-export" data-grid="asset">Export CSV</button>
        <button type="button" class="btn btn-outline-secondary grid-export" data-grid="asset" data-format="excel">Export Excel (CSV)</button>
      </div>
    </div>
    <div id="assetGrid" class="ag-theme-quartz grid-height"></div>
  </div>

  <div class="grid-section">
    <div class="grid-header">
      <h3>Assignments Tracking</h3>
      <div class="grid-meta">Employee allocations and return status</div>
    </div>
    <div class="grid-toolbar" data-grid="assignment">
      <div class="grid-search">
        <input type="search" class="grid-quick-filter" data-grid="assignment" placeholder="Search assignments...">
        <div class="chip-group" data-grid="assignment">
          <button type="button" class="chip active" data-filter="all">All</button>
          <button type="button" class="chip" data-filter="Assigned">Assigned</button>
          <button type="button" class="chip" data-filter="Returned">Returned</button>
        </div>
      </div>
      <div class="grid-actions">
        <button type="button" class="btn btn-secondary grid-save-view" data-grid="assignment">Save View</button>
        <button type="button" class="btn btn-secondary grid-reset-view" data-grid="assignment">Reset View</button>
        <button type="button" class="btn btn-outline-secondary grid-export" data-grid="assignment">Export CSV</button>
        <button type="button" class="btn btn-outline-secondary grid-export" data-grid="assignment" data-format="excel">Export Excel (CSV)</button>
      </div>
    </div>
    <div id="assignmentGrid" class="ag-theme-quartz grid-height"></div>
  </div>

  <div class="grid-section">
    <div class="grid-header">
      <h3>Maintenance Pipeline</h3>
      <div class="grid-meta">Costs, dates, and repair progress</div>
    </div>
    <div class="grid-toolbar" data-grid="maintenance">
      <div class="grid-search">
        <input type="search" class="grid-quick-filter" data-grid="maintenance" placeholder="Search maintenance...">
        <div class="chip-group" data-grid="maintenance">
          <button type="button" class="chip active" data-filter="all">All</button>
          <button type="button" class="chip" data-filter="Pending">Pending</button>
          <button type="button" class="chip" data-filter="In Progress">In Progress</button>
          <button type="button" class="chip" data-filter="Completed">Completed</button>
        </div>
      </div>
      <div class="grid-actions">
        <button type="button" class="btn btn-secondary grid-save-view" data-grid="maintenance">Save View</button>
        <button type="button" class="btn btn-secondary grid-reset-view" data-grid="maintenance">Reset View</button>
        <button type="button" class="btn btn-outline-secondary grid-export" data-grid="maintenance">Export CSV</button>
        <button type="button" class="btn btn-outline-secondary grid-export" data-grid="maintenance" data-format="excel">Export Excel (CSV)</button>
      </div>
    </div>
    <div id="maintenanceGrid" class="ag-theme-quartz grid-height"></div>
  </div>
</div>

<div class="modal fade" id="rowDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rowDetailTitle">Row Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="modal-detail-table" id="rowDetailBody"></table>
      </div>
      <div class="modal-footer">
        <a href="#" class="btn btn-outline-secondary" id="rowDetailSecondary">View</a>
        <a href="#" class="btn btn-primary" id="rowDetailPrimary">Edit</a>
      </div>
    </div>
  </div>
</div>

<script type="application/json" id="dashboard-data"><?php
  echo json_encode([
    'assets' => $data['assets'] ?? [],
    'assignments' => $data['assignments'] ?? [],
    'maintenance' => $data['maintenance'] ?? []
  ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
?></script>
<script src="https://cdn.jsdelivr.net/npm/ag-grid-community@31.2.1/dist/ag-grid-community.min.js"></script>
<script src="js/dashboard.js"></script>
