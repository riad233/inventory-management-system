<?php
require_once __DIR__ . '/../../../config/dropdown_helper.php';
?>

<div class="list-page-header">
    <h2><i class="fas fa-cube"></i> Add New Asset</h2>
    <div class="list-header-actions">
        <a href="?url=asset/index" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6">
    <div class="form-page-card">
      <div class="form-page-card-header" style="background:#eff6ff;">
        <i class="fas fa-plus" style="color:#1d4ed8;"></i>
        <h5>Add New Asset</h5>
      </div>
      <div class="card-body p-4">
          <form method="post" action="">
            <?php echo csrf_field(); ?>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Asset Name</label>
                <input type="text" name="name" class="form-control form-control-sm" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Category</label>
                <select name="category" class="form-select form-select-sm" required>
                  <option value="">Select Category</option>
                  <?php echo DropdownHelper::renderOptions('asset_categories'); ?>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Brand</label>
                <input type="text" name="brand" class="form-control form-control-sm" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Model</label>
                <input type="text" name="model" class="form-control form-control-sm" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Serial Number</label>
                <input type="text" name="serial" class="form-control form-control-sm" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select form-select-sm" required>
                  <option value="">Select Status</option>
                  <?php echo DropdownHelper::renderOptions('asset_status'); ?>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Purchase Date</label>
                <input type="date" name="purchase_date" class="form-control form-control-sm" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Warranty Expiry</label>
                <input type="date" name="warranty" class="form-control form-control-sm" required>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label">Vendor</label>
                <select name="vendor_id" class="form-select form-select-sm">
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
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
              <a href="?url=asset/index" class="btn btn-sm btn-light"><i class="fas fa-times"></i> Cancel</a>
              <button type="submit" name="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save me-1"></i> Add Asset
              </button>
            </div>
          </form>
      </div>
    </div>
  </div>
</div>
