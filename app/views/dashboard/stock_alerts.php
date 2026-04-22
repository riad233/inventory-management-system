<div class="stock-alerts">
  <div class="breadcrumb-nav">
    <a href="?url=dashboard/index">Dashboard</a> / Stock Alerts
  </div>

  <div class="alerts-header">
    <h1><i class="fas fa-bell"></i> Stock Alerts</h1>
    <p>Monitor inventory levels and stock warnings</p>
  </div>

  <div class="alerts-container">
    <div class="alert-section">
      <h3><i class="fas fa-exclamation-circle"></i> Out of Stock (Qty < 3)</h3>
      <div class="alert-list">
        <?php if(!empty($data['alerts']['out_items'])): ?>
          <?php foreach($data['alerts']['out_items'] as $item): ?>
            <div class="alert-item alert-danger">
              <div class="alert-icon"><i class="fas fa-times-circle"></i></div>
              <div class="alert-content">
                <div class="alert-title"><?php echo e($item['Item_Name']); ?></div>
                <div class="alert-detail">Current Quantity: <strong><?php echo e($item['Quantity']); ?></strong></div>
              </div>
              <a href="?url=asset/index" class="btn btn-sm btn-danger">View Assets</a>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="alert-empty">No out of stock items</div>
        <?php endif; ?>
      </div>
    </div>

    <div class="alert-section">
      <h3><i class="fas fa-exclamation-triangle"></i> Low Stock (Qty: 3-10)</h3>
      <div class="alert-list">
        <?php if(!empty($data['alerts']['low_items'])): ?>
          <?php foreach($data['alerts']['low_items'] as $item): ?>
            <div class="alert-item alert-warning">
              <div class="alert-icon"><i class="fas fa-exclamation-triangle"></i></div>
              <div class="alert-content">
                <div class="alert-title"><?php echo e($item['Item_Name']); ?></div>
                <div class="alert-detail">Current Quantity: <strong><?php echo e($item['Quantity']); ?></strong></div>
              </div>
              <a href="?url=asset/index" class="btn btn-sm btn-warning">View Assets</a>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="alert-empty">No low stock items</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<style>
.stock-alerts {
  padding: 20px;
}

.alerts-header {
  margin-bottom: 30px;
}

.alerts-header h1 {
  color: #333;
  margin-bottom: 5px;
}

.alerts-header p {
  color: #666;
  margin: 0;
}

.alerts-container {
  display: grid;
  grid-template-columns: 1fr;
  gap: 30px;
}

.alert-section {
  background: white;
  padding: 25px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.alert-section h3 {
  margin-bottom: 20px;
  color: #333;
  font-size: 1.2rem;
}

.alert-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.alert-item {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 15px;
  border-radius: 6px;
  border-left: 4px solid;
}

.alert-item.alert-danger {
  background: #ffe5e5;
  border-left-color: #dc3545;
  color: #721c24;
}

.alert-item.alert-warning {
  background: #fff8e5;
  border-left-color: #ff9800;
  color: #856404;
}

.alert-icon {
  font-size: 1.5rem;
  flex-shrink: 0;
}

.alert-content {
  flex: 1;
}

.alert-title {
  font-weight: bold;
  margin-bottom: 5px;
}

.alert-detail {
  font-size: 0.9rem;
  opacity: 0.9;
}

.alert-empty {
  text-align: center;
  padding: 20px;
  color: #666;
  background: #f8f9fa;
  border-radius: 6px;
}

@media (max-width: 768px) {
  .alert-item {
    flex-direction: column;
    align-items: flex-start;
  }

  .btn-sm {
    width: 100%;
  }
}
</style>
