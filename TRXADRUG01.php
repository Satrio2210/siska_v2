<!doctype html>
<?php include "conf/config.php";
//memulai session
session_start();


//cek adanya session
if (isset($_SESSION['username'])) {
  $user = $_SESSION['username'];

  ?>
  <html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Klinik Pratama">
    <title>Farmasi</title>
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
      /* Modal */
      .modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(15, 23, 42, .45);
        backdrop-filter: blur(4px);
      }

      .modal-content {
        background: #fff;
        width: 92%;
        max-width: 650px;
        margin: 60px auto;
        border-radius: 20px;
        overflow: hidden;
        box-shadow:
          0 25px 50px rgba(15, 23, 42, .18);
      }

      .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        background: linear-gradient(90deg,
            #16a34a,
            #22c55e);
      }

      .modal-title {
        color: white;
        font-size: 16px;
        font-weight: 700;
      }

      .modal-close {
        border: none;
        background: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
      }

      .modal-content {
        padding-bottom: 20px;
      }

      .form-group {
        padding: 0 20px;
        margin-top: 16px;
      }

      .form-group label {
        display: block;
        margin-bottom: 6px;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
      }

      .form-group input,
      .form-group select {
        width: 100%;
        box-sizing: border-box;
        padding: 11px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 13px;
      }

      .form-group input:focus,
      .form-group select:focus {
        outline: none;
        border-color: #16a34a;
        box-shadow: 0 0 0 4px rgba(22, 163, 74, .12);
      }

      .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 20px;
        margin-top: 10px;
      }

      .btn-modal-cancel {
        border: none;
        border-radius: 10px;
        background: #e2e8f0;
        color: #334155;
        padding: 10px 16px;
        cursor: pointer;
        font-weight: 700;
      }

      .btn-modal-save {
        border: none;
        border-radius: 10px;
        background: #16a34a;
        color: white;
        padding: 10px 18px;
        cursor: pointer;
        font-weight: 700;
      }

      .btn-modal-save:hover {
        background: #15803d;
      }

      /* End Modal */

      /* Punya Detail */
      .detail-card {
        background: #fff;
        border: 1px solid #dbe4ee;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(15, 23, 42, .04),
          0 8px 24px rgba(15, 23, 42, .06);
      }

      .detail-title {
        padding: 12px 16px;
        background: linear-gradient(90deg, #16a34a, #22c55e);
        color: white;
        font-weight: 700;
      }

      .detail-body {
        padding: 16px;
      }

      #tbldetail {
        width: 100%;
        border-collapse: collapse;
      }

      #tbldetail th {
        background: #f8fafc;
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
      }

      #tbldetail td {
        padding: 10px;
        border-bottom: 1px solid #f1f5f9;
      }

      .btn-edit {
        background: #dbeafe;
        color: #1d4ed8;
      }

      .btn-delete {
        background: #fee2e2;
        color: #b91c1c;
      }

      .btn-edit,
      .btn-delete {
        border: none;
        border-radius: 8px;
        padding: 6px 10px;
        cursor: pointer;
        margin-right: 5px;
      }

      .btn-siapkan {
        background: #16a34a;
        color: white;
        border: none;
        border-radius: 10px;
        padding: 12px 18px;
        font-weight: 700;
        cursor: pointer;
      }
    </style>

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

  <body onLoad="periksaakses('PASS_DRUG_ENTR');">
    <div id="wrapper">
      <!-- Side Menu -->
      <?php include "inc/side-menu.php"; ?>

      <!-- tampilan menu -->
      <div id="content-wrapper">
        <?php include "inc/header.php"; ?>
        <div class="content">
          <div class="content-modern">

            <div class="card-modern">
              <div class="card-title">Daftar Resep Masuk</div>

              <input type="text" id="txtsearch" autocomplete="off" class="form-control" name="txtsearch"
                placeholder="Cari Pasien / No Antrian..." maxlength="20"
                onkeyup="if (value.length > 0) { ambilscreen(this.value); } else {ambilscreen('')};">

              <div id="tblscreen"></div>
            </div>

            <!-- Detail Resep Card-->
            <div id="resepdetail"></div>

            <div id="modalEditObat" class="modal">

              <div class="modal-content">

                <div class="modal-header">

                  <div class="modal-title">
                    Edit Obat
                  </div>

                  <button type="button" class="modal-close" onclick="closemodal()">
                    Ã—
                  </button>

                </div>

                <input type="hidden" id="edit_prsccode">
                <input type="hidden" id="edit_stockcode">

                <div class="form-group">
                  <label>Nama Obat</label>
                  <input type="text" id="edit_stockname" readonly>
                </div>

                <div class="form-grid">

                  <div class="form-group">
                    <label>Qty</label>
                    <input type="number" id="edit_qty">
                  </div>

                  <div class="form-group">
                    <label>Jenis</label>
                    <select id="edit_conc">
                      <option value="N">Non Racikan</option>
                      <option value="Y">Racikan</option>
                    </select>
                  </div>

                </div>

                <div class="form-group">
                  <label>Batch</label>
                  <input type="text" id="edit_batch" readonly>
                </div>

                <div class="modal-footer">

                  <button type="button" class="btn-modal-cancel" onclick="closemodal()">

                    Batal

                  </button>

                  <button type="button" class="btn-modal-save" onclick="simpanobat()">

                    Simpan Perubahan

                  </button>

                </div>

              </div>

            </div>

            </form>

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
    </div>

    <script src="js/TRXADRUG01.js"></script>
    <script src="js/ui.js"></script>

  </body>

  </html>
  <?php
} else {
  header("Location: " . "signin.php");
}
?>