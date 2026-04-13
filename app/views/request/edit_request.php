<?php
// Session already started in layout
require_once __DIR__ . '/../../../config/dropdown_helper.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Equipment Request - IMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg content-action-bar p-0">
  <div class="container-fluid">
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="btn btn-outline-secondary btn-sm" href="?url=request/index"><i class="fas fa-arrow-left"></i> Back to Requests</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Equipment Request</h5>
        </div>
        <div class="card-body">
            <?php if(!isset($data['request']) || empty($data['request'])): ?>
                <div class="alert alert-danger">Request not found!</div>
                <a href="?url=request/index" class="btn btn-secondary">Go Back</a>
            <?php else: $req = $data['request']; ?>
          <form method="post" action="?url=request/edit/<?php echo e($req['Request_ID']); ?>">
            <?php echo csrf_field(); ?>
            
            <div class="mb-3">
              <label class="form-label">Employee</label>
              <input type="text" class="form-control" value="<?php 
                  foreach($data['employees'] as $emp) {
                      if($emp['User_ID'] == $req['User_ID']) echo e($emp['Name']);
                  }
              ?>" disabled>
              <small class="text-muted">Employee cannot be changed once requested.</small>
            </div>
            <div class="mb-3">
              <label class="form-label">Equipment Type</label>
              <select name="equipment_type" class="form-control" required>
                <option value="">Select Equipment Type</option>
                <?php echo DropdownHelper::renderOptions('asset_categories', $req['Equipment_Type']); ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control" rows="4" required><?php echo e($req['Description']); ?></textarea>
            </div>
            <div class="text-end">
              <button type="submit" name="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Request
              </button>
            </div>
          </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
