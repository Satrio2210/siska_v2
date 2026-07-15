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
    <title>Data Signa</title>
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
      

      .button-view {
        background: rgb(28, 184, 65);
        /* this is an green */
      }

      .button-delete {
        background: rgb(202, 60, 60);
        /* this is an maroon */
      }
    </style>


      <link rel="stylesheet" href="assets/css/modern-table.css">`n</head>
  <script type="text/javascript" src="js/jquery.js"></script>
  <script src="js/sanie.js"></script>
  <script src="js/sweetalert.min.js"></script>

  <script>
    $(document).ready(function () {
      setInterval(timestamp, 1000);
    });
    function timestamp() { $.ajax({ url: 'inc/timestamp.php', success: function (data) { $('#timestamp').html(data); }, }); }
  </script>

  <!-- <body onLoad="periksaakses('PASS_TRXA_POLI');
"> -->

  <body onload="ambilscreen('');">
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
                Data Signa
              </li>


            </ul>
          </div>
          <!-- Tab Menu -->

          <!-- Form Input -->
          <form name="frmtrxapoli" class="pure-form pure-form-aligned" method="post" action="">
            <fieldset>

              <div class="pure-control-group">

                <label for="txtsgnacode">Kode Signa :</label>
                <input type="text" name="txtsgnacode" id="txtsgnacode" maxlength="2" style="width: 50px;" onkeyup="var start = this.selectionStart;
                          var end = this.selectionEnd;
                          this.value = this.value.toUpperCase();
                          this.setSelectionRange(start, end);
                          if (value.length > 1) 
                            {
                              periksaid(this.value);
                            }
                          else
                          {
                            document.getElementById('txtsgnaname').setAttribute('disabled','true');
                          }  
                          "
                  onkeydown="if (event.keyCode == 13 && value.length > 2) document.getElementById('txtsgnaname').focus()">

              </div><!-- pure-control-group -->

              <div class="pure-control-group">

                <label for="txtsgnaname">Nama Signa :</label>
                <input type="text" name="txtsgnaname" id="txtsgnaname" maxlength="50" style="width: 300px;" onkeydown="if (event.keyCode == 13 && value.length > 0)
              {
                  if (document.getElementById('txtsgnacode').value == '')
                  {
                    swal({
                        title: 'Kode Signa Kosong' ,
                        text: 'Anda belum mengisi Kode Signa, silah periksa lagi',
                        icon: 'warning',
                        });
                    document.getElementById('txtsgnacode').value = '';
                    document.getElementById('txtsgnacode').focus();
                  }
                  else if (document.getElementById('txtsgnaname').value == '')
                  {
                      swal({
                          title: 'Keterangan Signa Kosong' ,
                          text: 'Anda belum mengisi Keterangan Signa, silah periksa lagi',
                          icon: 'warning',
                          });
                      document.getElementById('txtsgnaname').value = '';
                      document.getElementById('txtsgnaname').focus();
                  }

                   else
                   {
                       var insgnacode = document.getElementById('txtsgnacode').value;
                       var insgnaname = document.getElementById('txtsgnaname').value;
                       input(insgnacode,insgnaname);
                    }

              }">

              </div><!-- pure-control-group -->
              <div class="pure-control-group">
                <label for="txtsgnausag">Cara Pemakaian :</label>
                <input type="text" name="txtsgnausag" id="txtsgnausag" maxlength="50" style="width: 300px;" readonly>
              </div>
            </fieldset>
            <fieldset>
              <a class="pure-button pure-button-primary" onclick="javascript:
                  if (document.getElementById('txtsgnacode').value == '')
                  {
                    swal({
                        title: 'Kode Signa Kosong' ,
                        text: 'Anda belum mengisi Kode Signa, silah periksa lagi',
                        icon: 'warning',
                        });
                    document.getElementById('txtsgnacode').value = '';
                    document.getElementById('txtsgnacode').focus();
                  }
                  else if (document.getElementById('txtsgnaname').value == '')
                  {
                      swal({
                          title: 'Keterangan Signa Kosong' ,
                          text: 'Anda belum mengisi Keterangan Signa, silah periksa lagi',
                          icon: 'warning',
                          });
                      document.getElementById('txtsgnaname').value = '';
                      document.getElementById('txtsgnaname').focus();
                  }

                   else
                   {
                       var insgnacode = document.getElementById('txtsgnacode').value;
                       var insgnaname = document.getElementById('txtsgnaname').value;
                       input(insgnacode,insgnaname);
                    }

        ">Submit</a>
            </fieldset>

            <fieldset>
              <label for="txtsearch">Cari...</label>
              <input type="text" class="pure-input-rounded" name="txtsearch" id="txtsearch" maxlength="20"
                style="width: 200px;" onkeyup="if (value.length > 0) { ambilscreen(this.value); } else {ambilscreen('')};"
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
          <span class="labelTime Time"><b>Date :</b> <?php $tgl = date('d-m-Y');
          echo $tgl; ?></span>
        </div>
        <div class="footertime">
          <span class="labelTime Time" id="timestamp"></span>
        </div>


      </div>
    </div>
    <script src="js/TRXAPOLI05.js"></script>
    

      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/ui.js"></script>


  </body>

  </html>
  <?php
} else {
  header("Location: " . "signin.php");
}
?>
