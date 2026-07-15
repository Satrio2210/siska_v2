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
<title>Pengajuan Pembayaran</title>
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
      width: 100px;
      height: 100px;
      position: relative;
      top: 0px;
      left: 0px;
      }

	.button-view {
    	background: rgb(28, 184, 65);
            /* this is an green */
        }

	.button-delete {
            background: rgb(202, 60, 60);
            /* this is an maroon */
        }

</style>

<style type="text/css">
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
<script src="js/sanie.js"></script>
<script src="js/sweetalert.min.js"></script>

<script>
$(document).ready(function() 
{
    setInterval(timestamp, 1000);
});  
    function timestamp() { $.ajax({ url: 'inc/timestamp.php', success: function(data) { $('#timestamp').html(data); }, }); }
</script>

<body onLoad="periksaakses('PASS_VEND_ENTR');
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

              <li class="pure-menu-item pure-menu-disabled">
                Pengajuan Pembayaran
              </li>

              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TRXAVEND02.php'">
                <a class="pure-menu-link">
                Persetujuan Pembayaran
                </a>
              </li>

              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TRXAVEND03.php'">
                <a class="pure-menu-link">
                Eksekusi Pembayaran
                </a>
              </li>

            </ul>
          </div>
    <!-- Tab Menu -->

    <!-- Form Input -->
    <form name="frmtrxavend" class="pure-form pure-form-aligned" method="post" action="">
    	<fieldset>

          <div class="pure-control-group">

            <label for="txtvendcode">Transaction Code :</label>
                <input type="text" 
                     name="txtvendcode" 
                     id="txtvendcode"
                     maxlength ="7"
                     style="width: 90px;"
                     readonly = "true">

            <label for="tglvenddate">Transaction Date :</label>
                <input type="date" name="tglvenddate" id="tglvenddate" value="<?php echo $datenow; ?>"
                    onchange="document.getElementById('txtsuplname').focus()">

                    <input type="hidden" name="hidproccode" id="hidproccode">
                    <input type="hidden" name="hidprocdivi" id="hidprocdivi">
          </div><!-- pure-control-group -->

      		<div class="pure-control-group">

        		<label for="txtsuplname">Suplier Name :</label>
              <input type="text" 
            	  	name="txtsuplname" 
              		id="txtsuplname" 
              		maxlength ="20"
              		style="width: 300px;"
                  readonly="true">

                  <input type="hidden" name="hidsuplcode" id="hidsuplcode">

                  <input type="hidden" name="hidprocvatx" id="hidprocvatx">
                  <input type="hidden" name="hiddownpaid" id="hiddownpaid">
                  <input type="hidden" name="hidremapaid" id="hidremapaid">
                  <input type="hidden" name="hidinvccode" id="hidinvccode">
                  <input type="hidden" name="hiddueddate" id="hiddueddate">

          </div><!-- pure-control-group -->


      </fieldset>


      <fieldset>
          <a class="pure-button pure-button-primary" onclick="javascript:

                  if (document.getElementById('txtvendcode').value == '')
                  {
                    swal({
                        title: 'Kode Transaksi Pembayaran Kosong' ,
                        text: 'Pastikan Akses Form sudah aktif',
                        icon: 'warning',
                        });
                  }
                  else if (document.getElementById('txtsuplname').value == '')
                  {
                    swal({
                        title: 'Nama Suplier belum dipilih' ,
                        text: 'Pastikan Akses Form sudah aktif',
                        icon: 'warning',
                        });

                  }

                   else
                   {
                       var invendcode = document.getElementById('txtvendcode').value;
                       var invenddate = document.getElementById('tglvenddate').value;
                       var inproccode = document.getElementById('hidproccode').value;
                       var inprocdivi = document.getElementById('hidprocdivi').value;
                       var insuplcode = document.getElementById('hidsuplcode').value;
                       var inprocvatx = document.getElementById('hidprocvatx').value;
                       var indownpaid = document.getElementById('hiddownpaid').value;
                       var inremapaid = document.getElementById('hidremapaid').value;
                       var ininvccode = document.getElementById('hidinvccode').value;
                       var indueddate = document.getElementById('hiddueddate').value;

                       input(invendcode,invenddate,inproccode,inprocdivi,insuplcode,inprocvatx, indownpaid, inremapaid, ininvccode, indueddate);
                       //document.frmtrxavend.submit();
                    }
        ">Ajukan</a>
        </fieldset>
      <fieldset>
          <div id="tblscreen">
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
<script src="js/TRXAVEND01.js"></script>


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
