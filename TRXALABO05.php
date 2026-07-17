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
    <title>Input Hasil Pemeriksaan</title>
    <link rel="shortcut icon" href="assets/img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="assets/css/layouts/header.css">
    <link rel="stylesheet" href="assets/css/layouts/side-menu.css">
    <link rel="stylesheet" href="assets/css/trxapati-shared.css">

    <style>
      .table-container {
        overflow: auto;
        border-radius: 16px;
        max-height: 420px;
        border: 1px solid #e2e8f0;
        margin-top: 10px;
      }

      #lab-items-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
      }

      #lab-items-table thead {
        background: #10b981;
        color: white;
        position: sticky;
        top: 0;
        z-index: 10;
      }

      #lab-items-table th {
        padding: 6px 6px;
        font-size: 13px;
        font-weight: 700;
        text-align: center;
        vertical-align: middle;
        border: none;
        letter-spacing: 0.3px;
      }

      #lab-items-table td {
        padding: 8px 6px;
        font-size: 12px;
        /* color: #374151; */
        text-align: center;
        vertical-align: middle;
        border-bottom: 1px solid #e5e7eb;
        overflow-wrap: break-word;
        word-break: break-word;
      }

      #lab-items-table th:nth-child(1),
      #lab-items-table td:nth-child(1) {
        width: 10%;
      }

      #lab-items-table th:nth-child(2),
      #lab-items-table td:nth-child(2) {
        width: 20%;
      }

      #lab-items-table th:nth-child(3),
      #lab-items-table td:nth-child(3) {
        width: 20%;
      }

      #lab-items-table th:nth-child(4),
      #lab-items-table td:nth-child(4) {
        width: 20%;
      }

      #lab-items-table th:nth-child(5),
      #lab-items-table td:nth-child(5) {
        width: 20%;
      }

      #lab-items-table th:nth-child(6),
      #lab-items-table td:nth-child(6) {
        width: 10%;
      }

      .pemeriksaan-row {
        display: flex;
        align-items: stretch;
        gap: 8px;
      }

      .pemeriksaan-row select.form-control {
        flex: 1;
        min-width: 0;
      }

      #btnTambahItem {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 42px;
        width: 42px;
        min-width: 42px;
        height: auto;
        padding: 0;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        background: #fff;
        color: #10b981;
        cursor: pointer;
        line-height: 1;
        box-sizing: border-box;
      }

      #btnTambahItem i {
        font-size: 20px;
        line-height: 1;
      }

      #btnTambahItem:hover {
        color: #059669;
        border-color: #10b981;
        background: #f0fdf9;
      }

      .btn-icon-del {
        border: none;
        background: transparent;
        color: #c0392b;
        cursor: pointer;
        font-size: 16px;
      }

      .btn-icon-del:hover {
        color: #922b21;
      }

      #txtpemeriksaan {
        background: #f5f5f5;
      }
    </style>
  </head>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="js/jquery.js"></script>
  <script type="text/javascript" src="js/sanie.js"></script>
  <script src="js/sweetalert.min.js"></script>
  <script>
    $(document).ready(function () {
      setInterval(timestamp, 1000);
    });
    function timestamp() { $.ajax({ url: 'inc/timestamp.php', success: function (data) { $('#timestamp').html(data); }, }); }
  </script>

  <body onLoad="periksaakses('PASS_LABO_ENTR');">
    <div id="wrapper">
      <?php include "inc/side-menu.php"; ?>

      <div id="content-wrapper">
        <?php include "inc/header.php"; ?>
        <div class="content">
          <div class="content-modern">
            <form name="frmtrxalabo" id="frmtrxalabo" class="pure-form pure-form-aligned" method="post" action=""
              onsubmit="return false;">

              <div class="card-modern">
                <div class="card-title">Data Pasien</div>

                <div class="form-grid">
                  <div class="form-group">
                    <label class="form-label" for="txtlaboregi">Nomor Daftar :</label>
                    <input class="form-control" type="text" name="txtlaboregi" id="txtlaboregi" maxlength="14"
                      readonly="true">
                    <input type="hidden" name="hidlabodoct" id="hidlabodoct" value="<?php echo $user; ?>">
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="txtpaticode">Rekam Medis :</label>
                    <input class="form-control" type="text" name="txtpaticode" id="txtpaticode" maxlength="10"
                      readonly="true">
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="txtmainname">Nama :</label>
                    <input class="form-control" type="text" name="txtmainname" id="txtmainname" maxlength="50"
                      readonly="true">
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="txtmaingend">L/P :</label>
                    <input class="form-control" type="text" name="txtmaingend" id="txtmaingend" maxlength="10"
                      readonly="true">
                    <input type="hidden" name="hidmaingend" id="hidmaingend">
                    <input type="hidden" name="hidmaintitl" id="hidmaintitl">
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="txtmainage">Usia :</label>
                    <input class="form-control" type="text" name="txtmainage" id="txtmainage" maxlength="50"
                      readonly="true">
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="txtbirtdate">Tgl Lahir :</label>
                    <input class="form-control" type="text" name="txtbirtdate" id="txtbirtdate" maxlength="14"
                      readonly="true">
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="txtmainaddr">Alamat :</label>
                    <input class="form-control" type="text" name="txtmainaddr" id="txtmainaddr" maxlength="50"
                      readonly="true">
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="txtregipaym">Pembayaran :</label>
                    <input class="form-control" type="text" name="txtregipaym" id="txtregipaym" maxlength="50"
                      readonly="true">
                  </div>
                </div>
              </div>

              <div class="form-grid-2 pati">
                <div class="card-modern">
                  <div class="card-title">Input Hasil Pemeriksaan</div>

                  <div class="form-group-full">
                    <label class="form-label" for="txtpemeriksaan">Pemeriksaan :</label>
                    <div class="pemeriksaan-row">
                      <select class="form-control" name="txtpemeriksaan" id="txtpemeriksaan"
                        onchange="onPemeriksaanChange(this)">
                        <option value="">-- Pilih / muat dari order --</option>
                      </select>
                      <button type="button" id="btnTambahItem" title="Tambah Item" onclick="tambahBarisManual();">
                        <i class="bi bi-plus"></i>
                      </button>
                    </div>
                    <input type="hidden" name="hidmedicode" id="hidmedicode" value="">
                    <input type="hidden" name="hidtempcode" id="hidtempcode" value="">
                  </div>

                  <div class="table-container">
                    <table id="lab-items-table">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Item</th>
                          <th>Hasil</th>
                          <th>Rujukan</th>
                          <th>Satuan</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody id="lab-items-container">
                        <tr>
                          <td colspan="6" style="text-align:center;color:#888;">Pilih pasien & pemeriksaan untuk memuat
                            item lab</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <!-- <div class="form-group" style="margin-top:12px;">
                    <label class="form-label" for="txtlabonote">Remark :</label>
                    <textarea class="form-control" id="txtlabonote" name="txtlabonote" rows="3" cols="30"></textarea>
                  </div> -->

                  <div>
                    <button type="button" class="btn-modern btn-save" style="margin-top: 10px; height: 38px;"
                      onclick="submitHasilLab();">Input Hasil</button>
                  </div>
                </div>

                <div class="card-modern">
                  <div class="card-title">BHP</div>
                </div>
              </div>

              <div class="card-modern">
                <div class="card-title">Hasil Pemeriksaan Laboratorium</div>
                <div id="tblscreen" style="display:none;"></div>
                <div id="tblviewresult" style="margin-top:16px;"></div>

                <div>
                  <button type="button" class="btn-modern btn-refresh" style="margin-top: 10px; height: 38px;"
                    onclick="printHasilLab();">Print Hasil</button>
                </div>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="footerdate">
      <span class="labelTime Time"><b>Date :</b>
        <?php $tgl = date('d-m-Y');
        echo $tgl; ?>
      </span>
    </div>
    <div class="footertime">
      <span class="labelTime Time" id="timestamp"></span>
    </div>
    <script src="js/TRXALABO05.js"></script>
    <script src="js/ui.js"></script>
    <script>
      (function () {
        var params = new URLSearchParams(window.location.search);
        var regicode = params.get('regicode');
        var paticode = params.get('paticode');
        if (regicode && paticode) {
          setTimeout(function () {
            if (typeof viewcode === 'function') {
              viewcode(regicode, paticode);
            }
          }, 500);
        }
      })();
    </script>
  </body>

  </html>
  <?php
} else {
  header("Location: " . "signin.php");
}
?>