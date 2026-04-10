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
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="?url=dashboard/index"><i class="fas fa-boxes\"></i> IMS</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="?url=request/create">New Request</a></li>
        <li class="nav-item"><a class="nav-link" href="?url=auth/logout">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid mt-4">
  <h2><i class="fas fa-clipboard"></i> Equipment Requests</h2>
  
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
                <td><?php echo $req['Request_ID']; ?></td>
                <td><?php echo $req['Name'] ?? 'N/A'; ?></td>
                <td><?php echo $req['Equipment_Type']; ?></td>
                <td><?php echo $req['Request_Date']; ?></td>
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
                    <a href="?url=request/approve/<?php echo $req['Request_ID']; ?>" class="btn-action btn-action-approve" title="Approve">Approve</a>
                    <a href="?url=request/reject/<?php echo $req['Request_ID']; ?>" class="btn-action btn-action-reject" title="Reject">Reject</a>
                  <?php endif; ?>
                  <a href="?url=request/delete/<?php echo $req['Request_ID']; ?>" class="btn-action btn-action-delete" onclick="return confirm('Delete this request?')" title="Delete">Delete</a>
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
