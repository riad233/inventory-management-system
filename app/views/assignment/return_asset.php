<?php
// Session already started in layout
require_once __DIR__ . '/../../../config/dropdown_helper.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Return Asset - IMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-success text-white">
          <h5 class="mb-0"><i class="fas fa-undo"></i> Return Asset</h5>
        </div>
        <div class="card-body">
          <form method="post" action="">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label class="form-label">Select Assignment</label>
              <select name="assignment_id" class="form-control" required>
                <option value="">Select Assignment</option>
                <?php if(isset($data['assignments']) && is_array($data['assignments'])): ?>
                  <?php foreach($data['assignments'] as $assign): ?>
                    <?php if($assign['Actual_Return_Date'] == null): ?>
                      <option value="<?php echo e($assign['Assignment_ID']); ?>">
                        <?php echo e(($assign['Asset_Name'] ?? '') . ' - ' . ($assign['Name'] ?? '')); ?>
                      </option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Condition on Return</label>
              <select name="condition" class="form-control" required>
                <option value="">Select Condition</option>
                <?php echo DropdownHelper::renderOptions('return_conditions'); ?>
              </select>
            </div>
            <div class="text-end">
              <button type="submit" name="submit" class="btn btn-success">
                <i class="fas fa-check"></i> Return Asset
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
