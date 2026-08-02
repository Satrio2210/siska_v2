<!DOCTYPE html>
<?php include "conf/config.php";
session_start();
if (isset($_SESSION['username'])) {
    $user = $_SESSION['username'];
    ?>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Data Pasien Rujukan RS</title>
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
    <script src="js/sweetalert.min.js"></script>
    <script>
      $(document).ready(function () {
        ambilscreen('', 1);
        setInterval(timestamp, 1000);
      });
      function timestamp() { $.ajax({ url: 'inc/timestamp.php', success: function (data) { $('#timestamp').html(data); }, }); }
    </script>

    <body>
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
                                <div class="card-title">&#x1F9D1; Data Pasien Rujukan RS &middot; Poli Umum</div>
                            </div>
                            <input type="text" name="txtsearch" id="txtsearch" class="form-control"
                                style="width: 250px;" placeholder="Cari nama / kode pendaftaran..." autocomplete="off"
                                onkeyup="if (value.length > 0) { ambilscreen(this.value, 1); } else { ambilscreen('', 1); }">

                            <div id="tblscreen" style="margin-top: 10px;"></div>
                        </div>
                    </div>
                </div>

                <!-- Modal Rekam Medis -->
                <div class="modal fade" id="modalRekamMedis" tabindex="-1" aria-labelledby="modalRekamMedisLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header" style="background: #f0fdfa; border-bottom: 2px solid #10b981;">
                                <h5 class="modal-title" id="modalRekamMedisLabel"
                                    style="font-weight:700; color:#0f766e;">&#x1F4DC; Rekam Medis Pasien</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="rekamMedisBody"
                                style="background:#f8fafc; padding:16px;">
                                <div class="rm-empty" style="text-align:center;color:#9ca3af;padding:30px;">Memuat data...
                                </div>
                            </div>
                        </div>
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
        <script src="js/TRXAPATI08.js"></script>
        <script src="js/ui.js"></script>
    </body>

    </html>
    <?php
} else {
    header("Location: " . "signin.php");
}
?>
