<?php
require_once __DIR__ . '/../../../config/dropdown_helper.php';
?>

<div class="list-page-header">
    <h2><i class="fas fa-undo"></i> Return Asset</h2>
    <div class="list-header-actions">
        <a href="?url=assignment/index" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="row justify-content-center">
  <div class="col-md-7 col-lg-5">
    <div class="form-page-card">
      <div class="form-page-card-header" style="background:#f0fdf4;">
        <i class="fas fa-undo" style="color:#15803d;"></i>
        <h5>Return Asset</h5>
      </div>
      <div class="card-body p-4">
          <form method="post" action="">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label class="form-label">Select Assignment</label>
              <select name="assignment_id" class="form-select form-select-sm" required>
                <option value="">Select Assignment</option>
                <?php if(isset($data['assignments']) && is_array($data['assignments'])): ?>
                  <?php foreach($data['assignments'] as $assign): ?>
                    <?php if($assign['Actual_Return_Date'] == null): ?>
                      <option value="<?php echo e($assign['Assignment_ID']); ?>">
                        <?php echo e(($assign['Asset_Name'] ?? '') . ' — ' . ($assign['Name'] ?? '')); ?>
                      </option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Condition on Return</label>
              <select name="condition" class="form-select form-select-sm" required>
                <option value="">Select Condition</option>
                <?php echo DropdownHelper::renderOptions('return_conditions'); ?>
              </select>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <a href="?url=assignment/index" class="btn btn-sm btn-light"><i class="fas fa-times"></i> Cancel</a>
              <button type="submit" name="submit" class="btn btn-success btn-sm">
                <i class="fas fa-check me-1"></i> Return Asset
              </button>
            </div>
          </form>
      </div>
    </div>
  </div>
</div>
