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
        <title>Laboratorium</title>
        <link rel="shortcut icon" href="assets/img/logo.png">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="assets/css/bootstrap/bootstrap.css">
        <link rel="stylesheet" href="assets/css/layouts/header.css">
        <link rel="stylesheet" href="assets/css/layouts/side-menu.css">
        <link rel="stylesheet" href="assets/css/trxapati-shared.css">
        <link rel="stylesheet" href="assets/css/modern-table.css">`n</head>

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
                    <input type="hidden" id="hidlabodoct" value="<?php echo $user; ?>">
                    <div class="content-modern">
                        <div class="card-modern" id="daftarPasien">
                            <div class="card-title">Daftar Pasien Laboratorium</div>
                            <div id="tblscreen">
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

        <script src="js/TRXALABO00.js"></script>
        <script src="js/ui.js"></script>

    </body>

    </html>
    <?php
} else {
    header("Location: " . "signin.php");
}
?>
