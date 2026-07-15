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
<title>Riwayat Medis</title>
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

  .button-print {
            background: rgb(223, 117, 20);
            /* this is an orange */
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
    <link rel="stylesheet" href="assets/css/modern-table.css">`n</head>
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

<body onLoad="periksaakses('PASS_MEDI_REPO'); 
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
                Riwayat Medis
              </li>

              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'MEDIRECO02.php'">
                <a class="pure-menu-link">
                Statistik ICD 10
                </a>
              </li>

            </ul>
          </div>
    <!-- Tab Menu -->

    <!-- Form Input -->
    <form name="frmmedireco" class="pure-form pure-form-aligned" method="post" action="MEDIRECO01P.php">
    	<fieldset>

      		<div class="pure-control-group">

        		<label for="txtmastcode">No. RM :</label>
              <input type="text"	name="txtmastcode"	id="txtmastcode"
              maxlength ="10"	style="width: 400px;"  	readonly="true">

            <label for="txtmaingend">Gender :</label>
              <input type="text"  name="txtmaingend"  id="txtmaingend" 
              maxlength ="10" style="width: 300px;"   readonly="true">

          </div><!-- pure-control-group -->

          <div class="pure-control-group">

            <label for="txtmainname">Nama :</label>
              <input type="text"  name="txtmainname"  id="txtmainname" 
              maxlength ="10" style="width: 400px;"   readonly="true">

            <label for="txtmainpidn">No. Identitas :</label>
              <input type="text"  name="txtmainpidn"  id="txtmainpidn" 
              maxlength ="10" style="width: 300px;"   readonly="true">

          </div><!-- pure-control-group -->

          <div class="pure-control-group">

            <label for="txtmainbirt">Tgl. Lahir :</label>
              <input type="text"  name="txtmainbirt"  id="txtmainbirt" 
              maxlength ="10" style="width: 400px;"   readonly="true">

            <label for="txtmainphne">No. Telp. :</label>
              <input type="text"  name="txtmainphne"  id="txtmainphne" 
              maxlength ="10" style="width: 300px;"   readonly="true">

          </div><!-- pure-control-group -->

          <div class="pure-control-group">

            <label for="txtmainage">Umur :</label>
              <input type="text"  name="txtmainage"  id="txtmainage" 
              maxlength ="10" style="width: 400px;"   readonly="true">

            <label for="txtmainprof">Pekerjaan :</label>
              <input type="text"  name="txtmainprof"  id="txtmainprof" 
              maxlength ="10" style="width: 300px;"   readonly="true">

          </div><!-- pure-control-group -->

          <div class="pure-control-group">

            <label for="txtmainaddr">Alamat :</label>

            <textarea id="txtmainaddr" name="txtmainaddr"
            rows="2" cols="30">
              
            </textarea>


            <label for="txtmainalergi">Penyakit/Alergi :</label>
            <textarea id="txtmainalergi" name="txtmainalergi"
            rows="2" cols="30">
            </textarea>

          </div><!-- pure-control-group -->

          	<div class="pure-control-group">


          <a class="pure-button pure-button-primary" onclick="javascript:
                  if (document.getElementById('txtmainalergi').value == '')
                  {
                    swal({
                        title: 'Keterangan Penyakit Riwayat Alergi Kosong' ,
                        text: 'Masukkan Keterangan jika ingin melakukan Update',
                        icon: 'warning',
                        });
                  }

                  else
                  {
                       var inmastcode = document.getElementById('txtmastcode').value;
                       var inmainalergi = document.getElementById('txtmainalergi').value;

                       input(inmastcode, inmainalergi);
                    }
        ">Update</a>

        <a class="pure-button button-print" onclick="javascript:
                  if (document.getElementById('txtmastcode').value == '') 
                  {
                    swal({
                        title: 'Data belum di pilih' ,
                        text: 'Pilih Data Rekam Medis yang ingin di cetak',
                        icon: 'warning',
                        });
                  }
                  else 
                    { document.frmmedireco.submit();  }">Print</a>


          	</div><!-- pure-control-group --> 

      </fieldset>

      <fieldset>
          <div id="tblviewrm">  
      </fieldset>

        <fieldset>
        <label for="txtsearch">Cari...</label>
        <input type="text" class="pure-input-rounded"
            name="txtsearch" 
            id="txtsearch" 
            maxlength ="20"
            style="width: 200px;"
            onkeyup="if (value.length > 0) { ambilscreen(this.value); } else {ambilscreen('')};"

            onkeydown="if (event.keyCode == 13 && value.length > 0) 
                        { 
                        document.getElementById('txtsearch').value = '';
                        document.getElementById('txtsearch').focus()
                        }">
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
<script src="js/MEDIRECO01.js"></script>


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

