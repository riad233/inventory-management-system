# Complete Sidebar Toggle Solution - Full Code Reference

## Overview

This document contains the complete corrected HTML, CSS, and JavaScript code for the sidebar toggle feature with a button that **always remains visible**.

---

## 1. HTML (app/views/layout.php) - Structure

```html
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
    <!-- ====== SIDEBAR CONTAINER ====== -->
    <div class="sidebar" id="sidebar">
        <!-- Sidebar Header/Branding -->
        <div class="sidebar-header">
            <h5 class="sidebar-title">
                <i class="fas fa-cube"></i> IMS
            </h5>
        </div>

        <!-- Sidebar Navigation Menu -->
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

    <!-- ====== TOGGLE BUTTON - ALWAYS VISIBLE ====== -->
    <!-- Positioned OUTSIDE sidebar with fixed positioning -->
    <!-- Slides from left: 260px (at sidebar) to left: 0 (off-screen) -->
    <button class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Toggle Sidebar">
        <i class="fas fa-chevron-left"></i>
    </button>

    <!-- ====== SIDEBAR OVERLAY (Mobile Only) ====== -->
    <!-- Click to close sidebar on mobile devices -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ====== MAIN CONTENT ====== -->
    <div class="main-content" id="mainContent">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <!-- ... existing navbar content ... -->
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/table-search.js"></script>
    <script src="js/list-search-init.js"></script>
    <script src="js/layout.js"></script>
    
    <!-- Notifications Container -->
    <div id="notificationContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>
</body>
</html>
```

---

## 2. CSS (public/css/layout.css) - Styling

