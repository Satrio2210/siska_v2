// DOM Elements
const sidebar = document.getElementById('sidebar');
const desktopToggleBtn = document.getElementById('desktop-toggle-btn');
const mobileToggleBtn = document.getElementById('mobile-toggle-btn');
const toggleIcon = document.getElementById('toggle-icon');
const themeToggle = document.getElementById('theme-toggle');
const themeToggleIcon = document.getElementById('theme-toggle-icon');
const navLinks = document.querySelectorAll('.nav-link-custom');
const htmlElement = document.documentElement;
const customTooltip = document.getElementById('sidebar-tooltip');

// Notification Helper (Elegant replacement for standard Alert)
function alertNotification(message, type = 'success') {
    const container = document.getElementById('ui-toast-container');
    const id = 'toast-' + Date.now();
    const bgClass = type === 'success' ? 'bg-success text-white' : type === 'info' ? 'bg-info text-white' : 'bg-warning text-dark';

    const toastHtml = `
                <div id="${id}" class="toast align-items-center ${bgClass} border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-info-circle me-2"></i> ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
    container.insertAdjacentHTML('beforeend', toastHtml);
    const toastEl = document.getElementById(id);
    const bsToast = new bootstrap.Toast(toastEl, { delay: 3000 });
    bsToast.show();

    toastEl.addEventListener('hidden.bs.toast', () => {
        toastEl.remove();
    });
}

// Toggle Sidebar - Desktop
desktopToggleBtn.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');

    // Adjust Chevron Icon
    if (sidebar.classList.contains('collapsed')) {
        toggleIcon.classList.replace('bi-chevron-left', 'bi-chevron-right');
    } else {
        toggleIcon.classList.replace('bi-chevron-right', 'bi-chevron-left');
    }
});

// Toggle Sidebar - Mobile
mobileToggleBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    sidebar.classList.toggle('mobile-show');
});

// Hide Mobile Sidebar when clicking outside
document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768) {
        if (!sidebar.contains(e.target) && e.target !== mobileToggleBtn && !mobileToggleBtn.contains(e.target)) {
            sidebar.classList.remove('mobile-show');
        }
    }
});

// Theme Toggle Logic
themeToggle.addEventListener('click', () => {
    const currentTheme = htmlElement.getAttribute('data-bs-theme');
    const nextTheme = currentTheme === 'light' ? 'dark' : 'light';

    htmlElement.setAttribute('data-bs-theme', nextTheme);

    // Update icon
    if (nextTheme === 'dark') {
        themeToggleIcon.classList.replace('bi-moon-stars-fill', 'bi-sun-fill');
        alertNotification('Mode Gelap diaktifkan', 'info');
    } else {
        themeToggleIcon.classList.replace('bi-sun-fill', 'bi-moon-stars-fill');
        alertNotification('Mode Terang diaktifkan', 'info');
    }
});

// On window resize, adjust sidebar layout
window.addEventListener('resize', () => {
    if (window.innerWidth <= 768) {
        sidebar.classList.remove('collapsed');
        toggleIcon.classList.replace('bi-chevron-right', 'bi-chevron-left');
    }
});

// Collapsible Nav Group Logic
const navGroups = document.querySelectorAll('.nav-group');

// 1. Handle Klik Header Utama (Level 1 - Farmasi, dll)
navGroups.forEach(group => {
    const header = group.querySelector('.nav-group-header');
    if (!header) return;

    header.addEventListener('click', (e) => {
        e.preventDefault();

        // Accordion behavior untuk level 1
        navGroups.forEach(other => {
            if (other !== group) {
                other.classList.remove('open');
            }
        });

        group.classList.toggle('open');
    });
});

// 2. Handle Klik Submenu Bertingkat (Level 2 - Report)
document.querySelectorAll('.has-child > a').forEach(toggleBtn => {
    toggleBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation(); // Mencegah event bubbling ke parent

        const parentLi = toggleBtn.closest('.has-child');
        if (parentLi) {
            parentLi.classList.toggle('open');
        }
    });
});

// 3. Auto-open berdasarkan URL aktif (Support Multi-level)
function autoOpenActiveGroup() {
    const currentPath = window.location.pathname;
    const fileName = currentPath.split('/').pop() || 'index.php';

    // Cari semua link (termasuk yang ada di dalam sub-submenu)
    document.querySelectorAll('.nav-submenu a, .nav-sub-submenu a').forEach(link => {
        const href = link.getAttribute('href');

        if (href && href !== 'javascript:void(0)' && fileName === href) {
            link.classList.add('active');

            // Buka parent level 2 jika ada (.has-child)
            const parentChild = link.closest('.has-child');
            if (parentChild) {
                parentChild.classList.add('open');
            }

            // Buka parent level 1 (.nav-group)
            const parentGroup = link.closest('.nav-group');
            if (parentGroup) {
                parentGroup.classList.add('open');
            }
        }
    });


    // Also check top-level dashboard link
    const dashboardLink = document.querySelector('.nav-link-custom[data-nav="dashboard"]');
    if (dashboardLink && fileName === 'index.php') {
        dashboardLink.classList.add('active');
    }
}

autoOpenActiveGroup();