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
    <title>Report Penjualan Obat</title>
    <link rel="shortcut icon" href="assets/img/icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/layouts/header.css">
    <link rel="stylesheet" href="assets/css/bootstrap/bootstrap.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/pure/pure-min.css">
    <!--[if lte IE 8]>
  <link rel="stylesheet" href="assets/css/layouts/side-menu-old-ie.css">
<![endif]-->
    <!--[if gt IE 8]><!-->
    <link rel="stylesheet" href="assets/css/layouts/side-menu.css">
    <!--<![endif]-->
    <style type="text/css">
      

      .button-print {
        background: rgb(223, 117, 20);
        /* this is an orange */
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

  <body onLoad="
        periksaakses('PASS_DRUG_VIEW');
        ambilviewrepo(
          document.getElementById('tglstartdate').value,
          document.getElementById('tglenddate').value
        );
">
    <div id="wrapper">
      <!-- Side Menu -->
      <?php include "inc/side-menu.php"; ?>

      <!-- tampilan menu -->
      <div id="content-wrapper">
<?php include "inc/header.php"; ?>

        <div class="content">

          <!-- Tab Menu -->
          <div class="pure-menu pure-menu-horizontal">
            <ul class="pure-menu-list">

              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TRXADRUG04.php'">
                <a class="pure-menu-link">
                  Harian / Bulanan
                </a>
              </li>

              <li class="pure-menu-item pure-menu-disabled">
                Resep Harian
              </li>

              <!-- <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TRXADRUG05.php'">
                <a class="pure-menu-link">
                Pemakaian Obat
                </a>
              </li>

              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TRXADRUG06.php'">
                <a class="pure-menu-link">
                Obat Kadaluarsa
                </a>
              </li>

              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TRXADRUG07.php'">
                <a class="pure-menu-link">
                Harga Obat
                </a>
              </li> -->

            </ul>
          </div>
          <!-- Tab Menu -->

          <form name="frmreport" class="pure-form" method="post" action="TRXADRUG08P.php">

            <fieldset>
              <label for="tglstartdate">Start</label>

              <input type="date" name="tglstartdate" id="tglstartdate" value="<?php echo $datenow; ?>" onchange="document.getElementById('tglenddate').value = this.value;
                    ambilviewrepo(this.value,document.getElementById('tglenddate').value);">

              <label for="tglenddate">End</label>

              <input type="date" name="tglenddate" id="tglenddate" value="<?php echo $datenow; ?>"
                onchange="ambilviewrepo(document.getElementById('tglstartdate').value,this.value);">

              <!-- <input type="text" class="pure-input-rounded" name="txtnamafarmasi" id="txtnamafarmasi" maxlength="20"
                style="width: 200px;" onkeyup="if (value.length > 0) { ambilviewprice(this.value); };" onkeydown="if (event.keyCode == 13 && value.length > 13) 
                        { 
                        document.getElementById('txtstockcode').value = '';
                        document.getElementById('txtstockcode').focus()
                        }"> -->

              <!-- <a class="pure-button button-print" onclick="javascript: document.frmreport.submit();">Export Excel</a> -->
              <button type="submit" class="pure-button button-print">Export Excel</button>

            </fieldset>

            <fieldset>
              <input type="radio" id="semua" name="jenispasien" value="" onchange="ambilviewrepo(
              document.getElementById('tglstartdate').value,
              document.getElementById('tglenddate').value);" checked>
              <label for="semua">SEMUA</label>

              <input type="radio" id="bpjs" name="jenispasien" value="B" onchange="ambilviewrepo(
              document.getElementById('tglstartdate').value,
              document.getElementById('tglenddate').value);">
              <label for="bpjs">BPJS</label>

              <input type="radio" id="umum" name="jenispasien" value="U" onchange="ambilviewrepo(
              document.getElementById('tglstartdate').value,
              document.getElementById('tglenddate').value);">
              <label for="umum">UMUM</label>

              <br><br>

              <label for="dokter">DOKTER:</label>
              <select id="dokter" name="dokter" onchange="ambilviewrepo(
              document.getElementById('tglstartdate').value,
              document.getElementById('tglenddate').value);">
                <option value="">- SEMUA DOKTER -</option>
                <?php
                $q_doct = $db->query("SELECT DISTINCT 
                    passiden.PASS_USER_IDEN AS DOCT_CODE,
                    passiden.PASS_USER_NAME AS DOCT_NAME
                FROM trxaprsc 
                JOIN passiden ON trxaprsc.TRXA_PRSC_DOCT = passiden.PASS_USER_IDEN
                WHERE trxaprsc.TRXA_PRSC_STAT IN ('A', 'I', 'P')
                ORDER BY passiden.PASS_USER_NAME ASC");
                while ($d = $q_doct->fetch(PDO::FETCH_ASSOC)) {
                    echo '<option value="' . $d['DOCT_CODE'] . '">' . $d['DOCT_NAME'] . '</option>';
                }
                ?>
              </select>
            </fieldset>

            <fieldset>
              <div id="tblviewrepo">
            </fieldset>
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
    <script src="js/TRXADRUG08.js"></script>
    

      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/ui.js"></script>


  </body>

  </html>
  <?php
} else {
  header("Location: " . "signin.php");
}
?>