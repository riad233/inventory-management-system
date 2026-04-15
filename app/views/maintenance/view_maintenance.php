<?php
// Session already started in layout
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Maintenance - IMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid mt-4">
  <div class="page-title"><i class="fas fa-tools"></i> Maintenance Records</div>

  <nav class="navbar navbar-expand-lg p-0" style="margin-bottom: 1rem;">
    <div class="container-fluid">
      <div class="collapse navbar-collapse justify-content-end gap-2">
        <div style="margin-right: 1rem;">
          <input type="text" id="maintenanceSearchInput" data-search-table="maintenanceTable" placeholder="Search maintenance..." style="padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px;">
        </div>
        <ul class="navbar-nav">
          <li class="nav-item"><a class="btn btn-primary btn-sm" href="?url=maintenance/add"><i class="fas fa-plus"></i> Add Maintenance</a></li>
        </ul>
      </div>
    </div>
  </nav>
  
  <?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php echo e($_GET['msg']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="card shadow">
    <div class="card-body">
      <table class="table table-striped table-hover" id="maintenanceTable">
        <thead class="table-dark">
          <tr>
            <th>Maintenance ID</th>
            <th>Asset</th>
            <th>Reported</th>
            <th>Status</th>
            <th>Cost</th>
          </tr>
        </thead>
        <tbody>
          <?php if(isset($data['maintenance']) && is_array($data['maintenance'])): ?>
            <?php foreach($data['maintenance'] as $maint): ?>
              <tr>
                <td><?php echo e($maint['Maintenance_ID']); ?></td>
                <td><?php echo e($maint['Asset_Name'] ?? 'N/A'); ?></td>
                <td><?php echo e($maint['Reported_Date']); ?></td>
                <td><span class="badge bg-warning"><?php echo e($maint['Status']); ?></span></td>
                <td><?php echo e($maint['Cost']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center py-4">No maintenance records found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/table-search.js"></script>
<script src="js/list-search-init.js"></script>
</body>
</html>
