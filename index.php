<?php
// PENTING: session_start() harus selalu diletakkan paling atas sebelum ada output HTML 
// agar tidak terjadi error "Headers already sent".
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "conf/config.php";
session_start();

//cek adanya session
if (isset($_SESSION['username'])) {
	$user = $_SESSION['username'];
	?>

	<!doctype html>
	<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="Sistem Informasi Klinik Pratama">
		<title>Sistem Informasi Klinik Pratama</title>
		<link rel="shortcut icon" href="assets/img/icon.png">

		<link rel="stylesheet" href="assets/css/layouts/side-menu.css">
		<link rel="stylesheet" href="assets/css/layouts/header.css">
		<link rel="stylesheet" href="assets/css/bootstrap/bootstrap.css">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

	</head>

	<body onload="ambilviewid('X')">

		<div id="wrapper">

			<!-- Side Menu -->
			<?php include "inc/side-menu.php"; ?>

			<!--<div class="pure-menu">
					<a class="pure-menu-heading" href="#"><?php echo $_SESSION['username']; ?></a>

					<ul class="pure-menu-list">
						<li class="pure-menu-item"><a href="PASSIDEN00.php" class="pure-menu-link">AKSES</a></li>
						<li class="pure-menu-item"><a href="GL.php" class="pure-menu-link">AKUNTING</a></li> 
						<li class="pure-menu-item"><a href="INVENTORY.php" class="pure-menu-link">PERSEDIAAN</a></li>
						<li class="pure-menu-item"><a href="PURCHASING.php" class="pure-menu-link">PEMBELIAN</a></li>
						<li class="pure-menu-item"><a href="TRXAPATI01.php" class="pure-menu-link">ADMISI</a></li>
						<li class="pure-menu-item"><a href="TRXAPOLI01.php" class="pure-menu-link">RAWAT JALAN</a></li>
						<li class="pure-menu-item"><a href="TRXALABO00.php" class="pure-menu-link">LABORATORIUM</a></li>
						<li class="pure-menu-item"><a href="CUSTMAST00.php" class="pure-menu-link">REKANAN</a></li> 
						<li class="pure-menu-item"><a href="TRXASALE00.php" class="pure-menu-link">KASIR</a></li>
						<li class="pure-menu-item"><a href="TRXADRUG00.php" class="pure-menu-link">FARMASI</a></li>
						<li class="pure-menu-item"><a href="MEDIRECO00.php" class="pure-menu-link">REKAM MEDIS</a></li>
						<li class="pure-menu-item"><a href="TRXAVEND00.php" class="pure-menu-link">KEUANGAN</a></li>
						<li class="pure-menu-item"><a href="FIXEDASSET.php" class="pure-menu-link">ASSET TETAP</a></li> 
						<li class="pure-menu-item"><a href="EMPLMAST00.php" class="pure-menu-link">PERSONIL</a></li>

						<li class="pure-menu-item menu-item-divided"><a href="signout.php" class="pure-menu-link">EXIT</a>
						</li>
					</ul>
				</div> -->
			<!-- </div> -->

			<!-- Tampilan Menu -->
			<div id="content-wrapper">
				<?php include "inc/header.php"; ?>
				<div class="content">
					<!-- Konten AJAX (Dashboard) dirender disini -->
					<div id="tblviewdata"></div>

					<div class="footerdate">
						<span class="labelTime Time"><b>Date :</b> <?php echo date('d-m-Y'); ?></span>
					</div>
					<div class="footertime">
						<span class="labelTime Time" id="timestamp"></span>
					</div>
				</div>
			</div>
		</div>

		<!-- Scripts -->
		<!-- Tambahkan jQuery agar fungsi $ dan ajax bisa berjalan -->
		<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
		<script src="js/VIEWDATA.js"></script>
		<script src="js/ui.js"></script>

		<script>
			// Script Waktu/Jam Realtime
			$(document).ready(function () {
				setInterval(timestamp, 1000);
			});

			$(document).ready(function () {
				setInterval(timestamp, 1000);

				// PERBAIKAN: Panggil dashboard secara otomatis saat index.php baru dibuka
				if (typeof ambilviewid === "function") {
					ambilviewid("");
				}
			});

			function timestamp() {
				// Menggunakan try-catch agar jika inc/timestamp.php tidak ada, tidak merusak script lain
				try {
					$.ajax({
						url: 'inc/timestamp.php',
						success: function (data) {
							$('#timestamp').html(data);
						},
						error: function () {
							// Fallback waktu lokal jika file PHP tidak ditemukan di preview
							const now = new Date();
							$('#timestamp').html(now.toLocaleTimeString('id-ID'));
						}
					});
				} catch (e) {
					console.log(e);
				}
			}
		</script>

	</body>

	</html>
	<?php
} else {
	header("Location: signin.php");
}
?>