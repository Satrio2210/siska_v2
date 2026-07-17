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
    <title>Data Pasien</title>
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

  <body onLoad="periksaakses('PASS_LABO_ENTR'); 
">
    <div id="wrapper">

      <?php include "inc/side-menu.php"; ?>

      <div id="content-wrapper">
        <?php include "inc/header.php"; ?>
        <div class="content">

          <div class="content-modern">

            <form name="frmtrxalabo" class="pure-form pure-form-aligned" method="post" action="">
              <div class="form-grid-2">

                <div class="card-modern">
                  <div class="card-title">Data Pasien</div>

                  <div class="form-grid">
                    <div class="form-group">
                      <label class="form-label" for="txttretcode">No. Daftar :</label>
                      <input type="text" class="form-control" name="txttretcode" id="txttretcode" maxlength="14"
                        readonly="true">
                      <input type="hidden" name="hidtretdoct" id="hidtretdoct" value="<?php echo $user; ?>">
                    </div>

                    <div class="form-group">
                      <label class="form-label" for="txtpaticode">Rekam Medis :</label>
                      <input type="text" class="form-control" name="txtpaticode" id="txtpaticode" maxlength="10"
                        readonly="true">
                    </div>

                    <div class="form-group">
                      <label class="form-label" for="txtmainname">Nama :</label>
                      <input type="text" class="form-control" name="txtmainname" id="txtmainname" maxlength="14"
                        readonly="true">
                    </div>

                    <div class="form-group">
                      <label class="form-label" for="txtmaingend">L/P :</label>
                      <input type="text" class="form-control" name="txtmaingend" id="txtmaingend" maxlength="10"
                        readonly="true">
                    </div>

                    <div class="form-group">
                      <label class="form-label" for="txtbirtdate">Tgl Lahir :</label>
                      <input type="text" class="form-control" name="txtbirtdate" id="txtbirtdate" maxlength="50"
                        readonly="true">
                    </div>

                    <div class="form-group">
                      <label class="form-label" for="txtmainage">Usia :</label>
                      <input type="text" class="form-control" name="txtmainage" id="txtmainage" maxlength="50"
                        readonly="true">
                    </div>

                    <div class="form-group full">
                      <label class="form-label" for="txtregipaym">Pembayaran :</label>
                      <input type="text" class="form-control" name="txtregipaym" id="txtregipaym" maxlength="50"
                        readonly="true">
                    </div>
                  </div>
                </div>

                <div class="card-modern">
                  <div class="card-title">Pemeriksaan</div>

                  <div class="form-group" style="position: relative;">
                    <label class="form-label" for="txtmedicode">Layanan/Tindakan :</label>
                    <input type="text" class="form-control" name="txtmedicode" id="txtmedicode" maxlength="100"
                      autocomplete="off" disabled onkeyup="if (value.length >= 0) 
                    {
                    var regipoli = document.getElementById('hidmediroom').value;
                    var regipaym = document.getElementById('hidregipaym').value;  
                    ambiltindakan(this.value,regipoli,regipaym);
                    } 
                    else 
                    { 
                      document.getElementById('tbltindakan').innerHTML = '';
                      document.getElementById('tbltindakan').style.visibility = 'hidden';
                    }">

                    <div id="tbltindakan"
                      style="position: absolute; top: 100%; left: 0; width: 100%; background-color: white; visibility: hidden; z-index: 1000; margin-top: 10px; border-radius: 12px; overflow-y: auto; max-height: 200px; box-shadow: 0 4px 6px rgba(0,0,0,0.15); border: 1px solid #d1d5db;">
                    </div>
                  </div>

                  <div class="form-group" style="margin-top: 15px;">
                    <label class="form-label" for="txttretquty">Qty :</label>
                    <input type="number" name="txttretquty" id="txttretquty" min="1" value="1" disabled
                      class="form-control" style="width: 80px;"
                      onkeydown="if(event.keyCode==13){ event.preventDefault(); }"
                      onclick="if (isNaN(this.value)) { this.value = '0'; this.focus(); }">
                  </div>

                  <input type="hidden" name="hidmedicode" id="hidmedicode">
                  <input type="hidden" name="hidmedirate" id="hidmedirate">
                  <input type="hidden" name="hidmediroom" id="hidmediroom">
                  <input type="hidden" name="hidregipaym" id="hidregipaym">

                  <button type="button" id="btn-input-tindakan" class="btn-modern btn-save"
                    style="margin-top: 15px; align-self: flex-start;" onclick="javascript:
                      if (document.getElementById('txtmedicode').value == '')
                      {
                        swal({
                            title: 'Tindakan Kosong' ,
                            text: 'Anda belum mengisi Tindakan, silah periksa lagi',
                            icon: 'warning',
                            });
                      }
                      else if (document.getElementById('txttretquty').value == '0')
                      {
                          swal({
                              title: 'Qty Kosong' ,
                              text: 'Anda belum mengisi Diagnosa, silah periksa lagi',
                              icon: 'warning',
                              });
                      }
                      else if (document.getElementById('txttretquty').value == '')
                      {
                          swal({
                              title: 'Qty Kosong' ,
                              text: 'Anda belum mengisi Diagnosa, silah periksa lagi',
                              icon: 'warning',
                              });
                      }
                      else if (isNaN(document.getElementById('txttretquty').value))
                      {
                          swal({
                              title: 'Masukkan Angka' ,
                              text: 'Anda memasukkan Huruf, bukan angka, silah periksa lagi',
                              icon: 'warning',
                              });                    
                      }

                      else
                      {
                       var intretcode = document.getElementById('txttretcode').value;
                       var intretdoct = document.getElementById('hidtretdoct').value;

                       var inmedicode = document.getElementById('hidmedicode').value;
                       var inmedirate = document.getElementById('hidmedirate').value;

                       var intretquty = document.getElementById('txttretquty').value;

                       var inmediroom = document.getElementById('hidmediroom').value;

                       input(intretcode,intretdoct,inmedicode,inmedirate,intretquty,inmediroom);
                    }
                    ">Input</button>

                  <div id="tblscreen" style="margin-top: 10px; width: 100%;"></div>

                </div>
              </div>

              <div class="card-modern">
                <button type="button" class="btn-modern btn-save" onclick="
                  swal({
                    title: 'Berhasil',
                    text: 'Data Pemeriksaan Berhasil di simpan.',
                    icon: 'success',
                    timer: 1500,
                    buttons: false
                  });
                  setTimeout(function () {
                    location.href = 'TRXALABO00.php';
                  }, 1600);
                ">Simpan
                  Pemeriksaan</button>
              </div>

            </form>

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

    <script src="js/TRXALABO01.js"></script>
    <script src="js/ui.js"></script>

    <script>
      // Auto-isi form dari daftar pasien (TRXALABO00) via URL params
      (function () {
        var params = new URLSearchParams(window.location.search);
        var regicode = params.get('regicode');
        var paticode = params.get('paticode');
        if (regicode && paticode && typeof ambildetailregi === 'function') {
          setTimeout(function () {
            ambildetailregi(regicode, paticode);
          }, 400);
        }
      })();
    </script>

  </body>

  </html>
  <?php
} else {
  header("Location: " . "signin.php");
}
?>