<?php
// Session already started in layout
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Asset - IMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="?url=dashboard/index"><i class="fas fa-boxes"></i> IMS</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="?url=asset/index">Back to Assets</a></li>
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
          <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Asset</h5>
        </div>
        <div class="card-body">
          <form method="post" action="">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label class="form-label">Asset Name</label>
              <input type="text" name="name" class="form-control" value="<?php echo isset($data['asset']) ? e($data['asset']['Asset_Name']) : ''; ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Category</label>
              <select name="category" class="form-control" required>
                <option>Computer</option>
                <option>Furniture</option>
                <option>Equipment</option>
                <option>Other</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Brand</label>
              <input type="text" name="brand" class="form-control" value="<?php echo isset($data['asset']) ? e($data['asset']['Brand']) : ''; ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Model</label>
              <input type="text" name="model" class="form-control" value="<?php echo isset($data['asset']) ? e($data['asset']['Model']) : ''; ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Serial Number</label>
              <input type="text" name="serial" class="form-control" value="<?php echo isset($data['asset']) ? e($data['asset']['Serial_Number']) : ''; ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Purchase Date</label>
              <input type="date" name="purchase_date" class="form-control" value="<?php echo isset($data['asset']) ? e($data['asset']['Purchase_Date']) : ''; ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Warranty Expiry</label>
              <input type="date" name="warranty" class="form-control" value="<?php echo isset($data['asset']) ? e($data['asset']['Warranty_Expiry']) : ''; ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-control" required>
                <option>Available</option>
                <option>In Use</option>
                <option>Maintenance</option>
                <option>Disposed</option>
              </select>
            </div>
            <div class="text-end">
              <button type="submit" name="submit" class="btn btn-warning">
                <i class="fas fa-save"></i> Update Asset
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
