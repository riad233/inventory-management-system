<?php
require_once __DIR__ . '/../../../config/dropdown_helper.php';
?>

<div class="list-page-header">
    <h2><i class="fas fa-clipboard"></i> Request Equipment</h2>
    <div class="list-header-actions">
        <a href="?url=request/index" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="row justify-content-center">
  <div class="col-md-7 col-lg-5">
    <div class="form-page-card">
      <div class="form-page-card-header" style="background:#eff6ff;">
        <i class="fas fa-paper-plane" style="color:#1d4ed8;"></i>
        <h5>New Equipment Request</h5>
      </div>
      <div class="card-body p-4">
          <form method="post" action="">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label class="form-label">Employee</label>
              <select name="user_id" class="form-select form-select-sm" required>
                <option value="">Select Employee</option>
                <?php if(isset($data['employees']) && is_array($data['employees'])): ?>
                  <?php foreach($data['employees'] as $emp): ?>
                    <option value="<?php echo e($emp['User_ID']); ?>"><?php echo e($emp['Name']); ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Equipment Type</label>
              <select name="equipment_type" class="form-select form-select-sm" required>
                <option value="">Select Equipment Type</option>
                <?php echo DropdownHelper::renderOptions('asset_categories'); ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Preferred Vendor <span class="text-muted fw-normal" style="font-size:0.9em;">(optional)</span></label>
              <select name="vendor_id" class="form-select form-select-sm">
                <option value="">Select Vendor</option>
                <?php if(isset($data['vendors']) && is_array($data['vendors'])): ?>
                  <?php foreach($data['vendors'] as $vendor): ?>
                    <option value="<?php echo e($vendor['Vendor_ID']); ?>"><?php echo e($vendor['Vendor_Name']); ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control form-control-sm" rows="4" required placeholder="Describe the equipment needed and reason..."></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <a href="?url=request/index" class="btn btn-sm btn-light"><i class="fas fa-times"></i> Cancel</a>
              <button type="submit" name="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-paper-plane me-1"></i> Submit Request
              </button>
            </div>
          </form>
      </div>
    </div>
  </div>
</div>
