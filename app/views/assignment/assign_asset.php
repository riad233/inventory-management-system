<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Assign Asset - IMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="?url=dashboard/index"><i class="fas fa-boxes"></i> IMS</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="?url=assignment/index">Back to Assignments</a></li>
        <li class="nav-item"><a class="nav-link" href="?url=auth/logout">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-primary text-white">
          <h5 class="mb-0"><i class="fas fa-hand-holding-box"></i> Assign Asset</h5>
        </div>
        <div class="card-body">
          <form method="post" action="">
            <div class="mb-3">
              <label class="form-label">Asset</label>
              <select name="asset_id" class="form-control" required>
                <option>Select Asset</option>
                <?php if(isset($data['assets']) && is_array($data['assets'])): ?>
                  <?php foreach($data['assets'] as $asset): ?>
                    <option value="<?php echo $asset['Asset_ID']; ?>"><?php echo $asset['Asset_Name']; ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Employee</label>
              <select name="user_id" class="form-control" required>
                <option>Select Employee</option>
                <?php if(isset($data['employees']) && is_array($data['employees'])): ?>
                  <?php foreach($data['employees'] as $emp): ?>
                    <option value="<?php echo $emp['User_ID']; ?>"><?php echo $emp['Name']; ?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Department</label>
              <input type="number" name="dept_id" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Expected Return Date</label>
              <input type="date" name="exp_return_date" class="form-control" required>
            </div>
            <div class="text-end">
              <button type="submit" name="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Assign
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
