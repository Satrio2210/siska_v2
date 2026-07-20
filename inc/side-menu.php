<!-- SIDEBAR -->
<aside id="sidebar">
    <!-- Sidebar Header / Logo -->
    <div class="sidebar-header">
        <a href="#" class="d-flex align-items-center text-decoration-none text-reset">
            <i class="bi bi-person-lines-fill text-primary fs-3 me-2"></i>
            <span class="brand-name fs-5 fw-bold text-gradient"><?php echo $_SESSION['username']; ?></span>
        </a>
        <!-- Desktop Sidebar Toggle Button -->
        <button class="btn btn-sm btn-link text-secondary d-none d-md-block" id="desktop-toggle-btn"
            title="Toggle Sidebar">
            <i class="bi bi-chevron-left fs-5" id="toggle-icon"></i>
        </button>
    </div>

    <!-- Sidebar Nav Links -->
    <div class="sidebar-nav">
        <div class="nav-section-title">Menu Utama</div>

        <a href="index.php" class="nav-link-custom" data-nav="dashboard" data-tooltip-text="Dashboard">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>

        <div class="nav-group">
            <a href="javascript:void(0)" class="nav-link-custom nav-group-header">
                <i class="bi bi-person-plus-fill"></i>
                <span>Admisi</span>
                <i class="bi bi-chevron-down nav-group-chevron ms-auto"></i>
            </a>
            <ul class="nav-submenu">
                <li><a href="TRXAPATI01.php" class="nav-link-custom submenu-link">
                        <i class="bi bi-caret-right"></i>Pasien Baru</a></li>
                <li><a href="TRXAPATI02.php" class="nav-link-custom submenu-link"><i
                            class="bi bi-caret-right"></i>Pasien Berobat</a></li>
                <li><a href="TRXAPATI07.php" class="nav-link-custom submenu-link"><i class="bi bi-caret-right"></i>TTV &
                        Antropometri</a></li>
                <li><a href="TRXAPATI08.php" class="nav-link-custom submenu-link"><i
                            class="bi bi-caret-right"></i>Rujukan</a></li>
                <li><a href="TRXAPATI06.php" class="nav-link-custom submenu-link"><i
                            class="bi bi-caret-right"></i>Daftar Harga</a></li>
            </ul>
        </div>

        <div class="nav-group">
            <a href="javascript:void(0)" class="nav-link-custom nav-group-header">
                <i class="bi bi-clipboard2-pulse-fill"></i>
                <span>Rawat Jalan</span>
                <i class="bi bi-chevron-down nav-group-chevron ms-auto"></i>
            </a>
            <ul class="nav-submenu">
                <li><a href="TRXAPOLI00.php" class="nav-link-custom submenu-link"><i
                            class="bi bi-caret-right"></i>Pemeriksaan</a></li>
                <li><a href="#" class="nav-link-custom submenu-link"><i class="bi bi-caret-right"></i>Harga
                        Tindakan</a></li>
                <li><a href="#" class="nav-link-custom submenu-link"><i class="bi bi-caret-right"></i>Diagnosa</a></li>
            </ul>
        </div>

        <div class="nav-group">
            <a href="javascript:void(0)" class="nav-link-custom nav-group-header">
                <i class="bi bi-clipboard2-pulse-fill"></i>
                <span>Laboratorium</span>
                <i class="bi bi-chevron-down nav-group-chevron ms-auto"></i>
            </a>
            <ul class="nav-submenu">
                <li><a href="TRXALABO00.php" class="nav-link-custom submenu-link"><i
                            class="bi bi-caret-right"></i>Pemeriksaan</a></li>
                <li><a href="TRXALABO05.php" class="nav-link-custom submenu-link"><i
                            class="bi bi-caret-right"></i>Hasil</a></li>
                <li><a href="#" class="nav-link-custom submenu-link"><i class="bi bi-caret-right"></i>Harga
                        Pemeriksaan</a></li>
            </ul>
        </div>

        <div class="nav-group">
            <a href="javascript:void(0)" class="nav-link-custom nav-group-header">
                <i class="bi bi-cash-coin"></i>
                <span>Kasir</span>
                <i class="bi bi-chevron-down nav-group-chevron ms-auto"></i>
            </a>
            <ul class="nav-submenu">
                <li><a href="TRXASALE00.php" class="nav-link-custom submenu-link"><i
                            class="bi bi-caret-right"></i>Pembayaran</a></li>
                <li><a href="TRXASALE02.php" class="nav-link-custom submenu-link"><i
                            class="bi bi-caret-right"></i>Kwitansi</a></li>
                <li><a href="#" class="nav-link-custom submenu-link"><i class="bi bi-caret-right"></i>Report</a></li>
            </ul>
        </div>

        <div class="nav-group">
            <a href="javascript:void(0)" class="nav-link-custom nav-group-header">
                <i class="bi bi-capsule"></i>
                <span>Farmasi</span>
                <i class="bi bi-chevron-down nav-group-chevron ms-auto"></i>
            </a>
            <ul class="nav-submenu">
                <li><a href="TRXADRUG00.php" class="nav-link-custom submenu-link"><i class="bi bi-caret-right"></i>Input
                        Resep</a></li>
                <li><a href="TRXADRUG01.php" class="nav-link-custom submenu-link"><i
                            class="bi bi-caret-right"></i>Penyerahan Obat</a></li>
                <li><a href="TRXADRUG02.php" class="nav-link-custom submenu-link"><i
                            class="bi bi-caret-right"></i>Penjualan Obat</a></li>
                <li><a href="TRXADRUG03.php" class="nav-link-custom submenu-link"><i
                            class="bi bi-caret-right"></i>Faktur</a></li>
                <li><a href="TRXADRUG04.php" class="nav-link-custom submenu-link"><i
                            class="bi bi-caret-right"></i>Report</a></li>
            </ul>
        </div>

        <div class="nav-group">
            <a href="javascript:void(0)" class="nav-link-custom nav-group-header">
                <i class="bi bi-file-earmark-medical-fill"></i>
                <span>Rekam Medis</span>
                <i class="bi bi-chevron-down nav-group-chevron ms-auto"></i>
            </a>
            <ul class="nav-submenu">
                <li><a href="MEDIRECO05.php" class="nav-link-custom submenu-link"><i
                            class="bi bi-caret-right"></i>Daftar RM Pasien</a></li>
            </ul>
        </div>

        <div class="nav-section-title">Sistem & Pengaturan</div>

        <div class="nav-group">
            <a href="javascript:void(0)" class="nav-link-custom nav-group-header">
                <i class="bi bi-envelope-paper"></i>
                <span>Akses</span>
                <i class="bi bi-chevron-down nav-group-chevron ms-auto"></i>
            </a>
            <ul class="nav-submenu">
                <li><a href="PASSIDEN01.php" class="nav-link-custom submenu-link">Data User</a></li>
                <li><a href="PASSIDEN02.php" class="nav-link-custom submenu-link">Password</a></li>
                <li><a href="PASSIDEN03.php" class="nav-link-custom submenu-link">Level Akses</a></li>
            </ul>
        </div>

        <div class="nav-group">
            <a href="javascript:void(0)" class="nav-link-custom nav-group-header">
                <i class="bi bi-gear"></i>
                <span>Akunting</span>
                <i class="bi bi-chevron-down nav-group-chevron ms-auto"></i>
            </a>
            <ul class="nav-submenu">
                <li><a href="AUTOJRNL00.php" class="nav-link-custom submenu-link">Auto Jurnal</a></li>
                <li><a href="TRXAJRNL00.php" class="nav-link-custom submenu-link">Manual Jurnal</a></li>
                <li><a href="COACMAST00.php" class="nav-link-custom submenu-link">C.O.A.</a></li>
                <li><a href="TBLACOAC00.php" class="nav-link-custom submenu-link">Grup Akun</a></li>
                <li><a href="TBLEDIVI00.php" class="nav-link-custom submenu-link">Divisi</a></li>
                <li><a href="REPOACCT00.php" class="nav-link-custom submenu-link">Laporan</a></li>
            </ul>
        </div>

        <div class="nav-group">
            <a href="javascript:void(0)" class="nav-link-custom nav-group-header">
                <i class="bi bi-gear"></i>
                <span>Persediaan</span>
                <i class="bi bi-chevron-down nav-group-chevron ms-auto"></i>
            </a>
            <ul class="nav-submenu">
                <li><a href="TBLIUNIT00.php" class="nav-link-custom submenu-link">Spesifikasi Item</a></li>
                <li><a href="INVEMAST00.php" class="nav-link-custom submenu-link">Master Item</a></li>
                <li><a href="WAREMAST00.php" class="nav-link-custom submenu-link">Ware House</a></li>
                <li><a href="INVETRANS00.php" class="nav-link-custom submenu-link">Transfer</a></li>
                <li><a href="INVESTOCK00.php" class="nav-link-custom submenu-link">Stock</a></li>
                <li><a href="INVEUP00.php" class="nav-link-custom submenu-link">Update Harga</a></li>
            </ul>
        </div>

        <div class="nav-group">
            <a href="javascript:void(0)" class="nav-link-custom nav-group-header">
                <i class="bi bi-question-circle"></i>
                <span>Pembelian</span>
                <i class="bi bi-chevron-down nav-group-chevron ms-auto"></i>
            </a>
            <ul class="nav-submenu">
                <li><a href="TRXAPROC01.php" class="nav-link-custom submenu-link">Purchase Order</a></li>
                <li><a href="TRXAPROC03.php" class="nav-link-custom submenu-link">Penerimaan</a></li>
                <li><a href="TRXAPROC07.php" class="nav-link-custom submenu-link">Laporan</a></li>
            </ul>
        </div>

        <div class="nav-group">
            <a href="javascript:void(0)" class="nav-link-custom nav-group-header">
                <i class="bi bi-gear"></i>
                <span>Rekanan</span>
                <i class="bi bi-chevron-down nav-group-chevron ms-auto"></i>
            </a>
            <ul class="nav-submenu">
                <li><a href="CUSTMAST00.php" class="nav-link-custom submenu-link">Data Rekanan</a></li>
                <li><a href="CUSTRCVD01.php" class="nav-link-custom submenu-link">Pelunasan</a></li>
                <li><a href="CUSTRCVD03.php" class="nav-link-custom submenu-link">Kwitansi</a></li>
            </ul>
        </div>

        <div class="nav-group">
            <a href="javascript:void(0)" class="nav-link-custom nav-group-header">
                <i class="bi bi-gear"></i>
                <span>Keuangan</span>
                <i class="bi bi-chevron-down nav-group-chevron ms-auto"></i>
            </a>
            <ul class="nav-submenu">
                <li><a href="TBLEBANK00.php" class="nav-link-custom submenu-link">Data Bank</a></li>
                <li><a href="TRXAVEND01.php" class="nav-link-custom submenu-link">Data Vendor</a></li>
            </ul>
        </div>

        <div class="nav-group">
            <a href="javascript:void(0)" class="nav-link-custom nav-group-header">
                <i class="bi bi-gear"></i>
                <span>Personil</span>
                <i class="bi bi-chevron-down nav-group-chevron ms-auto"></i>
            </a>
            <ul class="nav-submenu">
                <li><a href="TBLEPOST00.php" class="nav-link-custom submenu-link">Data Pendukung</a></li>
                <li><a href="EMPLMAST01.php" class="nav-link-custom submenu-link">Register Karyawan</a></li>
                <li><a href="EMPLMAST02.php" class="nav-link-custom submenu-link">Update Karyawan</a></li>
                <li><a href="EMPLMAST03.php" class="nav-link-custom submenu-link">Resign Karyawan</a></li>
                <li><a href="EMPLMAST04.php" class="nav-link-custom submenu-link">View Employment</a></li>
            </ul>
        </div>

    </div>

</aside>

<!-- Collapsed Sidebar Tooltip Element -->
<div id="sidebar-tooltip" class="collapsed-tooltip"></div>