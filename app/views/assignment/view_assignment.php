<?php
// Session already started in layout
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Assignments - IMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="css/list-actions.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid mt-4">
  <div class="page-title"><i class="fas fa-hand-holding-box"></i> Asset Assignments</div>

  <nav class="navbar navbar-expand-lg content-action-bar p-0">
    <div class="container-fluid">
      <div class="collapse navbar-collapse justify-content-end gap-2">
        <div class="action-search">
          <input type="text" id="assignmentSearchInput" data-search-table="assignmentTable" placeholder="Search assignments...">
        </div>
        <ul class="navbar-nav">
          <li class="nav-item"><a class="btn btn-primary btn-sm" href="?url=assignment/assign"><i class="fas fa-plus"></i> Assign Asset</a></li>
          <li class="nav-item"><a class="btn btn-outline-secondary btn-sm" href="?url=assignment/returnAsset"><i class="fas fa-undo"></i> Return Asset</a></li>
        </ul>
      </div>
    </div>
  </nav>
  
  <?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php echo e($_GET['msg']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="card shadow">
    <div class="card-body">
      <table class="table table-striped table-hover" id="assignmentTable">
        <thead class="table-dark">
          <tr>
            <th>Assignment ID</th>
            <th>Asset</th>
            <th>Employee</th>
            <th>Assigned Date</th>
            <th>Expected Return</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if(isset($data['assignments']) && is_array($data['assignments'])): ?>
            <?php foreach($data['assignments'] as $assign): ?>
              <tr>
                <td><?php echo e($assign['Assignment_ID']); ?></td>
                <td><?php echo e($assign['Asset_Name'] ?? 'N/A'); ?></td>
                <td><?php echo e($assign['Name'] ?? 'N/A'); ?></td>
                <td><?php echo e($assign['Assigned_Date']); ?></td>
                <td><?php echo e($assign['Expected_Return_Date']); ?></td>
                <td>
                  <?php if($assign['Actual_Return_Date'] == null): ?>
                    <span class="badge bg-success">Assigned</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Returned</span>
                  <?php endif; ?>
                </td>
                <td>
                  <form method="post" action="?url=assignment/delete/<?php echo e($assign['Assignment_ID']); ?>" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-action btn-action-delete" onclick="return confirm('Delete this assignment?')" title="Delete">
                      <i class="fas fa-trash"></i> Delete
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center py-4">No assignments found</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/table-search.js"></script>
<script src="js/list-search-init.js"></script>
</body>
</html>
