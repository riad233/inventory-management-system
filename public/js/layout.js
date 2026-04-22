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

    // localStorage key for sidebar state
    const SIDEBAR_STATE_KEY = 'ims_sidebar_collapsed';

    /**
     * Initialize sidebar state from localStorage or defaults
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
            // Mobile: Always start collapsed, show overlay toggle
            sidebar.classList.add('collapsed');
            overlay.classList.remove('show');
        }
    }

    /**
     * Toggle sidebar between expanded and collapsed states
     */
    function toggleSidebar() {
        const isDesktop = window.innerWidth > 991;

        if (isDesktop) {
            // Desktop toggle - saves state
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
     * Handle window resize - maintain proper sidebar state
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

    // Toggle button click
    if (toggleBtn) {
        toggleBtn.addEventListener('click', toggleSidebar);
    }

    // Overlay click to close (mobile)
    if (overlay) {
        overlay.addEventListener('click', closeSidebarMobile);
    }

    // Navigation item clicks (close mobile sidebar on nav click)
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            closeSidebarMobile();
        });
    });

    // Window resize
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

