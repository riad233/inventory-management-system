<?php
require_once __DIR__ . '/../../../config/dropdown_helper.php';
?>

<div class="list-page-header">
    <h2><i class="fas fa-cube"></i> Edit Asset</h2>
    <div class="list-header-actions">
        <a href="?url=asset/index" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="row justify-content-center">
  <div class="col-md-8 col-lg-6">
    <div class="form-page-card">
      <div class="form-page-card-header" style="background:#fef9c3;">
        <i class="fas fa-edit" style="color:#92400e;"></i>
        <h5>Edit Asset</h5>
      </div>
      <div class="card-body p-4">
          <form method="post" action="">
            <?php echo csrf_field(); ?>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Asset Name</label>
                <input type="text" name="name" class="form-control form-control-sm" value="<?php echo isset($data['asset']) ? e($data['asset']['Asset_Name']) : ''; ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Category</label>
                <select name="category" class="form-select form-select-sm" required>
                  <option value="">Select Category</option>
                  <?php echo DropdownHelper::renderOptions('asset_categories', isset($data['asset']) ? $data['asset']['Category'] : null); ?>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Brand</label>
                <input type="text" name="brand" class="form-control form-control-sm" value="<?php echo isset($data['asset']) ? e($data['asset']['Brand']) : ''; ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Model</label>
                <input type="text" name="model" class="form-control form-control-sm" value="<?php echo isset($data['asset']) ? e($data['asset']['Model']) : ''; ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Serial Number</label>
                <input type="text" name="serial" class="form-control form-control-sm" value="<?php echo isset($data['asset']) ? e($data['asset']['Serial_Number']) : ''; ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select form-select-sm" required>
                  <option value="">Select Status</option>
                  <?php echo DropdownHelper::renderOptions('asset_status', isset($data['asset']) ? $data['asset']['Status'] : null); ?>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Purchase Date</label>
                <input type="date" name="purchase_date" class="form-control form-control-sm" value="<?php echo isset($data['asset']) ? e($data['asset']['Purchase_Date']) : ''; ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Warranty Expiry</label>
                <input type="date" name="warranty" class="form-control form-control-sm" value="<?php echo isset($data['asset']) ? e($data['asset']['Warranty_Expiry']) : ''; ?>" required>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label">Vendor</label>
                <select name="vendor_id" class="form-select form-select-sm">
                  <option value="">Select Vendor</option>
                  <option value="1" <?php echo isset($data['asset']) && $data['asset']['Vendor_ID'] == 1 ? 'selected' : ''; ?>>Tech Solutions Ltd</option>
                  <option value="2" <?php echo isset($data['asset']) && $data['asset']['Vendor_ID'] == 2 ? 'selected' : ''; ?>>Global IT Suppliers</option>
                  <option value="3" <?php echo isset($data['asset']) && $data['asset']['Vendor_ID'] == 3 ? 'selected' : ''; ?>>Digital World Bangladesh</option>
                  <option value="4" <?php echo isset($data['asset']) && $data['asset']['Vendor_ID'] == 4 ? 'selected' : ''; ?>>Office Solutions Inc</option>
                  <option value="5" <?php echo isset($data['asset']) && $data['asset']['Vendor_ID'] == 5 ? 'selected' : ''; ?>>Enterprise Systems Ltd</option>
                  <option value="6" <?php echo isset($data['asset']) && $data['asset']['Vendor_ID'] == 6 ? 'selected' : ''; ?>>Future Tech Co</option>
                  <option value="7" <?php echo isset($data['asset']) && $data['asset']['Vendor_ID'] == 7 ? 'selected' : ''; ?>>Premium Services Group</option>
                  <option value="8" <?php echo isset($data['asset']) && $data['asset']['Vendor_ID'] == 8 ? 'selected' : ''; ?>>Regional IT Partners</option>
                  <option value="9" <?php echo isset($data['asset']) && $data['asset']['Vendor_ID'] == 9 ? 'selected' : ''; ?>>Growth Solutions</option>
                  <option value="10" <?php echo isset($data['asset']) && $data['asset']['Vendor_ID'] == 10 ? 'selected' : ''; ?>>Smart Systems Bangladesh</option>
                </select>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
              <a href="?url=asset/index" class="btn btn-sm btn-light"><i class="fas fa-times"></i> Cancel</a>
              <button type="submit" name="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save me-1"></i> Update Asset
              </button>
            </div>
          </form>
      </div>
    </div>
  </div>
</div>