<!doctype html>
<?php include "conf/config.php";
session_start();

if (isset($_SESSION['username'])) {
  $user = $_SESSION['username'];

  ?>
  <html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Klinik Pratama">
    <title>Print Faktur</title>
    <link rel="shortcut icon" href="assets/img/icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/layouts/header.css">
    <link rel="stylesheet" href="assets/css/bootstrap/bootstrap.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/layouts/side-menu.css">
    <link rel="stylesheet" href="assets/css/trxapati-shared.css">
    <style>
      .content {
        background: #f8fafc;
        padding: 20px;
      }

      .card-modern {
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 2px solid #e5e7eb;
        border-radius: 18px;
        padding: 16px 18px;
        margin-bottom: 16px;
      }

      .card-title {
        font-size: 16px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 14px;
      }

      .form-control {
        border: 1px solid #D1D5DB;
        color: #1F2937;
        font-size: 14px;
        height: 38px;
        font-weight: 500;
        padding: 0 14px;
        border-radius: 12px;
        width: 100%;
        max-width: 320px;
        box-sizing: border-box;
      }

      .form-control:focus {
        outline: none;
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.12);
      }

      .form-label {
        font-size: 14px;
        font-weight: 600;
        color: #4B5563;
        display: block;
        margin-bottom: 6px;
      }

      /* Custom pharmacy table — mirror TRXAPOLI01V, NO modern-table.css */
      .custom-pharmacy-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        table-layout: fixed;
        background: #fff;
      }

      .table-wrapper {
        width: 100%;
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        background: white;
      }

      .custom-pharmacy-table thead {
        position: sticky;
        top: 0;
        z-index: 2;
      }

      .custom-pharmacy-table thead tr {
        display: table;
        width: 100%;
        table-layout: fixed;
      }

      .custom-pharmacy-table th {
        background-color: #10b981;
        color: #fff;
        font-weight: 700;
        padding: 14px 12px;
        border-bottom: 2px solid #059669;
        font-size: 13px;
        text-align: center;
      }

      .custom-pharmacy-table tbody {
        display: block;
        max-height: 420px;
        overflow-y: auto;
        overflow-x: hidden;
        width: 100%;
        scrollbar-gutter: stable;
      }

      .custom-pharmacy-table tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
        transition: .2s ease;
        background: white;
      }

      .custom-pharmacy-table tbody tr:nth-child(even) {
        background: #f9fafb;
      }

      .custom-pharmacy-table tbody tr:hover {
        background: #f3f4f6;
      }

      .custom-pharmacy-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #edf2f7;
        color: #334155;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
        word-wrap: break-word;
      }

      .btn-print {
        display: inline-block;
        background: #ea580c;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
      }

      .btn-print:hover {
        background: #c2410c;
        color: #fff;
      }
    </style>
  </head>
  <script type="text/javascript" src="js/jquery.js"></script>
  <script>
    $(document).ready(function () {
      setInterval(timestamp, 1000);
    });
    function timestamp() { $.ajax({ url: 'inc/timestamp.php', success: function (data) { $('#timestamp').html(data); }, }); }
  </script>

  <body onLoad="periksaakses('PASS_DRUG_ENTR');">
    <div id="wrapper">
      <?php include "inc/side-menu.php"; ?>

      <div id="content-wrapper">
        <?php include "inc/header.php"; ?>
        <div class="content">
          <div class="content-modern">

            <form name="frmtrxadrug" method="post">
              <div class="card-modern">
                <div class="card-title">Print Faktur</div>
                <label class="form-label" for="txtdrugcode">Cari Faktur</label>
                <input type="text" class="form-control" name="txtdrugcode" id="txtdrugcode" maxlength="20"
                  placeholder="Ketik nomor faktur..."
                  onkeyup="if (value.length > 0) { ambilviewid(this.value); };"
                  onkeydown="if (event.keyCode == 13 && value.length > 13) {
                    document.getElementById('txtdrugcode').value = '';
                    document.getElementById('txtdrugcode').focus();
                  }">
              </div>

              <div class="card-modern">
                <div class="card-title">Daftar Faktur</div>
                <div id="tblviewid"></div>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>

    <script src="js/TRXADRUG03.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/ui.js"></script>
  </body>

  </html>
  <?php
} else {
  header("Location: " . "signin.php");
}
?>
