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
        <title>Rujukan</title>
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

    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/sanie.js"></script>
    <script src="js/sweetalert.min.js"></script>

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
                            <div class="card-title">&#x1F9D1; Data Pasien Rujukan</div>
                            <h1>RUJUKAN</h1>
                            <p>Testing</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="js/ui.js"></script>
    </body>

    </html>
    <?php
} else {
    header("Location: " . "signin.php");
}
?>