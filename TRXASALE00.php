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
        <title>Kasir</title>
        <link rel="shortcut icon" href="assets/img/logo.png">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="assets/css/bootstrap/bootstrap.css">
        <link rel="stylesheet" href="assets/css/layouts/header.css">
        <link rel="stylesheet" href="assets/css/layouts/side-menu.css">
        <link rel="stylesheet" href="assets/css/trxapati-shared.css">

        <style type="text/css">
            #tblscreen {
                width: 100%;
                margin-top: 10px;
            }

            #tblscreen .action-group {
                display: inline-flex;
                flex-direction: row;
                flex-wrap: nowrap;
                align-items: center;
                justify-content: center;
                gap: 6px;
            }

            #tblscreen .action-group a {
                display: inline-flex;
                flex: 0 0 auto;
                white-space: nowrap;
            }
        </style>

    </head>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/sanie.js"></script>
    <script src="js/sweetalert.min.js"></script>
    <script type="text/javascript" src="js/TRXASALE01.js"></script>

    <script>
        $(document).ready(function () {
            setInterval(timestamp, 1000);
        });
        function timestamp() { $.ajax({ url: 'inc/timestamp.php', success: function (data) { $('#timestamp').html(data); }, }); }
    </script>

    <body onLoad="periksaakses('PASS_TRXA_POLI'); 
">
        <div id="wrapper">

            <?php include "inc/side-menu.php"; ?>

            <div id="content-wrapper">
                <?php include "inc/header.php"; ?>
                <div class="content">
                    <div class="content-modern">
                        <div class="card-modern">
                            <div class="card-title">Daftar Pasien</div>

                            <div class="form-group">
                                <label class="form-label" for="txtregicode">Cari Pasien</label>
                                <input type="text" class="form-control" name="txtregicode" id="txtregicode" maxlength="20"
                                    style="margin-bottom: 10px; width: 250px;"
                                    onkeyup="if (value.length > 0) { ambilscreen(this.value); };" onkeydown="if (event.keyCode == 13 && value.length > 13) 
                                    { 
                                    document.getElementById('txtregicode').value = '';
                                    document.getElementById('txtregicode').focus()
                                    }">
                            </div>
                            <div id="tblscreen"></div>

                        </div>
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