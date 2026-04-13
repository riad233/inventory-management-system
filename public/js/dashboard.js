const dashboardDataEl = document.getElementById('dashboard-data');
let assetsData = [];
let assignmentsData = [];
let maintenanceData = [];

if (dashboardDataEl) {
  try {
    const payload = JSON.parse(dashboardDataEl.textContent);
    assetsData = payload.assets || [];
    assignmentsData = payload.assignments || [];
    maintenanceData = payload.maintenance || [];
  } catch (error) {
    assetsData = [];
    assignmentsData = [];
    maintenanceData = [];
  }
}

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
