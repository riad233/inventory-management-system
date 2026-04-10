<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - IMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
  body {
    background-color: #f5f5f5;
  }
  .card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
  }
  .stat-card {
    text-align: center;
    padding: 20px;
  }
  .stat-card h3 {
    font-size: 32px;
    color: #007bff;
    margin: 0;
  }
  .stat-card p {
    margin: 0;
    color: #6c757d;
  }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="?url=dashboard/index"><i class="fas fa-boxes"></i> IMS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="?url=asset/index">Assets</a></li>
        <li class="nav-item"><a class="nav-link" href="?url=assignment/index">Assignments</a></li>
        <li class="nav-item"><a class="nav-link" href="?url=maintenance/index">Maintenance</a></li>
        <li class="nav-item"><a class="nav-link" href="?url=vendor/index">Vendors</a></li>
        <li class="nav-item"><a class="nav-link" href="?url=request/index">Requests</a></li>
        <li class="nav-item"><a class="nav-link" href="?url=auth/logout">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid mt-4">
  <div class="row">
    <div class="col-md-12">
      <h1>Dashboard</h1>
      <p>Welcome, <?php echo $_SESSION['username']; ?></p>
    </div>
  </div>

  <?php if(isset($_GET['msg'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php echo $_GET['msg']; ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div class="row mt-4">
    <div class="col-md-3">
      <div class="card stat-card">
        <div style="font-size: 32px; color: #007bff;"><i class="fas fa-cube"></i></div>
        <h3><?php echo isset($data['total_assets']) ? $data['total_assets'] : 0; ?></h3>
        <p>Total Assets</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card">
        <div style="font-size: 32px; color: #28a745;"><i class="fas fa-hand-holding-box"></i></div>
        <h3><?php echo isset($data['total_pending']) ? $data['total_pending'] : 0; ?></h3>
        <p>Pending Returns</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card">
        <div style="font-size: 32px; color: #ffc107;"><i class="fas fa-tools"></i></div>
        <h3><?php echo isset($data['total_maintenance']) ? $data['total_maintenance'] : 0; ?></h3>
        <p>Maintenance</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card">
        <div style="font-size: 32px; color: #dc3545;"><i class="fas fa-check-circle"></i></div>
        <h3><?php echo isset($data['total_assignments']) ? $data['total_assignments'] : 0; ?></h3>
        <p>Total Assignments</p>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-6">
      <h3>Recent Assets</h3>
      <div class="card">
        <div class="card-body">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Asset Name</th>
                <th>Category</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if(isset($data['assets']) && is_array($data['assets'])): ?>
                <?php foreach(array_slice($data['assets'], 0, 5) as $asset): ?>
                  <tr>
                    <td><?php echo $asset['Asset_Name']; ?></td>
                    <td><?php echo $asset['Category']; ?></td>
                    <td><span class="badge bg-info"><?php echo $asset['Status']; ?></span></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="3" class="text-center">No assets found</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <h3>Recent Assignments</h3>
      <div class="card">
        <div class="card-body">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Asset</th>
                <th>Employee</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if(isset($data['assignments']) && is_array($data['assignments'])): ?>
                <?php foreach(array_slice($data['assignments'], 0, 5) as $assign): ?>
                  <tr>
                    <td><?php echo $assign['Asset_Name'] ?? 'N/A'; ?></td>
                    <td><?php echo $assign['Name'] ?? 'N/A'; ?></td>
                    <td>
                      <?php if($assign['Actual_Return_Date'] == null): ?>
                        <span class="badge bg-success">Assigned</span>
                      <?php else: ?>
                        <span class="badge bg-secondary">Returned</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="3" class="text-center">No assignments found</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
