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
        <?php if(!empty($data['errors'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-1">
                <?php foreach($data['errors'] as $msg): ?><li><?php echo e($msg); ?></li><?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
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
              <label class="form-label">Preferred Vendor <span class="text-muted fw-normal" style="font-size:0.9em;">(optional)</span></label>
              <select name="vendor_id" class="form-select form-select-sm">
                <option value="">Select Vendor</option>
                <?php if(!empty($data['vendors'])): ?>
                  <?php foreach($data['vendors'] as $vendor): ?>
                    <option value="<?php echo e($vendor['Vendor_ID']); ?>"
                      <?php echo ($req['Vendor_ID'] == $vendor['Vendor_ID']) ? 'selected' : ''; ?>>
                      <?php echo e($vendor['Vendor_Name']); ?>
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
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
