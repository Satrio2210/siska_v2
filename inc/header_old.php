<nav class="top-navbar">
	<div class="d-flex align-items-center">
		<!-- Mobile Sidebar Toggle -->
		<button class="btn btn-outline-secondary me-3 d-md-none" id="mobile-toggle-btn" aria-label="Toggle Sidebar">
			<i class="bi bi-list fs-5"></i>
		</button>
		<div id="page-title" class="d-none d-sm-block">
			<div class="page-title-main">
				KPRJ YEMIMA MEDIKA
			</div>
			<div class="page-title-sub">
				SISKA Apps
			</div>
		</div>
		<!-- <h4 class="mb-0 fw-semibold d-none d-sm-inline-block" id="page-title">KPRJ Yemima Medika</h4> -->
	</div>

	<div class="d-flex align-items-center gap-3">
		<!-- Quick Toast Notification Area (Replaces Alert) -->
		<div id="ui-toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100"></div>

		<!-- Theme Toggle Switch -->
		<button class="btn btn-outline-secondary rounded-circle" id="theme-toggle" title="Ubah Tema"
			style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
			<i class="bi bi-moon-stars-fill" id="theme-toggle-icon"></i>
		</button>

		<!-- Notifications Dropdown -->
		<div class="dropdown">
			<button class="btn btn-outline-secondary rounded-circle position-relative"
				style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"
				data-bs-toggle="dropdown" aria-expanded="false">
				<i class="bi bi-bell"></i>
				<span
					class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
					<span class="visually-hidden">Notifikasi</span>
				</span>
			</button>
			<ul class="dropdown-menu dropdown-menu-end shadow" style="width: 280px;">
				<li>
					<h6 class="dropdown-header">Notifikasi Terbaru</h6>
				</li>
				<li><a class="dropdown-item py-2" href="#">
						<div class="small text-muted">--</div>
						<div class="fw-semibold text-truncate">-</div>
					</a></li>
				<li><a class="dropdown-item py-2" href="#">
						<div class="small text-muted">--</div>
						<div class="fw-semibold text-truncate">-</div>
					</a></li>
				<li>
					<hr class="dropdown-divider">
				</li>
				<li><a class="dropdown-item text-center text-primary py-2 small fw-semibold" href="#">---</a></li>
			</ul>
		</div>
	</div>
</nav>