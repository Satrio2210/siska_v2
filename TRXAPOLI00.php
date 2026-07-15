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
    <title>Pemeriksaan Pasien</title>
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
      #tblscreen {
        position: relative !important;
        top: auto !important;
        left: auto !important;
        width: 100%;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        margin-top: 10px;
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

  <body onLoad="periksaakses('PASS_TRXA_POLI');">

    <div id="wrapper">

      <?php include "inc/side-menu.php"; ?>

      <div id="content-wrapper">

        <?php include "inc/header.php"; ?>

        <div class="content">
          <input type="hidden" id="hidexamdoct" value="<?php echo $user; ?>">
          <div class="content-modern">
            <div class="card-modern" id="daftarPasien">
              <div class="card-title">Pasien Terdaftar</div>
              <div id="tblscreen">
              </div>
            </div>
          </div>
        </div>

        <div class="footerdate">
          <span class="labelTime Time"><b>Date :</b> <?php $tgl = date('d-m-Y');
          echo $tgl; ?></span>
        </div>
        <div class="footertime">
          <span class="labelTime Time" id="timestamp"></span>
        </div>
      </div>
    </div>

    <script src="js/TRXAPOLI01.js"></script>
    <script src="js/ui.js"></script>
    <script>
      document.addEventListener('click', function (e) {
        const btn = e.target.closest('.button-panggil');
        if (!btn) return;

        const nomor = btn.getAttribute('data-noantri') || '';
        const nama = btn.getAttribute('data-nama') || '';
        const poli = btn.getAttribute('data-poli') || '';
        const channel = btn.getAttribute('data-channel') || 'POLI';

        const params = "channel=" + encodeURIComponent(channel)
          + "&nomor=" + encodeURIComponent(nomor)
          + "&nama=" + encodeURIComponent(nama)
          + "&poli=" + encodeURIComponent(poli);

        fetch('panggil_queue.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: params
        })
          .then(r => r.json())
          .then(res => {
            console.log('RESP panggil_queue POLI:', res);
          })
          .catch(err => {
            console.error('Error fetch panggil_queue POLI:', err);
          });
      });
    </script>
  </body>

  </html>
  <?php
} else {
  header("Location: " . "signin.php");
}
?>