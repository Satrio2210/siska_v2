<!DOCTYPE html>
<?php include "conf/config.php";
session_start();

if (isset($_SESSION['username'])) {
  $user = $_SESSION['username'];
  ?>
  <html lang="id">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Klinik Pratama">
    <title>Report Pemakaian Obat</title>
    <link rel="shortcut icon" href="assets/img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="assets/css/layouts/header.css">
    <link rel="stylesheet" href="assets/css/layouts/side-menu.css">
    <link rel="stylesheet" href="assets/css/trxapati-shared.css">
  </head>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="js/jquery.js"></script>
  <script type="text/javascript" src="js/sanie.js"></script>

  <script>
    $(document).ready(function () {
      setInterval(timestamp, 1000);
    });
    function timestamp() { $.ajax({ url: 'inc/timestamp.php', success: function (data) { $('#timestamp').html(data); }, }); }
  </script>

  <body onLoad="periksaakses('PASS_DRUG_VIEW');
    ambilviewrepo(
      document.getElementById('tglstartdate').value,
      document.getElementById('tglenddate').value
    );
    ">
    <div id="wrapper">
      <!-- Side Menu -->
      <?php include "inc/side-menu.php"; ?>

      <!-- Tampilan Menu -->
      <div id="content-wrapper">
        <?php include "inc/header.php"; ?>

        <div class="content">
          <div class="content-modern">
            <div class="card-modern">
              <div class="poli-card-header">
                <div class="card-title">&#128203; Report Pemakaian Obat</div>
              </div>

              <form name="frmreport" class="pure-form" method="post" action="TRXADRUG04P.php">
                <div class="form-grid">
                  <div class="form-group">
                    <label class="form-label" for="tglstartdate">Dari Tanggal</label>
                    <input type="date" name="tglstartdate" id="tglstartdate" class="form-control"
                      value="<?php echo $datenow; ?>"
                      onchange="document.getElementById('tglenddate').value = this.value;
                        ambilviewrepo(this.value, document.getElementById('tglenddate').value);">
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="tglenddate">Sampai Tanggal</label>
                    <input type="date" name="tglenddate" id="tglenddate" class="form-control"
                      value="<?php echo $datenow; ?>"
                      onchange="ambilviewrepo(document.getElementById('tglstartdate').value, this.value);">
                  </div>
                </div>

                <div style="margin-top:14px;">
                  <button type="button" class="btn-modern btn-save btn-fit" onclick="exportexcel();">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                  </button>
                </div>
              </form>

              <div id="tblviewrepo" style="margin-top:16px;"></div>
            </div>
          </div>
        </div><!-- div content -->

        <div class="footerdate">
          <span class="labelTime Time"><b>Date :</b> <?php $tgl = date('d-m-Y');
          echo $tgl; ?></span>
        </div>
        <div class="footertime">
          <span class="labelTime Time" id="timestamp"></span>
        </div>

      </div>
    </div>

    <script src="js/TRXADRUG04.js"></script>
    <script src="js/ui.js"></script>

    <script>
      function exportexcel() {
        var startdate = document.getElementById('tglstartdate').value;
        var enddate = document.getElementById('tglenddate').value;
        window.open(
          'TRXADRUG04X.php?q=' + startdate + '|' + enddate,
          '_blank'
        );
      }
    </script>

  </body>

  </html>
  <?php
} else {
  header("Location: " . "signin.php");
}
?>
