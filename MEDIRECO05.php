<!doctype html>
<?php
include "conf/config.php";
include "inc/sanie.php";
session_start();

if (isset($_SESSION['username'])) {
  $user = $_SESSION['username'];
  $qsearch = isset($_GET['q']) ? trim($_GET['q']) : '';
  $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
  ?>
  <html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Klinik Pratama">
    <title>Daftar Pasien Rekam Medis</title>
    <link rel="shortcut icon" href="assets/img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="assets/css/layouts/header.css">
    <link rel="stylesheet" href="assets/css/layouts/side-menu.css">
    <link rel="stylesheet" href="assets/css/trxapati-shared.css">
    <link rel="stylesheet" href="assets/css/modern-table.css">
  </head>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="js/jquery.js"></script>
  <script src="js/sweetalert.min.js"></script>
  <script>
    $(document).ready(function () {
      setInterval(timestamp, 1000);
    });
    function timestamp() {
      $.ajax({ url: 'inc/timestamp.php', success: function (data) { $('#timestamp').html(data); } });
    }
  </script>

  <body onLoad="periksaakses('PASS_MEDI_REPO');">
    <div id="wrapper">
      <?php include "inc/side-menu.php"; ?>
      <div id="content-wrapper">
        <?php include "inc/header.php"; ?>
        <div class="content">
          <div class="content-modern">
            <div class="card-modern">
              <div class="card-title">DAFTAR PASIEN REKAM MEDIS
              </div>

              <form method="GET" action="MEDIRECO05.php" class="rm05-search-row" id="frmSearchRm05">
                <div class="form-group">
                  <label class="form-label" for="txtsearch">CARI PASIEN</label>
                  <input type="text" name="q" id="txtsearch" class="form-control" placeholder="Cari Nama / No. RM..."
                    value="<?php echo htmlspecialchars($qsearch, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off">
                </div>
                <!-- <button type="submit" class="btn-modern btn-save" style="height:38px;">
                  <i class="bi bi-search"></i> Cari
                </button> -->
                <?php if ($qsearch !== '') { ?>
                  <a href="MEDIRECO05.php" class="btn-modern"
                    style="height:38px;background:#6b7280;color:#fff;text-decoration:none;display:inline-flex;align-items:center;">
                    Reset
                  </a>
                <?php } ?>
              </form>

              <div id="tblscreen" style="margin-top: 10px;"></div>

            </div>
          </div>
        </div>
        <div class="footerdate"><?php echo isset($datenow) ? $datenow : date('Y-m-d'); ?></div>
        <div class="footertime"><span id="timestamp"></span></div>
      </div>
    </div>
    <script src="js/MEDIRECO05.js?v=<?php echo time(); ?>"></script>
    <script src="js/ui.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        ambilscreen(
          <?php echo json_encode($qsearch); ?>,
          <?php echo (int) $page; ?>
        );
      });
    </script>
  </body>

  </html>
  <?php
} else {
  header("Location: " . "signin.php");
}
?>