```css
/* ============================================
   RESET & GLOBAL STYLES
   ============================================ */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background-color: #f5f5f5;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    width: 100%;
}

/* ============================================
   SIDEBAR STYLES
   ============================================ */

.sidebar {
    /* Fixed positioning to viewport */
    position: fixed;
    left: 0;
    top: 0;
    width: 260px;
    height: 100vh;
    
    /* Dark gradient background */
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    color: white;
    
    /* Z-index layering */
    z-index: 1050;
    
    /* Content scrolling */
    overflow-y: auto;
    overflow-x: hidden;
    
    /* Smooth animation */
    transform: translateX(0);
    transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    
    /* Shadow for depth */
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
}

/* ✅ COLLAPSED STATE: Slide off-screen to the left */
.sidebar.collapsed {
    transform: translateX(-260px);
}

/* Sidebar header/branding */
.sidebar-header {
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    gap: 12px;
}

.sidebar-title {
    margin: 0;
    font-size: 1.3em;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
}

/* Sidebar navigation */
.sidebar-nav {
    display: flex;
    flex-direction: column;
    padding: 15px 0;
    gap: 3px;
}

/* Individual nav items */
.nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 18px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

/* Hover state */
.nav-item:hover {
    background: rgba(255, 255, 255, 0.08);
    color: white;
    border-left-color: #667eea;
}

/* Active state - current page */
.nav-item.active {
    background: rgba(102, 126, 234, 0.2);
    color: #a5f3fc;
    border-left-color: #667eea;
    font-weight: 600;
}

/* Icons in nav items */
.nav-item i {
    font-size: 1.1em;
    min-width: 20px;
    text-align: center;
    flex-shrink: 0;
}

/* Text labels in nav items */
.nav-label {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Custom scrollbar for sidebar */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* ============================================
   TOGGLE BUTTON - ALWAYS VISIBLE ✅
   ============================================ */

.sidebar-toggle-btn {
    /* ✅ FIXED positioning = Always visible in viewport */
    position: fixed;
    
    /* Position at sidebar edge, moves smoothly with sidebar */
    left: 260px;
    top: 50%;
    transform: translateY(-50%);
    
    /* Size and shape */
    width: 32px;
    height: 48px;
    
    /* Styling */
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-left: none;  /* No left border to blend with sidebar */
    color: rgba(255, 255, 255, 0.8);
    border-radius: 0 6px 6px 0;
    
    /* Interactive */
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    
    /* ✅ SMOOTH ANIMATION */
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    
    /* Z-index: above sidebar, below navbar */
    z-index: 1051;
    
    /* Reset button defaults */
    font-size: 1em;
    padding: 0;
}

/* ✅ BUTTON SLIDES LEFT when sidebar collapses */
/* Using sibling combinator (~) since button is OUTSIDE sidebar */
.sidebar.collapsed ~ .sidebar-toggle-btn {
    left: 0;  /* Slides from 260px to 0 */
}

/* Hover effect */
.sidebar-toggle-btn:hover {
    background: linear-gradient(135deg, #111827 0%, #0f1419 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3), 
                inset 0 0 0 1px rgba(255, 255, 255, 0.2);
}

/* Click/active effect */
.sidebar-toggle-btn:active {
    transform: translateY(-50%) scale(0.95);
}

/* ✅ ICON ROTATION: < becomes > when collapsed */
/* Using sibling combinator (~) */
.sidebar.collapsed ~ .sidebar-toggle-btn i {
    transform: rotate(180deg);
}

/* Smooth icon rotation */
.sidebar-toggle-btn i {
    transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ============================================
   SIDEBAR OVERLAY (Mobile Only)
   ============================================ */

.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1040;
    display: none;
    opacity: 0;
    transition: opacity 0.3s ease;
}

/* Show overlay */
.sidebar-overlay.show {
    display: block;
    opacity: 1;
}

/* ============================================
   MAIN CONTENT
   ============================================ */

.main-content {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    
    /* Adjust for sidebar width */
    margin-left: 260px;
    
    /* ✅ Smooth margin animation */
    transition: margin-left 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Adjust margin when sidebar collapses (desktop) */
@media (min-width: 992px) {
    .sidebar.collapsed ~ .sidebar-overlay ~ .main-content {
        margin-left: 0;
    }
}

/* ============================================
   RESPONSIVE: MOBILE (<992px)
   ============================================ */

@media (max-width: 991px) {
    /* Hide toggle button on mobile */
    .sidebar-toggle-btn {
        display: none;
    }

    /* No margin adjustment on mobile */
    .main-content {
        margin-left: 0;
    }

    /* Sidebar hidden by default on mobile */
    .sidebar {
        transform: translateX(-260px);
    }

    /* Show sidebar when opened on mobile */
    .sidebar.mobile-open {
        transform: translateX(0);
    }
}

/* ============================================
   TOP NAVBAR
   ============================================ */

.top-navbar {
    background: white;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    padding: 12px 20px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 1100;
    flex-wrap: wrap;
    gap: 15px;
}

/* Hidden menu section */
.menu-section {
    display: none;
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

/* User info and menu */
.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
    white-space: nowrap;
}

.user-menu-toggle {
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-menu {
    position: relative;
}

.user-dropdown {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
    padding: 10px;
    display: none;
    min-width: 170px;
    z-index: 1000;
}

.user-dropdown.show {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* User avatar */
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

.user-avatar-gradient {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border: 2px solid #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

.user-meta {
    display: flex;
    flex-direction: column;
    line-height: 1.1;
}

.user-name {
    font-weight: 600;
    font-size: 14px;
    color: #333;
}

.user-role {
    color: #6c757d;
    font-size: 11px;
}

.user-caret {
    font-size: 10px;
    color: #adb5bd;
    margin-left: 5px;
}

/* ============================================
   PAGE CONTENT & FOOTER
   ============================================ */

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

.layout-footer {
    background: white;
    border-top: 1px solid #e5e7eb;
    padding: 15px 20px;
    text-align: center;
    margin-top: auto;
}

.layout-footer-text {
    color: #6c757d;
    font-size: 0.9em;
    margin: 0;
}
```

---

## 3. JavaScript (public/js/layout.js) - Functionality

