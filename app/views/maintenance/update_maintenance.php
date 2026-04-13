<?php
// Session already started in layout
require_once __DIR__ . '/../../../config/dropdown_helper.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update Maintenance Status - IMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg content-action-bar p-0">
  <div class="container-fluid">
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="btn btn-outline-secondary btn-sm" href="?url=maintenance/index"><i class="fas fa-arrow-left"></i> Back to Maintenance</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-warning text-dark">
          <h5 class="mb-0"><i class="fas fa-edit"></i> Update Maintenance Status</h5>
        </div>
        <div class="card-body">
          <form method="post" action="">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label class="form-label">Select Maintenance Record</label>
              <select name="maintenance_id" class="form-control" required>
                <option value="">Select Record</option>
                <?php if(isset($data['maintenance']) && is_array($data['maintenance'])): ?>
                  <?php foreach($data['maintenance'] as $maint): ?>
                    <option value="<?php echo e($maint['Maintenance_ID']); ?>">
                      <?php echo e(($maint['Asset_Name'] ?? '') . ' (' . $maint['Maintenance_ID'] . ')'); ?>
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-control" required>
                <option value="">Select Status</option>
                <?php echo DropdownHelper::renderOptions('maintenance_status'); ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">End Date</label>
              <input type="date" name="end_date" class="form-control" required>
            </div>
            <div class="text-end">
              <button type="submit" name="submit" class="btn btn-warning">
                <i class="fas fa-save"></i> Update Status
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
