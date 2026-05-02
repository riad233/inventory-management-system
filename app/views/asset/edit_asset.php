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

    <?php if(isset($data['errors']) && !empty($data['errors'])): ?>
    <div class="alert alert-danger alert-dismissible fade show mb-3">
        <strong><i class="fas fa-exclamation-circle me-1"></i> Please fix the following errors:</strong>
        <ul class="mb-0 mt-1">
            <?php foreach($data['errors'] as $field => $msg): ?><li><?php echo e($msg); ?></li><?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

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
                <label class="form-label">Vendor <span class="text-muted fw-normal" style="font-size:0.9em;">(optional)</span></label>
                <select name="vendor_id" class="form-select form-select-sm">
                  <option value="">Select Vendor</option>
                  <?php if(isset($data['vendors']) && is_array($data['vendors'])): ?>
                    <?php foreach($data['vendors'] as $vendor): ?>
                      <option value="<?php echo e($vendor['Vendor_ID']); ?>"
                        <?php echo (isset($data['asset']) && $data['asset']['Vendor_ID'] == $vendor['Vendor_ID']) ? 'selected' : ''; ?>>
                        <?php echo e($vendor['Vendor_Name']); ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>
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