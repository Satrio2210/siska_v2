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
<title>Lokasi</title>
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
<script src="js/sweetalert.min.js"></script>

<script>
$(document).ready(function() 
{
    setInterval(timestamp, 1000);
});  
    function timestamp() { $.ajax({ url: 'inc/timestamp.php', success: function(data) { $('#timestamp').html(data); }, }); }
</script>

<body onLoad="periksaakses('PASS_WARE_HOUS');
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
                Lokasi
              </li>

              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'WAREMAST01.php'">
                <a class="pure-menu-link">
                Ware House
                </a>
              </li>


            </ul>
          </div>
    <!-- Tab Menu -->

    <!-- Form Input -->
    <form name="frmfixeloca" class="pure-form pure-form-aligned" method="post" action="">
    	<fieldset>

      		<div class="pure-control-group">

        		<label for="txtlocacode">Kode Lokasi :</label>
              <input type="text" 
            	  	name="txtlocacode" 
              		id="txtlocacode" 
              		maxlength ="3"
              		style="width: 100px;"
                  onkeyup="var start = this.selectionStart;
                          var end = this.selectionEnd;
                          this.value = this.value.toUpperCase();
                          this.setSelectionRange(start, end);
                          if (value.length > 2) 
                            {
                              periksaid(this.value);
                            }
                          else
                          {
                            document.getElementById('txtlocaname').setAttribute('disabled','true');
                          }  
                          "
                onkeydown="if (event.keyCode == 13 && value.length > 2) document.getElementById('txtlocaname').focus()">

          </div><!-- pure-control-group --> 

          <div class="pure-control-group">

            <label for="txtlocaname">Nama Lokasi :</label>
              <input type="text" 
                  name="txtlocaname" 
                  id="txtlocaname" 
                  maxlength ="50"
                  style="width: 300px;"
                onkeydown="if (event.keyCode == 13 && value.length > 0) document.getElementById('txtlocaaddr').focus()">

          </div><!-- pure-control-group -->

          <div class="pure-control-group">

            <label for="txtlocaaddr">Alamat :</label>
              <input type="text" 
                  name="txtlocaaddr" 
                  id="txtlocaaddr" 
                  maxlength ="100"
                  style="width: 500px;"
                onkeydown="if (event.keyCode == 13 && value.length > 0) document.getElementById('txtemplname').focus()">

          </div><!-- pure-control-group -->

          <div class="pure-control-group">

            <label for="txtemplname">Nama Kontak :</label>
              <input type="text" 
                  name="txtemplname" 
                  id="txtemplname" 
                  maxlength ="50"
                  style="width: 300px;"
                autocomplete="off"
                onkeyup="if (value.length > 0) 
                  {
                  ambilemplcode(this.value);
                  } 
                else 
                  { 
                  document.getElementById('tblempl').style.visibility = 'hidden';
                  }"
              >

                <input type="hidden" name="hidemplcode" id="hidemplcode">

          </div><!-- pure-control-group -->


          <div class="pure-control-group">

          <label for="txtlocanote">Catatan :</label>
              <input type="text" 
              name="txtlocanote" 
              id="txtlocanote" 
              maxlength ="100" 
              style="width: 500px;"
              onkeydown="if (event.keyCode == 13 && value.length > 0)
              {
                  if (document.getElementById('txtlocacode').value == '')
                  {
                    swal({
                        title: 'Kode Lokasi Kosong' ,
                        text: 'Anda belum mengisi Kode Lokasi, silah periksa lagi',
                        icon: 'warning',
                        });
                    document.getElementById('txtlocacode').value = '';
                    document.getElementById('txtlocacode').focus();
                  }
                  else if (document.getElementById('txtlocaname').value == '')
                  {
                      swal({
                          title: 'Nama Lokasi Kosong' ,
                          text: 'Anda belum mengisi Nama Lokasi, silah periksa lagi',
                          icon: 'warning',
                          });
                      document.getElementById('txtlocaname').value = '';
                      document.getElementById('txtlocaname').focus();
                  }
                  else if (document.getElementById('txtlocaaddr').value == '')
                  {
                      swal({
                          title: 'Alamat Lokasi Kosong' ,
                          text: 'Anda belum mengisi Alamat Lokasi, silah periksa lagi',
                          icon: 'warning',
                          });
                      document.getElementById('txtlocaaddr').value = '';
                      document.getElementById('txtlocaaddr').focus();
                  }
                  else if (document.getElementById('hidemplcode').value == '')
                  {
                      swal({
                          title: 'Nama Staff Kosong' ,
                          text: 'Anda belum mengisi Nama Staff Lokasi, silah periksa lagi',
                          icon: 'warning',
                          });
                      document.getElementById('txtemplname').value = '';
                      document.getElementById('txtemplname').focus();
                  }

                   else
                   {
                       var inlocacode = document.getElementById('txtlocacode').value;
                       var inlocaname = document.getElementById('txtlocaname').value;
                       var inlocaaddr = document.getElementById('txtlocaaddr').value;
                       var inemplcode = document.getElementById('hidemplcode').value;
                       var inlocanote = document.getElementById('txtlocanote').value;
                       input(inlocacode,inlocaname,inlocaaddr, inemplcode, inlocanote);
                    }

              }"
              >
            </div><!-- pure-control-group --> 
      </fieldset>
      <fieldset>
          <a class="pure-button pure-button-primary" onclick="javascript:
			if ( document.getElementById('txtlocanote').value.length > 0)
              {
                  if (document.getElementById('txtlocacode').value == '')
                  {
                    swal({
                        title: 'Kode Lokasi Kosong' ,
                        text: 'Anda belum mengisi Kode Lokasi, silah periksa lagi',
                        icon: 'warning',
                        });
                    document.getElementById('txtlocacode').value = '';
                    document.getElementById('txtlocacode').focus();
                  }
                  else if (document.getElementById('txtlocaname').value == '')
                  {
                      swal({
                          title: 'Nama Lokasi Kosong' ,
                          text: 'Anda belum mengisi Nama Lokasi, silah periksa lagi',
                          icon: 'warning',
                          });
                      document.getElementById('txtlocaname').value = '';
                      document.getElementById('txtlocaname').focus();
                  }
                  else if (document.getElementById('txtlocaaddr').value == '')
                  {
                      swal({
                          title: 'Alamat Lokasi Kosong' ,
                          text: 'Anda belum mengisi Alamat Lokasi, silah periksa lagi',
                          icon: 'warning',
                          });
                      document.getElementById('txtlocaaddr').value = '';
                      document.getElementById('txtlocaaddr').focus();
                  }
                  else if (document.getElementById('hidemplcode').value == '')
                  {
                      swal({
                          title: 'Nama Staff Kosong' ,
                          text: 'Anda belum mengisi Nama Staff Lokasi, silah periksa lagi',
                          icon: 'warning',
                          });
                      document.getElementById('txtemplname').value = '';
                      document.getElementById('txtemplname').focus();
                  }

                   else
                   {
                       var inlocacode = document.getElementById('txtlocacode').value;
                       var inlocaname = document.getElementById('txtlocaname').value;
                       var inlocaaddr = document.getElementById('txtlocaaddr').value;
                       var inemplcode = document.getElementById('hidemplcode').value;
                       var inlocanote = document.getElementById('txtlocanote').value;
                       input(inlocacode,inlocaname,inlocaaddr, inemplcode, inlocanote);
                    }

              }        ">Submit</a>
        </fieldset>
        <fieldset>
                <div id="tblempl" 
              style="position: absolute; 
                 top: 200px;
                 left: calc(70% - 150px);
                 background-color: white; 
                 visibility: hidden; 
                 z-index: 100">
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
<script src="js/FIXELOCA01.js"></script>


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
