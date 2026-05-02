<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['title']) ? $data['title'] : 'IMS - Inventory Management System'; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/layout.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar Container -->
    <div class="sidebar" id="sidebar">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <h5 class="sidebar-title">
                <i class="fas fa-cube"></i> IMS Admin
            </h5>
            <p class="sidebar-subtitle">Inventory Management System</p>
            <span class="role-badge">
                <?php echo e(isset($_SESSION['role']) && !empty($_SESSION['role']) ? ucfirst((string)$_SESSION['role']) : 'Admin'); ?>
            </span>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="sidebar-nav">
            <?php
            $currentUrl = $_GET['url'] ?? '';
            $role       = $_SESSION['role'] ?? '';
            $isSA       = ($role === 'SuperAdmin');

            // Load permission helper once (already required by Controller base)
            // For the view we can check DB inline via AuthorizationHelper::hasPermission()
            ?>

            <!-- Dashboard — everyone sees it -->
            <a href="?url=dashboard/index"
               class="nav-item <?php echo strpos($currentUrl, 'dashboard') === 0 ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span class="nav-label">Dashboard</span>
            </a>

            <!-- Assets — asset.view -->
            <?php if ($isSA || AuthorizationHelper::hasPermission('asset.view')): ?>
            <a href="?url=asset/index"
               class="nav-item <?php echo strpos($currentUrl, 'asset') === 0 ? 'active' : ''; ?>">
                <i class="fas fa-boxes"></i>
                <span class="nav-label">Assets</span>
            </a>
            <?php endif; ?>

            <!-- Assignments — assignment.view -->
            <?php if ($isSA || AuthorizationHelper::hasPermission('assignment.view')): ?>
            <a href="?url=assignment/index"
               class="nav-item <?php echo strpos($currentUrl, 'assignment') === 0 ? 'active' : ''; ?>">
                <i class="fas fa-exchange-alt"></i>
                <span class="nav-label">Assignments</span>
            </a>
            <?php endif; ?>

            <!-- Maintenance — maintenance.view -->
            <?php if ($isSA || AuthorizationHelper::hasPermission('maintenance.view')): ?>
            <a href="?url=maintenance/index"
               class="nav-item <?php echo strpos($currentUrl, 'maintenance') === 0 ? 'active' : ''; ?>">
                <i class="fas fa-wrench"></i>
                <span class="nav-label">Maintenance</span>
            </a>
            <?php endif; ?>

            <!-- Employees — employee.view -->
            <?php if ($isSA || AuthorizationHelper::hasPermission('employee.view')): ?>
            <a href="?url=employee/index"
               class="nav-item <?php echo strpos($currentUrl, 'employee') === 0 ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span class="nav-label">Employees</span>
            </a>
            <?php endif; ?>

            <!-- Vendors — vendor.view -->
            <?php if ($isSA || AuthorizationHelper::hasPermission('vendor.view')): ?>
            <a href="?url=vendor/index"
               class="nav-item <?php echo strpos($currentUrl, 'vendor') === 0 ? 'active' : ''; ?>">
                <i class="fas fa-building"></i>
                <span class="nav-label">Vendors</span>
            </a>
            <?php endif; ?>

            <!-- Requests — request.view -->
            <?php if ($isSA || AuthorizationHelper::hasPermission('request.view')): ?>
            <a href="?url=request/index"
               class="nav-item <?php echo strpos($currentUrl, 'request') === 0 ? 'active' : ''; ?>">
                <i class="fas fa-paper-plane"></i>
                <span class="nav-label">Requests</span>
            </a>
            <?php endif; ?>

            <!-- ── Admin Section ───────────────────────── -->
            <?php if (in_array($role, ['Admin', 'SuperAdmin'], true)
                      && (AuthorizationHelper::hasPermission('admin.panel')
                          || AuthorizationHelper::hasPermission('admin.users')
                          || $isSA)): ?>
            <hr class="sidebar-divider">
            <div class="sidebar-section-title">Administration</div>
            <?php if ($isSA || AuthorizationHelper::hasPermission('admin.panel')): ?>
            <a href="?url=admin/index"
               class="nav-item <?php echo strpos($currentUrl, 'admin/index') === 0 || $currentUrl === 'admin' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span class="nav-label">Control Panel</span>
            </a>
            <?php endif; ?>
            <?php if ($isSA || AuthorizationHelper::hasPermission('admin.users')): ?>
            <a href="?url=admin/users"
               class="nav-item <?php echo strpos($currentUrl, 'admin/users') === 0
                                        || strpos($currentUrl, 'admin/addUser') === 0
                                        || strpos($currentUrl, 'admin/editUser') === 0 ? 'active' : ''; ?>">
                <i class="fas fa-user-tie"></i>
                <span class="nav-label">User Management</span>
            </a>
            <?php endif; ?>
            <?php if ($isSA || AuthorizationHelper::hasPermission('admin.panel')): ?>
            <a href="?url=admin/logs"
               class="nav-item <?php echo strpos($currentUrl, 'admin/logs') === 0 ? 'active' : ''; ?>">
                <i class="fas fa-history"></i>
                <span class="nav-label">Activity Logs</span>
            </a>
            <?php endif; ?>
            <?php endif; ?>

            <!-- ── SuperAdmin Section ──────────────────── -->
            <?php if ($isSA): ?>
            <hr class="sidebar-divider">
            <div class="sidebar-section-title">SuperAdmin</div>
            <a href="?url=superadmin/index"
               class="nav-item <?php echo strpos($currentUrl, 'superadmin/index') === 0 || $currentUrl === 'superadmin' ? 'active' : ''; ?>">
                <i class="fas fa-shield-alt"></i>
                <span class="nav-label">SA Panel</span>
            </a>
            <a href="?url=superadmin/permissions"
               class="nav-item <?php echo strpos($currentUrl, 'superadmin/permissions') === 0 ? 'active' : ''; ?>">
                <i class="fas fa-key"></i>
                <span class="nav-label">Permissions</span>
            </a>
            <a href="?url=superadmin/users"
               class="nav-item <?php echo strpos($currentUrl, 'superadmin/users') === 0
                                        || strpos($currentUrl, 'superadmin/addUser') === 0
                                        || strpos($currentUrl, 'superadmin/editUser') === 0 ? 'active' : ''; ?>">
                <i class="fas fa-users-cog"></i>
                <span class="nav-label">All Users</span>
            </a>
            <?php endif; ?>
        </nav>

        <!-- Sidebar Footer with Logout -->
        <div class="sidebar-footer">
            <a href="?url=auth/logout" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span class="nav-label">Logout</span>
            </a>
        </div>
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
        
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="js/table-search.js"></script>
    <script src="js/list-search-init.js"></script>
    <script src="js/layout.js"></script>
</body>
</html>
