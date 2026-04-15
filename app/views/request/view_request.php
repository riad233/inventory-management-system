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
</head>
<body>
<div class="container-fluid mt-4">
  <div class="page-title"><i class="fas fa-clipboard"></i> Equipment Requests</div>

  <nav class="navbar navbar-expand-lg p-0" style="margin-bottom: 1rem;">
    <div class="container-fluid">
      <div class="collapse navbar-collapse justify-content-end gap-2">
        <div style="margin-right: 1rem;">
          <input type="text" id="requestSearchInput" data-search-table="requestTable" placeholder="Search requests..." style="padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px;">
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
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center py-4">No requests found</td>
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
