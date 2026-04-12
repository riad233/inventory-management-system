<?php
// Session already started in layout
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Assets - IMS</title>
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
  .btn-action-edit {
    background-color: #3498db;
    color: white;
  }
  .btn-action-edit:hover {
    background-color: #2980b9;
    text-decoration: none;
    color: white;
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
        <li class="nav-item"><a class="nav-link" href="?url=asset/add">Add Asset</a></li>
        <li class="nav-item"><a class="nav-link" href="?url=auth/logout">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid mt-4">
  <h2><i class="fas fa-cube"></i> Assets</h2>
  
  <?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php echo e($_GET['msg']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="card shadow">
    <div class="card-body">
      <table class="table table-striped table-hover">
        <thead class="table-dark">
          <tr>
            <th>Asset ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Brand</th>
            <th>Serial #</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if(isset($data['assets']) && is_array($data['assets'])): ?>
            <?php foreach($data['assets'] as $asset): ?>
              <tr>
                <td><?php echo e($asset['Asset_ID']); ?></td>
                <td><?php echo e($asset['Asset_Name']); ?></td>
                <td><?php echo e($asset['Category']); ?></td>
                <td><?php echo e($asset['Brand']); ?></td>
                <td><?php echo e($asset['Serial_Number']); ?></td>
                <td><span class="badge bg-info"><?php echo e($asset['Status']); ?></span></td>
                <td>
                  <a href="?url=asset/edit/<?php echo e($asset['Asset_ID']); ?>" class="btn-action btn-action-edit" title="Edit">
                    <i class="fas fa-edit"></i> Edit
                  </a>
                  <form method="post" action="?url=asset/delete/<?php echo e($asset['Asset_ID']); ?>" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-action btn-action-delete" onclick="return confirm('Delete this asset?')" title="Delete">
                      <i class="fas fa-trash"></i> Delete
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="7" class="text-center py-4">No assets found</td>
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
