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
    <title>Report Resep Harian</title>
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
                <div class="card-title">&#128197; Report Resep Harian</div>
              </div>

              <form name="frmreport" class="pure-form" method="post" action="TRXADRUG08P.php">
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
                  <div class="form-group">
                    <label class="form-label">Jenis Pasien</label>
                    <div class="checkbox-row">
                      <label class="checkbox-modern">
                        <input type="radio" id="semua" name="jenispasien" value="" checked
                          onchange="ambilviewrepo(
                            document.getElementById('tglstartdate').value,
                            document.getElementById('tglenddate').value);">
                        SEMUA
                      </label>
                      <label class="checkbox-modern">
                        <input type="radio" id="bpjs" name="jenispasien" value="B"
                          onchange="ambilviewrepo(
                            document.getElementById('tglstartdate').value,
                            document.getElementById('tglenddate').value);">
                        BPJS
                      </label>
                      <label class="checkbox-modern">
                        <input type="radio" id="umum" name="jenispasien" value="U"
                          onchange="ambilviewrepo(
                            document.getElementById('tglstartdate').value,
                            document.getElementById('tglenddate').value);">
                        UMUM
                      </label>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="dokter">Dokter</label>
                    <select id="dokter" name="dokter" class="form-control"
                      onchange="ambilviewrepo(
                        document.getElementById('tglstartdate').value,
                        document.getElementById('tglenddate').value);">
                      <option value="">- SEMUA DOKTER -</option>
                      <?php
                      $q_doct = $db->query("SELECT DISTINCT
                          passiden.PASS_USER_IDEN AS DOCT_CODE,
                          passiden.PASS_USER_NAME AS DOCT_NAME
                      FROM trxaprsc
                      JOIN passiden ON trxaprsc.TRXA_PRSC_DOCT = passiden.PASS_USER_IDEN
                      JOIN emplmast ON passiden.PASS_EMPL_CODE = emplmast.EMPL_MAST_CODE
                      WHERE trxaprsc.TRXA_PRSC_STAT IN ('A', 'I', 'P')
                      AND emplmast.EMPL_MAIN_DIVI IN ('KAS', 'FAR')
                      ORDER BY passiden.PASS_USER_NAME ASC");
                      while ($d = $q_doct->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="' . $d['DOCT_CODE'] . '">' . $d['DOCT_NAME'] . '</option>';
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div style="margin-top:14px;">
                  <button type="submit" class="btn-modern btn-save btn-fit">
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

    <script src="js/TRXADRUG08.js"></script>
    <script src="js/ui.js"></script>

  </body>

  </html>
  <?php
} else {
  header("Location: " . "signin.php");
}
?>
