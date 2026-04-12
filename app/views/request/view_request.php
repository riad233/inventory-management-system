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
  .btn-action-approve {
    background-color: #27ae60;
    color: white;
  }
  .btn-action-approve:hover {
    background-color: #229954;
    text-decoration: none;
    color: white;
  }
  .btn-action-reject {
    background-color: #e74c3c;
    color: white;
  }
  .btn-action-reject:hover {
    background-color: #c0392b;
    text-decoration: none;
    color: white;
  }
  .btn-action-delete {
    background-color: #95a5a6;
    color: white;
  }
  .btn-action-delete:hover {
    background-color: #7f8c8d;
    text-decoration: none;
    color: white;
  }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg content-action-bar p-0">
  <div class="container-fluid">
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="btn btn-primary btn-sm" href="?url=request/create"><i class="fas fa-plus"></i> New Request</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid mt-4">
  <h2><i class="fas fa-clipboard"></i> Equipment Requests</h2>
  
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
</body>
</html>
