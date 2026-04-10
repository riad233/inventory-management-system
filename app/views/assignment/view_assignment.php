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
<style>
  .btn-action {
    padding: 6px 10px;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.85em;
    font-weight: 500;
    text-decoration: none;
  }
  .btn-action-delete {
    background-color: #e74c3c;
    color: white;
  }
  .btn-action-delete:hover {
    background-color: #c0392b;
    text-decoration: none;
    color: white;
  }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="?url=dashboard/index"><i class="fas fa-boxes"></i> IMS</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="?url=assignment/assign">Assign Asset</a></li>
        <li class="nav-item"><a class="nav-link" href="?url=assignment/returnAsset">Return Asset</a></li>
        <li class="nav-item"><a class="nav-link" href="?url=auth/logout">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid mt-4">
  <h2><i class="fas fa-hand-holding-box"></i> Asset Assignments</h2>
  
  <?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php echo $_GET['msg']; ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="card shadow">
    <div class="card-body">
      <table class="table table-striped table-hover">
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
                <td><?php echo $assign['Assignment_ID']; ?></td>
                <td><?php echo $assign['Asset_Name'] ?? 'N/A'; ?></td>
                <td><?php echo $assign['Name'] ?? 'N/A'; ?></td>
                <td><?php echo $assign['Assigned_Date']; ?></td>
                <td><?php echo $assign['Expected_Return_Date']; ?></td>
                <td>
                  <?php if($assign['Actual_Return_Date'] == null): ?>
                    <span class="badge bg-success">Assigned</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Returned</span>
                  <?php endif; ?>
                </td>
                <td>
                  <a href="?url=assignment/delete/<?php echo $assign['Assignment_ID']; ?>" class="btn-action btn-action-delete" onclick="return confirm('Delete this assignment?')" title="Delete">
                    <i class="fas fa-trash"></i> Delete
                  </a>
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
</body>
</html>
