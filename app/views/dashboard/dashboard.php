<!-- Breadcrumb -->
<div class="breadcrumb-nav">
  <a href="?url=home/index">Home</a> / Dashboard
</div>

<!-- Page Title -->
<div class="page-title">
  <i class="fas fa-tachometer-alt"></i> Dashboard
</div>

<!-- Alerts -->
<?php if(isset($_GET['msg'])): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo $_GET['msg']; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<!-- Statistics Section -->
<div class="row mb-4">
  <div class="col-md-6 col-lg-3 mb-3">
    <div class="table-container">
      <div style="text-align: center; padding: 20px;">
        <div style="font-size: 32px; color: #007bff;"><i class="fas fa-cube"></i></div>
        <h3 style="font-size: 2em; color: #007bff; margin: 10px 0;">
          <?php echo isset($data['total_assets']) ? $data['total_assets'] : 0; ?>
        </h3>
        <p style="color: #6c757d; margin: 0;">Total Assets</p>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3 mb-3">
    <div class="table-container">
      <div style="text-align: center; padding: 20px;">
        <div style="font-size: 32px; color: #28a745;"><i class="fas fa-check-circle"></i></div>
        <h3 style="font-size: 2em; color: #28a745; margin: 10px 0;">
          <?php echo isset($data['total_assigned']) ? $data['total_assigned'] : 0; ?>
        </h3>
        <p style="color: #6c757d; margin: 0;">Assigned</p>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3 mb-3">
    <div class="table-container">
      <div style="text-align: center; padding: 20px;">
        <div style="font-size: 32px; color: #ffc107;"><i class="fas fa-hand-holding-box"></i></div>
        <h3 style="font-size: 2em; color: #ffc107; margin: 10px 0;">
          <?php echo isset($data['total_pending']) ? $data['total_pending'] : 0; ?>
        </h3>
        <p style="color: #6c757d; margin: 0;">Maintenance</p>
      </div>
    </div>
  </div>

  <div class="col-md-6 col-lg-3 mb-3">
    <div class="table-container">
      <div style="text-align: center; padding: 20px;">
        <div style="font-size: 32px; color: #dc3545;"><i class="fas fa-exclamation-circle"></i></div>
        <h3 style="font-size: 2em; color: #dc3545; margin: 10px 0;">
          <?php echo isset($data['total_requests']) ? $data['total_requests'] : 0; ?>
        </h3>
        <p style="color: #6c757d; margin: 0;">Requests</p>
      </div>
    </div>
  </div>
</div>

<!-- Welcome Message -->
<div class="table-container">
  <h5><i class="fas fa-info-circle"></i> Welcome, <?php echo $_SESSION['username']; ?></h5>
  <p>You are logged in to the Inventory Management System. Use the sidebar or top navigation to manage your assets, assignments, maintenance records, and vendors.</p>
</div>

<!-- Recent Data Section -->
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
