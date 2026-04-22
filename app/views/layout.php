<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['title']) ? $data['title'] : 'IMS - Inventory Management System'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/layout.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar Container -->
    <div class="sidebar" id="sidebar">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <h5 class="sidebar-title">
                <i class="fas fa-cube"></i> IMS
            </h5>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="sidebar-nav">
            <a href="?url=dashboard/index" class="nav-item <?php echo (strpos($_GET['url'] ?? '', 'dashboard') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span class="nav-label">Dashboard</span>
            </a>
            <a href="?url=asset/index" class="nav-item <?php echo (strpos($_GET['url'] ?? '', 'asset') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-boxes"></i>
                <span class="nav-label">Assets</span>
            </a>
            <a href="?url=assignment/index" class="nav-item <?php echo (strpos($_GET['url'] ?? '', 'assignment') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-exchange-alt"></i>
                <span class="nav-label">Assignments</span>
            </a>
            <a href="?url=maintenance/index" class="nav-item <?php echo (strpos($_GET['url'] ?? '', 'maintenance') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-wrench"></i>
                <span class="nav-label">Maintenance</span>
            </a>
            <a href="?url=employee/index" class="nav-item <?php echo (strpos($_GET['url'] ?? '', 'employee') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span class="nav-label">Employees</span>
            </a>
            <a href="?url=vendor/index" class="nav-item <?php echo (strpos($_GET['url'] ?? '', 'vendor') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-building"></i>
                <span class="nav-label">Vendors</span>
            </a>
            <a href="?url=request/index" class="nav-item <?php echo (strpos($_GET['url'] ?? '', 'request') === 0) ? 'active' : ''; ?>">
                <i class="fas fa-paper-plane"></i>
                <span class="nav-label">Requests</span>
            </a>
        </nav>
    </div>

    <!-- Sidebar Toggle Button - ALWAYS VISIBLE -->
    <button class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Toggle Sidebar">
        <i class="fas fa-chevron-left"></i>
    </button>

    <!-- Sidebar Overlay (Mobile Only) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="menu-section">
                <ul class="top-navbar-nav">
                    <li><a href="?url=dashboard/index"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                    <li><a href="?url=asset/index"><i class="fas fa-boxes"></i> Assets</a></li>
                    <li><a href="?url=product/index"><i class="fas fa-box-open"></i> Products</a></li>
                    <li><a href="?url=assignment/index"><i class="fas fa-exchange-alt"></i> Assignments</a></li>
                    <li><a href="?url=maintenance/index"><i class="fas fa-wrench"></i> Maintenance</a></li>
                    <li><a href="?url=employee/index"><i class="fas fa-users"></i> Employees</a></li>
                    <li><a href="?url=vendor/index"><i class="fas fa-building"></i> Vendors</a></li>
                </ul>
            </div>
            <div class="top-navbar-right">
                <!-- Stock Alerts Bell -->
                <?php if (isset($data['stock_alerts']) && ($data['stock_alerts']['total_low'] > 0 || $data['stock_alerts']['total_out'] > 0)): ?>
                <div class="stock-alerts-dropdown">
                    <div class="alert-badge alert-badge-danger" data-bs-toggle="dropdown" title="Stock Alerts">
                        <i class="fas fa-bell"></i>
                        <span class="badge"><?php echo e($data['stock_alerts']['total_out'] + $data['stock_alerts']['total_low']); ?></span>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width: 350px;">
                        <li><h6 class="dropdown-header"><i class="fas fa-exclamation-circle"></i> Stock Alerts</h6></li>
                        
                        <?php if ($data['stock_alerts']['total_out'] > 0): ?>
                        <li><h6 class="dropdown-header" style="font-size: 0.85rem; color: #dc3545;">🚨 Out of Stock (Qty < 3)</h6></li>
                        <?php foreach (array_slice($data['stock_alerts']['out_items'], 0, 3) as $item): ?>
                        <li><a class="dropdown-item" href="?url=dashboard/stockAlerts">
                            <small><i class="fas fa-times-circle" style="color: #dc3545;"></i> <?php echo e($item['Item_Name']); ?> (Qty: <?php echo e($item['Quantity']); ?>)</small>
                        </a></li>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <?php if ($data['stock_alerts']['total_low'] > 0): ?>
                        <li><h6 class="dropdown-header" style="font-size: 0.85rem; color: #ff9800;">⚠️ Low Stock (3-10)</h6></li>
                        <?php foreach (array_slice($data['stock_alerts']['low_items'], 0, 3) as $item): ?>
                        <li><a class="dropdown-item" href="?url=dashboard/stockAlerts">
                            <small><i class="fas fa-exclamation-triangle" style="color: #ff9800;"></i> <?php echo e($item['Item_Name']); ?> (Qty: <?php echo e($item['Quantity']); ?>)</small>
                        </a></li>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center" href="?url=dashboard/stockAlerts">View All Alerts</a></li>
                    </ul>
                </div>
                <?php endif; ?>
                
                <div class="user-menu">
                    <div class="user-info user-menu-toggle" id="userMenuToggle">
                    <div class="user-avatar user-avatar-gradient">
                        <?php
                        $username = isset($_SESSION['username']) ? (string)$_SESSION['username'] : '';
                        echo e($username !== '' ? strtoupper(substr($username, 0, 1)) : 'A');
                        ?>
                    </div>
                    <div class="user-meta">
                        <span class="user-name">
                            <?php echo e($username !== '' ? ucfirst($username) : 'Admin'); ?>
                        </span>
                        <small class="user-role">
                            <?php echo e(isset($_SESSION['role']) && !empty($_SESSION['role']) ? ucfirst((string)$_SESSION['role']) : 'Administrator'); ?>
                        </small>
                    </div>
                    <i class="fas fa-chevron-down user-caret"></i>
                    </div>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="?url=auth/changePassword" class="btn btn-outline-secondary btn-sm">Change Password</a>
                        <a href="?url=auth/logout" class="btn btn-outline-danger btn-sm">Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-content">
            <?php
            // Include the view file
            $viewPath = ROOT_PATH . "/app/views/" . $view . ".php";
            if (file_exists($viewPath)) {
                include $viewPath;
            } else {
                echo "<div class='alert alert-danger'>View not found: $view</div>";
            }
            ?>
        </div>
        
        <!-- Footer -->
        <div class="layout-footer">
            <p class="layout-footer-text">&copy; 2026 Inventory Management System. All rights reserved.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/table-search.js"></script>
    <script src="js/list-search-init.js"></script>
    <script src="js/layout.js"></script>
    
    <!-- Stock Alert Notifications -->
    <div id="notificationContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>
    
    <script>
        // Check for stock alerts and show notifications
        <?php if (isset($data['stock_alerts'])): ?>
            const stockAlerts = <?php echo json_encode($data['stock_alerts']); ?>;
            
            function showNotification(title, message, type = 'warning', icon = 'fas fa-bell') {
                const container = document.getElementById('notificationContainer');
                const toastId = 'alert-' + Date.now();
                
                const toastHtml = `
                    <div id="${toastId}" class="toast" role="alert" style="min-width: 350px; border-left: 5px solid ${type === 'danger' ? '#dc3545' : '#ff9800'};">
                        <div class="toast-header bg-light">
                            <i class="fas ${icon}" style="color: ${type === 'danger' ? '#dc3545' : '#ff9800'}; margin-right: 8px;"></i>
                            <strong class="me-auto">${title}</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body">
                            ${message}
                        </div>
                    </div>
                `;
                
                container.insertAdjacentHTML('beforeend', toastHtml);
                const toastElement = document.getElementById(toastId);
                const toast = new bootstrap.Toast(toastElement, { autohide: true, delay: 10000 });
                toast.show();
                
                // Remove from DOM after hiding
                toastElement.addEventListener('hidden.bs.toast', () => {
                    toastElement.remove();
                });
            }
            
            // Show emergency alert if out of stock items exist
            if (stockAlerts.total_out > 0) {
                showNotification(
                    '🚨 EMERGENCY ALERT',
                    `${stockAlerts.total_out} item(s) are out of stock (Qty < 3). Immediate action required! <br><a href="?url=dashboard/stockAlerts" style="color: #dc3545; font-weight: bold;">View & Request Now</a>`,
                    'danger',
                    'fa-bell'
                );
            }
            
            // Show low stock alert if applicable
            if (stockAlerts.total_low > 0 && stockAlerts.total_out === 0) {
                showNotification(
                    '⚠️ LOW STOCK WARNING',
                    `${stockAlerts.total_low} item(s) are running low (Qty 3-10). <br><a href="?url=dashboard/stockAlerts" style="color: #ff9800; font-weight: bold;">View & Request Now</a>`,
                    'warning',
                    'fa-exclamation-triangle'
                );
            }
        <?php endif; ?>
    </script>
</body>
</html>
