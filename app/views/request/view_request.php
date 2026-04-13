<?php
// Session already started in layout
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Requests - IMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="css/list-actions.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid mt-4">
  <div class="page-title"><i class="fas fa-clipboard"></i> Equipment Requests</div>

  <nav class="navbar navbar-expand-lg content-action-bar p-0">
    <div class="container-fluid">
      <div class="collapse navbar-collapse justify-content-end gap-2">
        <div class="action-search">
          <input type="text" id="requestSearchInput" data-search-table="requestTable" placeholder="Search requests...">
        </div>
        <ul class="navbar-nav">
          <li class="nav-item"><a class="btn btn-primary btn-sm" href="?url=request/create"><i class="fas fa-plus"></i> New Request</a></li>
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
      <table class="table table-striped table-hover" id="requestTable">
        <thead class="table-dark">
          <tr>
            <th>Request ID</th>
            <th>Employee</th>
            <th>Equipment Type</th>
            <th>Request Date</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if(isset($data['requests']) && is_array($data['requests'])): ?>
            <?php foreach($data['requests'] as $req): ?>
              <tr>
                <td><?php echo e($req['Request_ID']); ?></td>
                <td><?php echo e($req['Name'] ?? 'N/A'); ?></td>
                <td><?php echo e($req['Equipment_Type']); ?></td>
                <td><?php echo e($req['Request_Date']); ?></td>
                <td>
                  <?php if($req['Status'] == 'Pending'): ?>
                    <span class="badge bg-warning">Pending</span>
                  <?php elseif($req['Status'] == 'Approved'): ?>
                    <span class="badge bg-success">Approved</span>
                  <?php else: ?>
                    <span class="badge bg-danger">Rejected</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if($req['Status'] == 'Pending'): ?>
                    <a href="?url=request/edit/<?php echo e($req['Request_ID']); ?>" class="btn-action btn-action-edit" title="Edit">Edit</a>
                    <form method="post" action="?url=request/approve/<?php echo e($req['Request_ID']); ?>" style="display:inline;">
                      <?php echo csrf_field(); ?>
                      <button type="submit" class="btn-action btn-action-approve" title="Approve">Approve</button>
                    </form>
                    <form method="post" action="?url=request/reject/<?php echo e($req['Request_ID']); ?>" style="display:inline;">
                      <?php echo csrf_field(); ?>
                      <button type="submit" class="btn-action btn-action-reject" title="Reject">Reject</button>
                    </form>
                  <?php endif; ?>
                  <form method="post" action="?url=request/delete/<?php echo e($req['Request_ID']); ?>" style="display:inline;">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn-action btn-action-delete" onclick="return confirm('Delete this request?')" title="Delete">Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center py-4">No requests found</td>
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
