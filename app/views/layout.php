<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['title']) ? $data['title'] : 'IMS - Inventory Management System'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #2c3e50;
            position: fixed;
            left: 0;
            top: 0;
            overflow-y: auto;
            padding-top: 0;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            transform: translateX(0);
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .sidebar-brand {
            padding: 18px 20px;
            text-align: center;
            color: white;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            background: #1a252f;
            cursor: grab;
            user-select: none;
            transition: background 0.2s ease;
        }

        .sidebar-brand:active {
            cursor: grabbing;
            background: #0f1419;
        }

        .sidebar-brand h4 {
            margin: 0;
            font-weight: 700;
            font-size: 1.3em;
            letter-spacing: 0.5px;
        }

        .sidebar-brand small {
            opacity: 0.7;
            font-size: 0.8em;
            display: block;
            margin-top: 4px;
        }

        .sidebar-nav {
            list-style: none;
            padding: 10px 0;
            margin: 0;
        }

        .sidebar-nav li {
            margin: 0;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 18px;
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            transition: all 0.25s ease;
            border-left: 3px solid transparent;
            font-weight: 500;
            font-size: 0.95em;
        }

        .sidebar-nav a:hover {
            background-color: rgba(52, 152, 219, 0.15);
            color: white;
            border-left-color: #3498db;
            padding-left: 22px;
        }

        .sidebar-nav a.active {
            background-color: rgba(52, 152, 219, 0.25);
            color: white;
            border-left-color: #3498db;
        }

        .sidebar-nav i {
            width: 18px;
            text-align: center;
            flex-shrink: 0;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .main-content.full-width {
            margin-left: 0;
        }

        /* Toggle Button */
        .sidebar-toggle {
            display: none;
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 15px;
            cursor: pointer;
            font-size: 1.3em;
            transition: all 0.3s ease;
        }

        .sidebar-toggle:hover {
            background: #764ba2;
        }

        .sidebar-toggle:active {
            background: #2c3e50;
        }

        /* Action Buttons */
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

        .btn-action-delete {
            background-color: #e74c3c;
            color: white;
        }

        .btn-action-delete:hover {
            background-color: #c0392b;
            text-decoration: none;
            color: white;
        }

        /* Top Navigation */
        .top-navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
            flex-wrap: wrap;
            gap: 15px;
        }

        .menu-section {
            flex: 1;
            min-width: 300px;
        }

        .top-navbar-nav {
            list-style: none;
            display: flex;
            gap: 20px;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }

        .top-navbar-nav li {
            white-space: nowrap;
        }

        .top-navbar-nav a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95em;
        }

        .top-navbar-nav a:hover {
            color: #667eea;
        }

        .top-navbar-right {
            display: flex;
            gap: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
            font-size: 0.9em;
        }

        /* Page Content */
        .page-content {
            padding: 20px;
            flex: 1;
            overflow-x: hidden;
        }

        @media (min-width: 768px) {
            .page-content {
                padding: 30px;
            }
        }

        .breadcrumb-nav {
            margin-bottom: 20px;
            font-size: 0.9em;
            overflow-x: auto;
        }

        .breadcrumb-nav a {
            color: #667eea;
            text-decoration: none;
        }

        .breadcrumb-nav a:hover {
            text-decoration: underline;
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
            font-size: 1.5em;
            font-weight: 700;
            color: #333;
            flex-wrap: wrap;
        }

        @media (min-width: 768px) {
            .page-title {
                font-size: 1.8em;
            }
        }

        .page-title i {
            color: #667eea;
            flex-shrink: 0;
        }

        /* Table Styling */
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-bottom: 20px;
            overflow-x: auto;
        }

        @media (min-width: 768px) {
            .table-container {
                padding: 20px;
            }
        }

        .table-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 10px;
            flex-wrap: wrap;
        }

        .table-controls input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 100%;
            min-width: 200px;
            flex-grow: 1;
        }

        @media (min-width: 768px) {
            .table-controls input {
                width: auto;
                min-width: 300px;
            }
        }

        .table {
            margin: 0;
            font-size: 0.9em;
        }

        @media (min-width: 768px) {
            .table {
                font-size: 1em;
            }
        }

        .table thead {
            background-color: #f8f9fa;
            border-bottom: 2px solid #ddd;
        }

        .table th {
            padding: 12px 8px;
            font-weight: 600;
            color: #333;
            text-align: left;
        }

        @media (min-width: 768px) {
            .table th {
                padding: 15px;
            }
        }

        .table td {
            padding: 12px 8px;
            vertical-align: middle;
            border-bottom: 1px solid #eee;
        }

        @media (min-width: 768px) {
            .table td {
                padding: 15px;
            }
        }

        .table tbody tr:hover {
            background-color: #f9f9f9;
        }

        /* Responsive Table */
        @media (max-width: 768px) {
            .table thead {
                display: none;
            }

            .table tbody tr {
                display: block;
                border: 1px solid #ddd;
                margin-bottom: 10px;
                border-radius: 5px;
            }

            .table tbody td {
                display: block;
                text-align: right;
                padding: 10px;
                background: #f9f9f9;
                border: none;
            }

            .table tbody td::before {
                content: attr(data-label);
                float: left;
                font-weight: 600;
                color: #667eea;
            }
        }

        /* Badge Styling */
        .badge-success {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85em;
            display: inline-block;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #333;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85em;
            display: inline-block;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85em;
            display: inline-block;
        }

        /* Button Styling */
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85em;
            border-radius: 5px;
        }

        .btn-action {
            background-color: #17a2b8;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9em;
            white-space: nowrap;
        }

        @media (min-width: 768px) {
            .btn-action {
                padding: 8px 15px;
                font-size: 1em;
            }
        }

        .btn-action:hover {
            background-color: #138496;
            color: white;
            text-decoration: none;
        }

        /* Dashboard Cards */
        .dashboard-cards {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        @media (min-width: 576px) {
            .dashboard-cards {
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }
        }

        @media (min-width: 768px) {
            .dashboard-cards {
                grid-template-columns: 1fr 1fr 1fr 1fr;
            }
        }

        @media (min-width: 1200px) {
            .dashboard-cards {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Mobile Menu Toggle */
        @media (max-width: 991px) {
            .sidebar-toggle {
                display: inline-block;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            #sidebarOverlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            #sidebarOverlay.show {
                display: block;
                opacity: 1;
            }

            .top-navbar-nav {
                gap: 10px;
            }

            .top-navbar-nav a {
                font-size: 0.85em;
            }

            .user-info span {
                display: none;
            }

            .user-avatar {
                width: 30px;
                height: 30px;
                font-size: 0.8em;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: fixed;
                top: 0;
                left: 0;
                z-index: 1001;
                max-height: 80vh;
            }

            .sidebar-brand h4 {
                font-size: 1.2em;
            }

            .page-title {
                font-size: 1.3em;
            }

            .top-navbar {
                padding: 10px 15px;
                gap: 10px;
            }

            .menu-section {
                display: none;
            }

            .top-navbar-nav {
                flex-direction: column;
                gap: 5px;
            }

            .table-controls {
                flex-direction: column;
                align-items: stretch;
            }

            .table-controls input {
                width: 100%;
            }

            .btn-action {
                padding: 10px;
                font-size: 0.85em;
            }
        }

        /* Utility Classes */
        .d-none-mobile {
            display: none;
        }

        @media (min-width: 768px) {
            .d-none-mobile {
                display: block;
            }
        }

        .d-block-mobile {
            display: block;
        }

        @media (min-width: 768px) {
            .d-block-mobile {
                display: none;
            }
        }

        /* Scrollbar for tables */
        .table-container {
            -webkit-overflow-scrolling: touch;
        }

        /* Touch-friendly adjustments */
        @media (hover: none) {
            .btn-action {
                padding: 12px 16px;
            }

        }
        
        .layout-footer {
            background: #2c3e50;
            color: #fff;
            text-align: center;
            padding: 15px;
            font-size: 0.9em;
            margin-top: auto;
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="img/logo.png" alt="IMS Logo" style="width: 50px; height: 50px; border-radius: 10px; margin-bottom: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.5);">
            <h4>IMS</h4>
            <small>Inventory System</small>
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
                    <li><a href="?url=assignment/index"><i class="fas fa-exchange-alt"></i> Assignments</a></li>
                    <li><a href="?url=maintenance/index"><i class="fas fa-wrench"></i> Maintenance</a></li>
                    <li><a href="?url=employee/index"><i class="fas fa-users"></i> Employees</a></li>
                    <li><a href="?url=vendor/index"><i class="fas fa-building"></i> Vendors</a></li>
                </ul>
            </div>
            <div class="top-navbar-right">
                <div class="user-info" style="cursor: pointer; display: flex; align-items: center; gap: 10px;">
                    <div class="user-avatar" style="background: linear-gradient(135deg, #667eea, #764ba2); border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.2); width: 35px; height: 35px; border-radius: 50%; color: white; display: flex; justify-content: center; align-items: center; font-weight: bold;">
                        <?php echo isset($_SESSION['username']) && !empty($_SESSION['username']) ? strtoupper(substr($_SESSION['username'], 0, 1)) : 'A'; ?>
                    </div>
                    <div style="display: flex; flex-direction: column; line-height: 1.1;">
                        <span style="font-weight: 600; font-size: 14px; color: #333;">
                            <?php echo isset($_SESSION['username']) && !empty($_SESSION['username']) ? ucfirst($_SESSION['username']) : 'Admin'; ?>
                        </span>
                        <small style="color: #6c757d; font-size: 11px;">
                            <?php echo isset($_SESSION['role']) && !empty($_SESSION['role']) ? ucfirst($_SESSION['role']) : 'Administrator'; ?>
                        </small>
                    </div>
                    <i class="fas fa-chevron-down" style="font-size: 10px; color: #adb5bd; margin-left: 5px;"></i>
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
            <p style="margin: 0;">&copy; 2026 Inventory Management System. All rights reserved.</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile Sidebar Toggle with enhanced functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            // Helper function to close sidebar
            const closeSidebar = () => {
                if (sidebar) {
                    sidebar.classList.remove('show');
                    sidebar.classList.add('collapsed');
                }
                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove('show');
                }
            };

            // Helper function to open sidebar
            const openSidebar = () => {
                if (sidebar) {
                    sidebar.classList.add('show');
                    sidebar.classList.remove('collapsed');
                }
                if (sidebarOverlay) {
                    sidebarOverlay.classList.add('show');
                }
            };

            // Toggle sidebar on button click
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (sidebar.classList.contains('show')) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                });
            }

            // Close sidebar when clicking on overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', closeSidebar);
            }

            // Close sidebar when clicking on a link (except hash links)
            if (sidebar) {
                const sidebarLinks = sidebar.querySelectorAll('a');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        // Don't close if it's a dropdown toggle or hash link
                        if (!this.classList.contains('dropdown-toggle') && !this.href.includes('#')) {
                            closeSidebar();
                        }
                    });
                });
            }

            // Close sidebar on window resize if scrolling back to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth > 991) {
                    closeSidebar();
                }
            });

            // Prevent body scroll when sidebar is open
            const updateBodyScroll = () => {
                if (sidebar && sidebar.classList.contains('show')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = 'auto';
                }
            };

            if (sidebar) {
                sidebar.addEventListener('transitionend', updateBodyScroll);
            }

            // Draggable Sidebar Functionality
            let isDragging = false;
            let startX = 0;
            let startLeft = 0;
            const sidebarBrand = sidebar ? sidebar.querySelector('.sidebar-brand') : null;
            const mainContent = document.getElementById('mainContent');

            if (sidebarBrand && window.innerWidth > 991) {
                sidebarBrand.addEventListener('mousedown', function(e) {
                    isDragging = true;
                    startX = e.clientX;
                    startLeft = sidebar.offsetLeft;
                    sidebar.style.transition = 'none';
                    sidebarBrand.style.cursor = 'grabbing';
                });

                document.addEventListener('mousemove', function(e) {
                    if (!isDragging || !sidebar) return;
                    
                    const deltaX = e.clientX - startX;
                    const newLeft = Math.max(-250, Math.min(0, startLeft + deltaX));
                    
                    sidebar.style.left = newLeft + 'px';
                    mainContent.style.marginLeft = (250 + newLeft) + 'px';
                });

                document.addEventListener('mouseup', function() {
                    if (!isDragging) return;
                    isDragging = false;
                    
                    // Save position to localStorage
                    if (sidebar) {
                        const position = sidebar.offsetLeft;
                        localStorage.setItem('sidebarPosition', position);
                        sidebar.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
                        
                        // Snap to position
                        if (position < -125) {
                            sidebar.style.left = '-250px';
                            mainContent.style.marginLeft = '0';
                        } else {
                            sidebar.style.left = '0';
                            mainContent.style.marginLeft = '250px';
                        }
                    }
                    
                    sidebarBrand.style.cursor = 'grab';
                });

                // Load saved position on page load
                const savedPosition = localStorage.getItem('sidebarPosition');
                if (savedPosition) {
                    const pos = parseInt(savedPosition);
                    sidebar.style.left = pos + 'px';
                    mainContent.style.marginLeft = (250 + pos) + 'px';
                }
            }

            // Automatically close success alert messages after 5 seconds
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    if (window.bootstrap && bootstrap.Alert) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    } else {
                        alert.style.display = 'none';
                    }
                });
            }, 5000);
        });
    </script>
</body>
</html>
