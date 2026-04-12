<?php
// Session already started in layout
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Maintenance - IMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="?url=dashboard/index"><i class="fas fa-boxes"></i> IMS</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="?url=maintenance/index">Back to Maintenance</a></li>
        <li class="nav-item"><a class="nav-link" href="?url=auth/logout">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-warning text-dark">
          <h5 class="mb-0"><i class="fas fa-tools"></i> Add Maintenance Record</h5>
        </div>
        <div class="card-body">
          <form method="post" action="">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label class="form-label">Asset</label>
              <select name="asset_id" class="form-control" required>
                <option value="">Select Asset</option>
                <?php if(isset($data['assets']) && is_array($data['assets'])): ?>
                  <?php foreach($data['assets'] as $asset): ?>
                    <option value="<?php echo e($asset['Asset_ID']); ?>"><?php echo e($asset['Asset_Name']); ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Estimated Cost</label>
              <input type="number" step="0.01" name="cost" class="form-control" required>
            </div>
            <div class="text-end">
              <button type="submit" name="submit" class="btn btn-warning">
                <i class="fas fa-save"></i> Add Record
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
