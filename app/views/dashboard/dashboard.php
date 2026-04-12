<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@31.2.1/styles/ag-grid.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community@31.2.1/styles/ag-theme-quartz.css">
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
  .admin-dashboard {
    font-family: 'Source Sans 3', sans-serif;
    color: #23262f;
  }

  .dashboard-hero {
    background: #f7f8fb;
    border: 1px solid #e6e9ef;
    border-radius: 16px;
    padding: 24px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 24px;
  }

  .dashboard-hero h1 {
    font-size: 1.8rem;
    margin: 0 0 6px;
    font-weight: 700;
  }

  .dashboard-hero p {
    margin: 0;
    color: #5f6776;
  }

  .hero-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .hero-actions .btn {
    border-radius: 999px;
    padding: 8px 16px;
    font-weight: 600;
  }

  .metric-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
  }

  .metric-card {
    background: #ffffff;
    border: 1px solid #e6e9ef;
    border-radius: 14px;
    padding: 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
  }

  .metric-card .label {
    color: #5f6776;
    font-size: 0.9rem;
  }

  .metric-card .value {
    font-size: 1.6rem;
    font-weight: 700;
    margin: 0;
  }

  .metric-card .icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f4f9;
    color: #3f4c5a;
    font-size: 1.1rem;
  }

  .grid-section {
    background: #ffffff;
    border: 1px solid #e6e9ef;
    border-radius: 16px;
    padding: 18px;
    margin-bottom: 20px;
    box-shadow: 0 10px 26px rgba(15, 23, 42, 0.05);
  }

  .grid-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 12px;
  }

  .grid-header h3 {
    font-size: 1.1rem;
    margin: 0;
    font-weight: 700;
  }

  .grid-meta {
    color: #5f6776;
    font-size: 0.85rem;
  }

  .grid-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 14px;
  }

  .grid-search {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .grid-search input {
    border: 1px solid #e0e4ee;
    border-radius: 999px;
    padding: 6px 12px;
    font-size: 0.9rem;
    min-width: 220px;
  }

  .chip-group {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .chip {
    border: 1px solid #d8dce5;
    background: #f7f8fb;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    color: #4d5563;
    transition: all 0.2s ease;
  }

  .chip.active {
    background: #1f2937;
    color: #ffffff;
    border-color: #1f2937;
  }

  .grid-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }

  .grid-actions .btn {
    border-radius: 999px;
    font-weight: 600;
    font-size: 0.85rem;
    padding: 6px 12px;
  }

  .grid-actions .btn-secondary {
    background: #f3f4f6;
    border-color: #d8dce5;
    color: #4b5563;
  }

  .modal-detail-table {
    width: 100%;
  }

  .modal-detail-table td {
    padding: 6px 0;
    vertical-align: top;
  }

  .modal-detail-table td:first-child {
    width: 40%;
    color: #6b7280;
    font-weight: 600;
  }

  .ag-theme-quartz {
    --ag-font-family: 'Source Sans 3', sans-serif;
    --ag-font-size: 13px;
    --ag-border-color: #e6e9ef;
    --ag-row-hover-color: #f7f9fc;
    --ag-selected-row-background-color: #eef3ff;
    --ag-header-background-color: #f5f7fa;
    --ag-foreground-color: #23262f;
  }

  .grid-height {
    height: 320px;
    width: 100%;
  }

  @media (max-width: 768px) {
    .dashboard-hero {
      padding: 18px;
    }
  }
</style>

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

<script src="https://cdn.jsdelivr.net/npm/ag-grid-community@31.2.1/dist/ag-grid-community.min.js"></script>
<script>
  const assetsData = <?php echo json_encode($data['assets'] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
  const assignmentsData = <?php echo json_encode($data['assignments'] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
  const maintenanceData = <?php echo json_encode($data['maintenance'] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

  const defaultGridOptions = {
    defaultColDef: {
      sortable: true,
      filter: true,
      floatingFilter: true,
      resizable: true,
      minWidth: 110
    },
    rowSelection: 'multiple',
    pagination: true,
    paginationPageSize: 8,
    animateRows: true
  };

  const assetGridOptions = {
    ...defaultGridOptions,
    columnDefs: [
      { headerName: 'Asset ID', field: 'Asset_ID', maxWidth: 120 },
      { headerName: 'Asset Name', field: 'Asset_Name', flex: 1 },
      { headerName: 'Category', field: 'Category' },
      { headerName: 'Brand', field: 'Brand' },
      { headerName: 'Status', field: 'Status' },
      { headerName: 'Purchase Date', field: 'Purchase_Date' },
      {
        headerName: 'Action',
        maxWidth: 120,
        sortable: false,
        filter: false,
        cellRenderer: params => {
          if (!params.data || !params.data.Asset_ID) {
            return '';
          }
          return `<a class="btn btn-sm btn-outline-secondary" href="?url=asset/edit/${params.data.Asset_ID}">Edit</a>`;
        }
      }
    ],
    rowData: assetsData,
    onRowClicked: event => {
      if (event.data && event.data.Asset_ID) {
        window.location.href = `?url=asset/edit/${event.data.Asset_ID}`;
      }
    }
  };

  const assignmentGridOptions = {
    ...defaultGridOptions,
    columnDefs: [
      { headerName: 'Assignment ID', field: 'Assignment_ID', maxWidth: 140 },
      { headerName: 'Asset', field: 'Asset_Name', flex: 1 },
      { headerName: 'Employee', field: 'Name' },
      { headerName: 'Assigned Date', field: 'Assigned_Date' },
      { headerName: 'Expected Return', field: 'Expected_Return_Date' },
      { headerName: 'Actual Return', field: 'Actual_Return_Date' },
      {
        headerName: 'Return Status',
        field: 'Return_Status',
        valueGetter: params => (params.data && params.data.Actual_Return_Date ? 'Returned' : 'Assigned')
      },
      {
        headerName: 'Action',
        maxWidth: 160,
        sortable: false,
        filter: false,
        cellRenderer: params => {
          if (!params.data || !params.data.Assignment_ID) {
            return '';
          }
          const viewUrl = '?url=assignment/index';
          const returnUrl = '?url=assignment/returnAsset';
          return `
            <div style="display:flex;gap:6px;">
              <a class="btn btn-sm btn-outline-secondary" href="${viewUrl}">View</a>
              <a class="btn btn-sm btn-outline-secondary" href="${returnUrl}">Return</a>
            </div>
          `;
        }
      }
    ],
    rowData: assignmentsData,
    onRowClicked: event => {
      if (event.data) {
        openDetailModal({
          title: 'Assignment Details',
          rows: {
            'Assignment ID': event.data.Assignment_ID || '-',
            'Asset': event.data.Asset_Name || '-',
            'Employee': event.data.Name || '-',
            'Assigned Date': event.data.Assigned_Date || '-',
            'Expected Return': event.data.Expected_Return_Date || '-',
            'Actual Return': event.data.Actual_Return_Date || '-',
            'Return Status': event.data.Actual_Return_Date ? 'Returned' : 'Assigned'
          },
          primaryLabel: 'Go to Assignments',
          primaryUrl: '?url=assignment/index',
          secondaryLabel: 'Return Asset',
          secondaryUrl: '?url=assignment/returnAsset'
        });
      }
    }
  };

  const maintenanceGridOptions = {
    ...defaultGridOptions,
    columnDefs: [
      { headerName: 'Maintenance ID', field: 'Maintenance_ID', maxWidth: 150 },
      { headerName: 'Asset', field: 'Asset_Name', flex: 1 },
      { headerName: 'Reported Date', field: 'Reported_Date' },
      { headerName: 'Repair End Date', field: 'Repair_End_Date' },
      { headerName: 'Status', field: 'Status' },
      { headerName: 'Cost', field: 'Cost' },
      {
        headerName: 'Action',
        maxWidth: 180,
        sortable: false,
        filter: false,
        cellRenderer: params => {
          if (!params.data || !params.data.Maintenance_ID) {
            return '';
          }
          return '<a class="btn btn-sm btn-outline-secondary" href="?url=maintenance/updateStatus">Update</a>';
        }
      }
    ],
    rowData: maintenanceData,
    onRowClicked: event => {
      if (event.data) {
        openDetailModal({
          title: 'Maintenance Details',
          rows: {
            'Maintenance ID': event.data.Maintenance_ID || '-',
            'Asset': event.data.Asset_Name || '-',
            'Reported Date': event.data.Reported_Date || '-',
            'Repair End Date': event.data.Repair_End_Date || '-',
            'Status': event.data.Status || '-',
            'Cost': event.data.Cost || '-'
          },
          primaryLabel: 'Update Status',
          primaryUrl: '?url=maintenance/updateStatus',
          secondaryLabel: 'View Maintenance',
          secondaryUrl: '?url=maintenance/index'
        });
      }
    }
  };

  const grids = {};
  const gridViewStorageKey = (gridKey) => `ims_grid_view_${gridKey}`;

  const saveGridView = (gridKey) => {
    const grid = grids[gridKey];
    if (!grid) {
      return;
    }
    const payload = {
      columnState: grid.columnApi.getColumnState(),
      filterModel: grid.api.getFilterModel(),
      sortModel: grid.api.getSortModel()
    };
    localStorage.setItem(gridViewStorageKey(gridKey), JSON.stringify(payload));
  };

  const loadGridView = (gridKey) => {
    const grid = grids[gridKey];
    if (!grid) {
      return;
    }
    const raw = localStorage.getItem(gridViewStorageKey(gridKey));
    if (!raw) {
      return;
    }
    const payload = JSON.parse(raw);
    if (payload.columnState) {
      grid.columnApi.applyColumnState({ state: payload.columnState, applyOrder: true });
    }
    if (payload.filterModel) {
      grid.api.setFilterModel(payload.filterModel);
    }
    if (payload.sortModel) {
      grid.api.setSortModel(payload.sortModel);
    }
  };

  const resetGridView = (gridKey) => {
    const grid = grids[gridKey];
    if (!grid) {
      return;
    }
    localStorage.removeItem(gridViewStorageKey(gridKey));
    grid.columnApi.resetColumnState();
    grid.api.setFilterModel(null);
    grid.api.setSortModel(null);
  };

  const applyChipFilter = (gridKey, value) => {
    const grid = grids[gridKey];
    if (!grid) {
      return;
    }

    if (value === 'all') {
      grid.api.setFilterModel(null);
      return;
    }

    if (gridKey === 'assignment') {
      grid.api.setFilterModel({
        Return_Status: { filterType: 'text', type: 'equals', filter: value }
      });
      return;
    }

    grid.api.setFilterModel({
      Status: { filterType: 'text', type: 'equals', filter: value }
    });
  };

  const openDetailModal = ({ title, rows, primaryLabel, primaryUrl, secondaryLabel, secondaryUrl }) => {
    const titleEl = document.getElementById('rowDetailTitle');
    const bodyEl = document.getElementById('rowDetailBody');
    const primaryEl = document.getElementById('rowDetailPrimary');
    const secondaryEl = document.getElementById('rowDetailSecondary');

    if (!titleEl || !bodyEl || !primaryEl || !secondaryEl) {
      return;
    }

    titleEl.textContent = title;
    bodyEl.innerHTML = '';

    Object.entries(rows).forEach(([label, value]) => {
      const row = document.createElement('tr');
      row.innerHTML = `<td>${label}</td><td>${value}</td>`;
      bodyEl.appendChild(row);
    });

    primaryEl.textContent = primaryLabel;
    primaryEl.href = primaryUrl;
    secondaryEl.textContent = secondaryLabel;
    secondaryEl.href = secondaryUrl;

    const modal = new bootstrap.Modal(document.getElementById('rowDetailModal'));
    modal.show();
  };

  const registerToolbar = (gridKey) => {
    const searchInput = document.querySelector(`.grid-quick-filter[data-grid="${gridKey}"]`);
    const chipGroup = document.querySelector(`.chip-group[data-grid="${gridKey}"]`);
    const exportButtons = document.querySelectorAll(`.grid-export[data-grid="${gridKey}"]`);
    const saveButtons = document.querySelectorAll(`.grid-save-view[data-grid="${gridKey}"]`);
    const resetButtons = document.querySelectorAll(`.grid-reset-view[data-grid="${gridKey}"]`);

    if (searchInput) {
      searchInput.addEventListener('input', (event) => {
        const grid = grids[gridKey];
        if (grid) {
          grid.api.setQuickFilter(event.target.value);
        }
      });
    }

    if (chipGroup) {
      chipGroup.addEventListener('click', (event) => {
        const button = event.target.closest('.chip');
        if (!button) {
          return;
        }
        chipGroup.querySelectorAll('.chip').forEach(chip => chip.classList.remove('active'));
        button.classList.add('active');
        applyChipFilter(gridKey, button.dataset.filter);
      });
    }

    exportButtons.forEach(button => {
      button.addEventListener('click', () => {
        const grid = grids[gridKey];
        if (grid) {
          if (button.dataset.format === 'excel') {
            if (typeof grid.api.exportDataAsExcel === 'function') {
              grid.api.exportDataAsExcel();
            } else {
              grid.api.exportDataAsCsv();
            }
            return;
          }
          grid.api.exportDataAsCsv();
        }
      });
    });

    saveButtons.forEach(button => {
      button.addEventListener('click', () => saveGridView(gridKey));
    });

    resetButtons.forEach(button => {
      button.addEventListener('click', () => resetGridView(gridKey));
    });
  };

  document.addEventListener('DOMContentLoaded', () => {
    const assetGrid = document.querySelector('#assetGrid');
    const assignmentGrid = document.querySelector('#assignmentGrid');
    const maintenanceGrid = document.querySelector('#maintenanceGrid');

    if (assetGrid) {
      grids.asset = agGrid.createGrid(assetGrid, assetGridOptions);
      registerToolbar('asset');
      loadGridView('asset');
    }

    if (assignmentGrid) {
      grids.assignment = agGrid.createGrid(assignmentGrid, assignmentGridOptions);
      registerToolbar('assignment');
      loadGridView('assignment');
    }

    if (maintenanceGrid) {
      grids.maintenance = agGrid.createGrid(maintenanceGrid, maintenanceGridOptions);
      registerToolbar('maintenance');
      loadGridView('maintenance');
    }
  });
</script>
