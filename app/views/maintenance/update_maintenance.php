<?php
require_once __DIR__ . '/../../../config/dropdown_helper.php';
?>

<div class="list-page-header">
    <h2><i class="fas fa-sync-alt"></i> Update Maintenance Status</h2>
    <div class="list-header-actions">
        <a href="?url=maintenance/index" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="row justify-content-center">
  <div class="col-md-7 col-lg-5">
    <div class="form-page-card">
      <div class="form-page-card-header" style="background:#fef9c3;">
        <i class="fas fa-edit" style="color:#92400e;"></i>
        <h5>Update Maintenance Status</h5>
      </div>
      <div class="card-body p-4">
          <form method="post" action="">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label class="form-label">Select Maintenance Record</label>
              <select name="maintenance_id" class="form-select form-select-sm" required>
                <option value="">Select Record</option>
                <?php if(isset($data['maintenance']) && is_array($data['maintenance'])): ?>
                  <?php foreach($data['maintenance'] as $maint): ?>
                    <option value="<?php echo e($maint['Maintenance_ID']); ?>">
                      <?php echo e(($maint['Asset_Name'] ?? '') . ' — #' . $maint['Maintenance_ID'] . ' (' . $maint['Status'] . ')'); ?>
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">New Status</label>
              <select name="status" class="form-select form-select-sm" required>
                <option value="">Select Status</option>
                <?php echo DropdownHelper::renderOptions('maintenance_status'); ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">End Date <span class="text-muted fw-normal" style="font-size:0.9em;">(optional)</span></label>
              <input type="date" name="end_date" class="form-control form-control-sm">
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <a href="?url=maintenance/index" class="btn btn-sm btn-light"><i class="fas fa-times"></i> Cancel</a>
              <button type="submit" name="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save me-1"></i> Update Status
              </button>
            </div>
          </form>
      </div>
    </div>
  </div>
</div>
