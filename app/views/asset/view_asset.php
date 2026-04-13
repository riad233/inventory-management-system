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
<link href="css/list-actions.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid mt-4">
  <div class="page-title"><i class="fas fa-cube"></i> Assets</div>

  <nav class="navbar navbar-expand-lg content-action-bar p-0">
    <div class="container-fluid">
      <div class="collapse navbar-collapse justify-content-end gap-2">
        <div class="action-search">
          <input type="text" id="assetSearchInput" data-search-table="assetTable" placeholder="Search assets...">
        </div>
        <ul class="navbar-nav">
          <li class="nav-item"><a class="btn btn-primary btn-sm" href="?url=asset/add"><i class="fas fa-plus"></i> Add Asset</a></li>
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
      <table class="table table-striped table-hover" id="assetTable">
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
                  <form method="post" action="?url=asset/delete/<?php echo e($asset['Asset_ID']); ?>" class="d-inline">
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
<script src="js/table-search.js"></script>
<script src="js/list-search-init.js"></script>
</body>
</html>
