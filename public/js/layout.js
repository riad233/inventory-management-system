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
            link.addEventListener('click', function() {
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
});
