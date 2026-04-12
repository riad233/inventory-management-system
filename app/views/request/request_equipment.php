<?php
// Session already started in layout
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Request Equipment- IMS</title>
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
          <h5 class="mb-0"><i class="fas fa-clipboard"></i> Request Equipment</h5>
        </div>
        <div class="card-body">
          <form method="post" action="">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label class="form-label">Employee</label>
              <select name="user_id" class="form-control" required>
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
              <select name="equipment_type" class="form-control" required>
                <option>Computer</option>
                <option>Printer</option>
                <option>Monitor</option>
                <option>Keyboard/Mouse</option>
                <option>Other</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control" rows="4" required></textarea>
            </div>
            <div class="text-end">
              <button type="submit" name="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Submit Request
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
