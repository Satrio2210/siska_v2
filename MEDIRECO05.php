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
  <style>
    .rm05-search-row {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      align-items: flex-end;
      margin-bottom: 16px;
    }
    .rm05-search-row .form-group {
      flex: 1;
      min-width: 220px;
      margin-bottom: 0;
    }
    .rm05-table-wrap .modern-table thead th {
      background: #169C89;
    }
    .rm05-table-wrap .modern-table tbody tr:hover {
      background: #e0f7fa;
    }
    .rm05-table-wrap .modern-table tbody {
      max-height: none;
      display: table-row-group;
    }
    .rm05-table-wrap .modern-table thead tr,
    .rm05-table-wrap .modern-table tbody tr {
      display: table-row;
    }
    .rm05-table-wrap .modern-table {
      table-layout: auto;
    }
    .rm05-table-wrap .modern-table th,
    .rm05-table-wrap .modern-table td {
      width: auto !important;
    }
    .rm05-table-wrap .modern-table td:nth-child(2) {
      text-align: left;
    }
    .rm05-btn-detail {
      display: inline-block;
      padding: 6px 14px;
      border-radius: 10px;
      background: #169C89;
      color: #fff !important;
      font-size: 12px;
      font-weight: 600;
      text-decoration: none;
      transition: .2s;
    }
    .rm05-btn-detail:hover {
      background: #0f7a6b;
      color: #fff !important;
    }
    .rm05-pagination {
      display: flex;
      gap: 6px;
      align-items: center;
      flex-wrap: wrap;
      margin-top: 16px;
    }
    .rm05-pagination a,
    .rm05-pagination span {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 34px;
      height: 34px;
      padding: 0 10px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      text-decoration: none;
      border: 1px solid #e5e7eb;
      color: #374151;
      background: #fff;
    }
    .rm05-pagination a:hover {
      border-color: #169C89;
      color: #169C89;
    }
    .rm05-pagination .active {
      background: #169C89;
      border-color: #169C89;
      color: #fff;
    }
    .rm05-pagination .disabled {
      opacity: .45;
      pointer-events: none;
    }
    .rm05-empty {
      text-align: center;
      padding: 28px;
      color: #64748b;
      font-weight: 500;
    }
  </style>
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
            <div class="card-title">
              <i class="bi bi-clipboard2-pulse"></i> DAFTAR PASIEN REKAM MEDIS
            </div>

            <form method="GET" action="MEDIRECO05.php" class="rm05-search-row" id="frmSearchRm05">
              <div class="form-group">
                <label class="form-label" for="txtsearch">CARI PASIEN</label>
                <input type="text" name="q" id="txtsearch" class="form-control"
                  placeholder="Cari Nama / No. RM..."
                  value="<?php echo htmlspecialchars($qsearch, ENT_QUOTES, 'UTF-8'); ?>"
                  autocomplete="off">
              </div>
              <button type="submit" class="btn-modern btn-save" style="height:38px;">
                <i class="bi bi-search"></i> Cari
              </button>
              <?php if ($qsearch !== '') { ?>
                <a href="MEDIRECO05.php" class="btn-modern" style="height:38px;background:#6b7280;color:#fff;text-decoration:none;display:inline-flex;align-items:center;">
                  Reset
                </a>
              <?php } ?>
            </form>

            <div class="table-wrapper rm05-table-wrap" id="tblscreen">
              <!-- diisi AJAX / server-side -->
            </div>
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
        <?php echo (int)$page; ?>
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