```javascript
// ============================================
// SIDEBAR TOGGLE FUNCTIONALITY
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // ===== Sidebar Elements =====
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    const overlay = document.getElementById('sidebarOverlay');
    const mainContent = document.getElementById('mainContent');
    const navItems = document.querySelectorAll('.nav-item');

    // localStorage key for sidebar state (persists across sessions)
    const SIDEBAR_STATE_KEY = 'ims_sidebar_collapsed';

    /**
     * Initialize sidebar state from localStorage or defaults
     * Desktop: Restores last saved state (expanded by default)
     * Mobile: Always starts collapsed
     */
    function initializeSidebarState() {
        const isDesktop = window.innerWidth > 991;
        const savedCollapsed = localStorage.getItem(SIDEBAR_STATE_KEY);

        if (isDesktop) {
            // Desktop: Check saved state, default to expanded (false)
            const shouldCollapse = savedCollapsed === 'true';
            if (shouldCollapse) {
                sidebar.classList.add('collapsed');
            } else {
                sidebar.classList.remove('collapsed');
            }
        } else {
            // Mobile: Always start collapsed, hide overlay
            sidebar.classList.add('collapsed');
            overlay.classList.remove('show');
        }
    }

    /**
     * Toggle sidebar between expanded and collapsed states
     * - Desktop: Toggles and saves state to localStorage
     * - Mobile: Shows/hides with overlay
     */
    function toggleSidebar() {
        const isDesktop = window.innerWidth > 991;

        if (isDesktop) {
            // Desktop toggle - saves state for persistence
            sidebar.classList.toggle('collapsed');
            const isNowCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem(SIDEBAR_STATE_KEY, isNowCollapsed);
        } else {
            // Mobile toggle - just show/hide with overlay
            const isOpen = sidebar.classList.contains('mobile-open');
            if (isOpen) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('show');
            } else {
                sidebar.classList.add('mobile-open');
                overlay.classList.add('show');
            }
        }
    }

    /**
     * Close sidebar on mobile
     */
    function closeSidebarMobile() {
        const isDesktop = window.innerWidth > 991;
        if (!isDesktop) {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
        }
    }

    /**
     * Handle window resize
     * Maintains proper sidebar state when switching between desktop/mobile
     */
    function handleWindowResize() {
        const isDesktop = window.innerWidth > 991;

        if (isDesktop) {
            // Switching to desktop - restore saved state
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
            initializeSidebarState();
        } else {
            // Switching to mobile - collapse and hide overlay
            sidebar.classList.remove('collapsed');
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
        }
    }

    // ===== Event Listeners =====

    // Toggle button click - show/hide sidebar
    if (toggleBtn) {
        toggleBtn.addEventListener('click', toggleSidebar);
    }

    // Overlay click to close sidebar (mobile)
    if (overlay) {
        overlay.addEventListener('click', closeSidebarMobile);
    }

    // Navigation item clicks - close mobile sidebar on nav click
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            closeSidebarMobile();
        });
    });

    // Window resize - handle desktop/mobile transitions
    window.addEventListener('resize', handleWindowResize);

    // Initialize on page load
    initializeSidebarState();

    // ===== User Menu Dropdown =====
    const userMenuToggle = document.getElementById('userMenuToggle');
    const userDropdown = document.getElementById('userDropdown');

    if (userMenuToggle && userDropdown) {
        userMenuToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function() {
            userDropdown.classList.remove('show');
        });

        userDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // ===== Auto-close Alert Messages =====
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
```

---

## Key Points Summary

### ✅ What Makes This Work

1. **Toggle Button Position**
   - `position: fixed` → Always visible in viewport
   - `left: 260px` → Positioned at sidebar edge
   - Slides to `left: 0` when sidebar collapses

2. **Transform Animation** (NOT display: none)
   - Sidebar: `transform: translateX(0)` → `translateX(-260px)`
   - Smooth, no "jank"
   - Can be interrupted and reversed

3. **Icon Rotation**
   - Uses CSS sibling selector: `.sidebar.collapsed ~ .sidebar-toggle-btn i`
   - Rotates 180° to show < or >
   - Synchronized with button movement

4. **State Persistence**
   - localStorage saves collapsed state
   - Remembers user preference across sessions
   - Only on desktop (mobile state not saved)

5. **Responsive Design**
   - Desktop (>991px): Toggle button always visible, margin adjustment
   - Mobile (<992px): Toggle button hidden, overlay-based sidebar

---

## Installation

1. Replace your `app/views/layout.php` with the HTML code
2. Replace your `public/css/layout.css` with the CSS code
3. Replace your `public/js/layout.js` with the JavaScript code
4. No dependencies required (uses vanilla JS, no jQuery)
5. Test in browser and verify toggle functionality works

---

## Browser Support

- ✅ Chrome/Edge (v90+)
- ✅ Firefox (v88+)
- ✅ Safari (v14+)
- ✅ Mobile browsers

All features use widely-supported CSS and JavaScript APIs.
