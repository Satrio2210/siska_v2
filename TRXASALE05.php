<!doctype html>
<?php include "conf/config.php";
//memulai session
session_start();

//cek adanya session
if (ISSET($_SESSION['username']))
{
	$user = $_SESSION['username'];


?>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Sistem Informasi Klinik Pratama">
<title>Report Fee Dokter</title>
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
    img {
      width: 100px; height: 100px;
      position: relative;
      top: 0px; left: 0px;
        }

  .button-print {
            background: rgb(223, 117, 20);
            /* this is an orange */
        }

	div.footerdate {
   position: fixed;
   left: 50;
   bottom: 50px;
   width: 90%;
   color: black;
   text-align: right;
}
div.footertime {
   position: fixed;
   left: 50;
   bottom: 20px;
   width: 90%;
   color: black;
   text-align: right;
}
</style>
</head>
<script type="text/javascript" src="js/jquery.js"></script>
<script>
$(document).ready(function() 
{
    setInterval(timestamp, 1000);
});  
    function timestamp() { $.ajax({ url: 'inc/timestamp.php', success: function(data) { $('#timestamp').html(data); }, }); }
</script>

<body onLoad="periksaakses('PASS_SALE_VIEW');
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

              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TRXASALE03.php'">
                <a class="pure-menu-link">
                Harian / Bulanan
                </a>
              </li>

              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TRXASALE04.php'">
                <a class="pure-menu-link">
                Fee Analis Laboratorium
                </a>
              </li>

              <li class="pure-menu-item pure-menu-disabled">
                Fee Dokter
              </li>

              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TRXASALE06.php'">
                <a class="pure-menu-link">
                Fee Bidan
                </a>
              </li>


            </ul>
          </div>
    <!-- Tab Menu -->

      <form name="frmreport" class="pure-form" method="post" action="TRXASALE05P.php">

      <fieldset>
          <label for="tglstartdate">Start</label>

          <input type="date" 
          name="tglstartdate" 
          id="tglstartdate"
          value="<?php echo $datenow;?>" 
          onchange="document.getElementById('tglenddate').value = this.value;
                    ambilviewrepo(this.value,document.getElementById('tglenddate').value);">

          <label for="tglenddate">End</label>

          <input type="date" 
          name="tglenddate" 
          id="tglenddate"
          value="<?php echo $datenow;?>" 
          onchange="ambilviewrepo(document.getElementById('tglstartdate').value,this.value);">
          <a class="pure-button button-print" 
          onclick="javascript: document.frmreport.submit();">Print</a>
      </fieldset>

      <fieldset>
        <div id="tblviewrepo">
      </fieldset>
  </form>   

		</div><!-- div content -->
<div class="footerdate">
  	<span class="labelTime Time"><b>Date  :</b> <?php $tgl=date('d-m-Y'); echo $tgl;?></span>
</div>
<div class="footertime">
	<span class = "labelTime Time" id="timestamp"></span>
</div>


    	</div>
</div>
<script src="js/TRXASALE05.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/ui.js"></script>


</body>
</html>
<?php
}
else

{
  header("Location: "."signin.php");
}
?>
