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
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="img/ims.png" alt="IMS Logo" class="sidebar-logo">
            <h4>IMS</h4>
            <small>Inventory Management System</small>
        </div>

        <ul class="sidebar-nav">
            <li>
                <a href="?url=dashboard/index" class="<?php echo (strpos($_GET['url'] ?? '', 'dashboard') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="?url=asset/index" class="<?php echo (strpos($_GET['url'] ?? '', 'asset') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-cube"></i> Assets
                </a>
            </li>
            <li>
                <a href="?url=product/index" class="<?php echo (strpos($_GET['url'] ?? '', 'product') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-box-open"></i> Products
                </a>
            </li>
            <li>
                <a href="?url=assignment/index" class="<?php echo (strpos($_GET['url'] ?? '', 'assignment') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-exchange-alt"></i> Assignments
                </a>
            </li>
            <li>
                <a href="?url=maintenance/index" class="<?php echo (strpos($_GET['url'] ?? '', 'maintenance') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-tools"></i> Maintenance
                </a>
            </li>
            <li>
                <a href="?url=employee/index" class="<?php echo (strpos($_GET['url'] ?? '', 'employee') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Employees
                </a>
            </li>
            <li>
                <a href="?url=vendor/index" class="<?php echo (strpos($_GET['url'] ?? '', 'vendor') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-store"></i> Vendors
                </a>
            </li>
            <li>
                <a href="?url=stock/index" class="<?php echo (strpos($_GET['url'] ?? '', 'stock') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-warehouse"></i> Stock
                </a>
            </li>
            <li>
                <a href="?url=request/index" class="<?php echo (strpos($_GET['url'] ?? '', 'request') !== false) ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt"></i> Requests
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <div class="menu-section">
                <ul class="top-navbar-nav">
                    <li><a href="?url=dashboard/index"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                    <li><a href="?url=asset/index"><i class="fas fa-boxes"></i> Assets</a></li>
                    <li><a href="?url=product/index"><i class="fas fa-box-open"></i> Products</a></li>
                    <li><a href="?url=assignment/index"><i class="fas fa-exchange-alt"></i> Assignments</a></li>
                    <li><a href="?url=maintenance/index"><i class="fas fa-wrench"></i> Maintenance</a></li>
                    <li><a href="?url=employee/index"><i class="fas fa-users"></i> Employees</a></li>
                    <li><a href="?url=vendor/index"><i class="fas fa-building"></i> Vendors</a></li>
                    <li><a href="?url=stock/index"><i class="fas fa-warehouse"></i> Stock</a></li>
                </ul>
            </div>
            <div class="top-navbar-right">
                
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
</body>
</html>
