<?php
// Session already started in layout
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
        <div class="card-header bg-success text-white">
          <h5 class="mb-0"><i class="fas fa-undo"></i> Return Asset</h5>
        </div>
        <div class="card-body">
          <form method="post" action="">
            <div class="mb-3">
              <label class="form-label">Select Assignment</label>
              <select name="assignment_id" class="form-control" required>
                <option value="">Select Assignment</option>
                <?php if(isset($data['assignments']) && is_array($data['assignments'])): ?>
                  <?php foreach($data['assignments'] as $assign): ?>
                    <?php if($assign['Actual_Return_Date'] == null): ?>
                      <option value="<?php echo $assign['Assignment_ID']; ?>">
                        <?php echo $assign['Asset_Name'] . ' - ' . $assign['Name']; ?>
                      </option>
                    <?php endif; ?>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Condition on Return</label>
              <select name="condition" class="form-control" required>
                <option>Good</option>
                <option>Fair</option>
                <option>Damaged</option>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
