<nav class="top-navbar">
    <!-- LEFT -->
    <div class="header-left">
        <button class="btn btn-light d-md-none me-3" id="mobile-toggle-btn">
            <i class="bi bi-list fs-4"></i>
        </button>

        <img src="assets/img/logo.png" alt="Logo Klinik" class="me-2">

        <div style="overflow: hidden;">
            <h2 class="page-title mb-1">
                KPRJ Yemima Medika
            </h2>
            <div class="page-subtitle">
                <span>SISKA Apps</span>
            </div>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="header-right">
        <!-- Search -->
        <div class="header-search d-none d-md-flex">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control border-0" placeholder="...">
        </div>

        <!-- Theme -->
        <button class="header-icon-btn" id="theme-toggle" title="Ubah Tema">
            <i class="bi bi-moon-stars" id="theme-toggle-icon"></i>
        </button>

        <!-- Notification -->
        <button class="header-icon-btn position-relative">
            <i class="bi bi-bell"></i>
            <span class="notification-dot"></span>
        </button>

        <!-- Profile -->
        <div class="dropdown">
            <button class="btn profile-btn d-flex align-items-center gap-2" data-bs-toggle="dropdown"
                aria-expanded="false">
                <img src="https://placehold.co/40x40/0d6efd/ffffff?text=U" alt="Avatar" class="profile-avatar">
                <div class="profile-info text-start d-none d-md-flex flex-column justify-content-center">
                    <span class="fw-semibold lh-1 mb-1 text-dark" style="font-size: 15px;">
                        <?php echo $_SESSION['PASS_USER_NAME']; ?>
                    </span>
                    <small class="text-secondary">
                        User
                    </small>
                </div>
                <i class="bi bi-chevron-down text-secondary ms-1 d-none d-md-block"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li>
                    <a class="dropdown-item" href="#" onclick="alertNotification('Fitur Profil', 'info')">
                        <i class="bi bi-person me-2"></i>Profil
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <a class="dropdown-item text-danger" href="signout.php">
                        <i class="bi bi-box-arrow-right me-2"></i>Keluar
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>