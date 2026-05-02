<?php
require_once __DIR__ . '/../../../config/dropdown_helper.php';
?>

<div class="list-page-header">
    <h2><i class="fas fa-tools"></i> Add Maintenance Record</h2>
    <div class="list-header-actions">
        <a href="?url=maintenance/index" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<?php if(isset($data['errors']) && !empty($data['errors'])): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <strong>Please fix the following errors:</strong>
    <ul class="mb-0 mt-1">
        <?php foreach($data['errors'] as $field => $errMsg): ?><li><?php echo e($errMsg); ?></li><?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row justify-content-center">
  <div class="col-md-7 col-lg-5">
    <div class="form-page-card">
      <div class="form-page-card-header" style="background:#fef9c3;">
        <i class="fas fa-tools" style="color:#92400e;"></i>
        <h5>Add Maintenance Record</h5>
      </div>
      <div class="card-body p-4">
          <form method="post" action="">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label class="form-label">Asset</label>
              <select name="asset_id" class="form-select form-select-sm" required>
                <option value="">Select Asset</option>
                <?php if(isset($data['assets']) && is_array($data['assets'])): ?>
                  <?php foreach($data['assets'] as $asset): ?>
                    <option value="<?php echo e($asset['Asset_ID']); ?>"><?php echo e($asset['Asset_Name']); ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Maintenance Status</label>
              <select name="maintenance_status" class="form-select form-select-sm" required>
                <option value="">Select Status</option>
                <?php echo DropdownHelper::renderOptions('maintenance_status'); ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Vendor</label>
              <select name="vendor_id" class="form-select form-select-sm">
                <option value="">Select Vendor (optional)</option>
                <?php if(isset($data['vendors']) && is_array($data['vendors'])): ?>
                  <?php foreach($data['vendors'] as $vendor): ?>
                    <option value="<?php echo e($vendor['Vendor_ID']); ?>"><?php echo e($vendor['Vendor_Name']); ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Estimated Cost</label>
              <input type="number" step="0.01" name="cost" class="form-control form-control-sm" required>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <a href="?url=maintenance/index" class="btn btn-sm btn-light"><i class="fas fa-times"></i> Cancel</a>
              <button type="submit" name="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save me-1"></i> Add Record
              </button>
            </div>
          </form>
      </div>
    </div>
  </div>
</div>
