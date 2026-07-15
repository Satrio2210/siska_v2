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
<title>Ruangan</title>
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
    <link rel="stylesheet" href="assets/css/modern-table.css">`n</head>
<script type="text/javascript" src="js/jquery.js"></script>
<script src="js/sweetalert.min.js"></script>

<script>
$(document).ready(function() 
{
    setInterval(timestamp, 1000);
});  
    function timestamp() { $.ajax({ url: 'inc/timestamp.php', success: function(data) { $('#timestamp').html(data); }, }); }
</script>

<body onLoad="periksaakses('PASS_REGI_ENTR');
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

              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TRXAPATI01.php'">
                <a class="pure-menu-link">
                Daftar Pasien
                </a>
              </li>

              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TRXAPATI02.php'">
                <a class="pure-menu-link">
                Pasien Berobat
                </a>
              </li>

              <li class="pure-menu-item pure-menu-disabled">
                Ruangan
              </li>

              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TRXAPATI04.php'">
                <a class="pure-menu-link">
                Jadwal Dokter
                </a>
              </li>

              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TRXAPATI07.php'">
                <a class="pure-menu-link">
                Pasien Periksa 
                </a>
              </li>

            </ul>
          </div>
    <!-- Tab Menu -->

    <!-- Form Input -->
    <form name="frmtrxapati" class="pure-form pure-form-aligned" method="post" action="">
    	<fieldset>

      		<div class="pure-control-group">

        		<label for="txtpolicode">Kode Poli :</label>
              	<input type="text" 
            	  	name="txtpolicode" 
              		id="txtpolicode" 
              		maxlength ="2"
              		style="width: 50px;"
                  onkeyup="var start = this.selectionStart;
                          var end = this.selectionEnd;
                          this.value = this.value.toUpperCase();
                          this.setSelectionRange(start, end);
                          if (value.length > 1) 
                            {
                              periksaid(this.value);
                            }
                          else
                          {
                            document.getElementById('txtpoliname').setAttribute('disabled','true');
                          }  
                          "
                onkeydown="if (event.keyCode == 13 && value.length > 2) document.getElementById('txtpoliname').focus()">

          </div><!-- pure-control-group --> 

          <div class="pure-control-group">

            <label for="txtpoliname">Nama Poli :</label>
              <input type="text" 
                  name="txtpoliname" 
                  id="txtpoliname" 
                  maxlength ="50"
                  style="width: 300px;"
              onkeydown="if (event.keyCode == 13 && value.length > 0)
              {
                  if (document.getElementById('txtpolicode').value == '')
                  {
                    swal({
                        title: 'Kode Ruangan Kosong' ,
                        text: 'Anda belum mengisi Kode Ruangan, silah periksa lagi',
                        icon: 'warning',
                        });
                    document.getElementById('txtpolicode').value = '';
                    document.getElementById('txtpolicode').focus();
                  }
                  else if (document.getElementById('txtpoliname').value == '')
                  {
                      swal({
                          title: 'Nama Ruangan Kosong' ,
                          text: 'Anda belum mengisi Nama Ruangan, silah periksa lagi',
                          icon: 'warning',
                          });
                      document.getElementById('txtpoliname').value = '';
                      document.getElementById('txtpoliname').focus();
                  }

                   else
                   {
                       var inpolicode = document.getElementById('txtpolicode').value;
                       var inpoliname = document.getElementById('txtpoliname').value;
                       input(inpolicode,inpoliname);
                    }

              }"
              >

          </div><!-- pure-control-group -->

      </fieldset>
      <fieldset>
          <a class="pure-button pure-button-primary" onclick="javascript:
                  if (document.getElementById('txtpolicode').value == '')
                  {
                    swal({
                        title: 'Kode Ruangan Kosong' ,
                        text: 'Anda belum mengisi Kode Ruangan, silah periksa lagi',
                        icon: 'warning',
                        });
                    document.getElementById('txtpolicode').value = '';
                    document.getElementById('txtpolicode').focus();
                  }
                  else if (document.getElementById('txtpoliname').value == '')
                  {
                      swal({
                          title: 'Nama Ruangan Kosong' ,
                          text: 'Anda belum mengisi Nama Ruangan, silah periksa lagi',
                          icon: 'warning',
                          });
                      document.getElementById('txtpoliname').value = '';
                      document.getElementById('txtpoliname').focus();
                  }

                   else
                   {
                       var inpolicode = document.getElementById('txtpolicode').value;
                       var inpoliname = document.getElementById('txtpoliname').value;
                       input(inpolicode,inpoliname);
                    }

        ">Submit</a>
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
<script src="js/TRXAPATI03.js"></script>


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

