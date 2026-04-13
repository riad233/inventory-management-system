<?php
// Session already started in layout
require_once __DIR__ . '/../../../config/dropdown_helper.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Asset - IMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg content-action-bar p-0">
  <div class="container-fluid">
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="btn btn-outline-secondary btn-sm" href="?url=asset/index"><i class="fas fa-arrow-left"></i> Back to Assets</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0"><i class="fas fa-plus"></i> Add New Asset</h5>
        </div>
        <div class="card-body">
          <form method="post" action="">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label class="form-label">Asset Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Category</label>
              <select name="category" class="form-control" required>
                <option value="">Select Category</option>
                <?php echo DropdownHelper::renderOptions('asset_categories'); ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Brand</label>
              <input type="text" name="brand" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Model</label>
              <input type="text" name="model" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Serial Number</label>
              <input type="text" name="serial" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Purchase Date</label>
              <input type="date" name="purchase_date" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Warranty Expiry</label>
              <input type="date" name="warranty" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select name="status" class="form-control" required>
                <option value="">Select Status</option>
                <?php echo DropdownHelper::renderOptions('asset_status'); ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Vendor</label>
              <select name="vendor_id" class="form-control">
                <option value="">Select Vendor</option>
                <option value="1">Tech Solutions Ltd</option>
                <option value="2">Global IT Suppliers</option>
                <option value="3">Digital World Bangladesh</option>
                <option value="4">Office Solutions Inc</option>
                <option value="5">Enterprise Systems Ltd</option>
                <option value="6">Future Tech Co</option>
                <option value="7">Premium Services Group</option>
                <option value="8">Regional IT Partners</option>
                <option value="9">Growth Solutions</option>
                <option value="10">Smart Systems Bangladesh</option>
              </select>
            </div>
            <div class="text-end">
              <button type="submit" name="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Add Asset
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
