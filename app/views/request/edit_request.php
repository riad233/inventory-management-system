<?php
require_once __DIR__ . '/../../../config/dropdown_helper.php';
?>

<div class="list-page-header">
    <h2><i class="fas fa-edit"></i> Edit Equipment Request</h2>
    <div class="list-header-actions">
        <a href="?url=request/index" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<div class="row justify-content-center">
  <div class="col-md-7 col-lg-5">
    <div class="form-page-card">
      <div class="form-page-card-header" style="background:#eff6ff;">
        <i class="fas fa-edit" style="color:#1d4ed8;"></i>
        <h5>Edit Equipment Request</h5>
      </div>
      <div class="card-body p-4">
        <?php if(!isset($data['request']) || empty($data['request'])): ?>
          <div class="alert alert-danger">Request not found!</div>
          <a href="?url=request/index" class="btn btn-sm btn-secondary">Go Back</a>
        <?php else: $req = $data['request']; ?>
          <form method="post" action="?url=request/edit/<?php echo e($req['Request_ID']); ?>">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label class="form-label">Employee</label>
              <input type="text" class="form-control form-control-sm" value="<?php
                foreach($data['employees'] as $emp) {
                  if($emp['User_ID'] == $req['User_ID']) echo e($emp['Name']);
                }
              ?>" disabled>
              <small class="text-muted">Employee cannot be changed once requested.</small>
            </div>
            <div class="mb-3">
              <label class="form-label">Equipment Type</label>
              <select name="equipment_type" class="form-select form-select-sm" required>
                <option value="">Select Equipment Type</option>
                <?php echo DropdownHelper::renderOptions('asset_categories', $req['Equipment_Type']); ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control form-control-sm" rows="4" required><?php echo e($req['Description']); ?></textarea>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3">
              <a href="?url=request/index" class="btn btn-sm btn-light"><i class="fas fa-times"></i> Cancel</a>
              <button type="submit" name="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-save me-1"></i> Update Request
              </button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
