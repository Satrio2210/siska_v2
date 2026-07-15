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

navGroups.forEach(group => {
    const header = group.querySelector('.nav-group-header');
    if (!header) return;

    header.addEventListener('click', (e) => {
        e.preventDefault();

        // Close other groups (accordion behavior)
        navGroups.forEach(other => {
            if (other !== group) {
                other.classList.remove('open');
            }
        });

        group.classList.toggle('open');
    });
});

// Auto-open parent group based on current URL
function autoOpenActiveGroup() {
    const currentPath = window.location.pathname;
    const fileName = currentPath.split('/').pop() || 'index.php';

    document.querySelectorAll('.nav-submenu .submenu-link').forEach(link => {
        const href = link.getAttribute('href');
        if (href && fileName === href) {
            link.classList.add('active');
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