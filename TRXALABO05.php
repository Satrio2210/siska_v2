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
    <title>Input Hasil Pemeriksaan</title>
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
          <div class="content-modern">
            <form name="frmtrxalabo" class="pure-form pure-form-aligned" method="post" action="">
              <div class="form-grid-2">

                <div class="card-modern"> <!-- kiri -->
                  <div class="card-title">Hasil Pemeriksaan Laboratorium</div>

                  <div class="form-grid">
                    <div class="form-group">
                      <label class="form-label" for="txtlaboregi">Nomor Daftar :</label>
                      <input class="form-control" type="text" name="txtlaboregi" id="txtlaboregi" maxlength="14"
                        readonly="true">
                      <input type="hidden" name="hidlabodoct" id="hidlabodoct" value="<?php echo $user; ?>">
                    </div>

                    <div class="form-group">
                      <label class="form-label" for="txtpaticode">Rekam Medis :</label>
                      <input class="form-control" type="text" name="txtpaticode" id="txtpaticode" maxlength="10"
                        readonly="true">
                    </div>

                    <div class="form-group">
                      <label class="form-label" for="txtmainname">Nama :</label>
                      <input class="form-control" type="text" name="txtmainname" id="txtmainname" maxlength="50"
                        readonly="true">
                    </div>

                    <div class="form-group">
                      <label class="form-label" for="txtmaingend">L/P :</label>
                      <input class="form-control" type="text" name="txtmaingend" id="txtmaingend" maxlength="10"
                        readonly="true">

                      <input type="hidden" name="hidmaingend" id="hidmaingend">
                      <input type="hidden" name="hidmaintitl" id="hidmaintitl">
                    </div>

                    <div class="form-group">
                      <label class="form-label" for="txtmainage">Usia :</label>
                      <input class="form-control" type="text" name="txtmainage" id="txtmainage" maxlength="50"
                        readonly="true">
                    </div>

                    <div class="form-group">
                      <label class="form-label" for="txtbirtdate">Tgl Lahir :</label>
                      <input class="form-control" type="text" name="txtbirtdate" id="txtbirtdate" maxlength="14"
                        readonly="true">
                    </div>

                    <div class="form-group">
                      <label class="form-label" for="txtmainaddr">Alamat :</label>
                      <input class="form-control" type="text" name="txtmainaddr" id="txtmainaddr" maxlength="50"
                        readonly="true">
                    </div>

                    <div class="form-group">
                      <label class="form-label" for="txtregipaym">Pembayaran :</label>
                      <input class="form-control" type="text" name="txtregipaym" id="txtregipaym" maxlength="50"
                        readonly="true">
                    </div>
                  </div>
                </div>

                <div class="card-modern"> <!-- Kanan -->
                  <div class="card-title">Hasil Pemeriksaan</div>
                  <div class="form-grid">

                    <div class="form-group">
                      <label class="form-label" for="txtmastcode">Pemeriksaan :</label>
                      <input class="form-control" type="text" name="txtmastcode" id="txtmastcode" maxlength="50"
                        autocomplete="off" onkeyup="if (value.length > 0) 
                        {
                          let gender = document.getElementById('hidmaintitl').value;
                          let tindakan = document.getElementById('txtlaboregi').value;

                          ambilrujukan(this.value,gender,tindakan);
                        } 
                        else 
                        { 
                          document.getElementById('tbllabomast').style.visibility = 'hidden';
                        }">
                      <input type="hidden" name="hidmastcode" id="hidmastcode">
                    </div>

                    <div class="form-grid-4">
                      <div class="form-group">
                        <label class="form-label" for="txtlaborslt">Hasil :</label>
                        <input class="form-control" type="text" name="txtlaborslt" id="txtlaborslt" maxlength="10"
                          onkeydown="if (event.keyCode == 13 && value.length > 0)
                        {
                              document.getElementById('txtlabonote').focus(); 
                        }">
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="txtlabovalu">Rujukan :</label>
                        <input class="form-control" type="text" name="txtlabovalu" id="txtlabovalu" maxlength="10"
                          readonly="true">
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="txtlabounit">Satuan :</label>
                        <input class="form-control" type="text" name="txtlabounit" id="txtlabounit" maxlength="10"
                          readonly="true">
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="txtlabonote">Remark :</label>
                    <textarea class="form-control" id="txtlabonote" name="txtlabonote" rows="4" cols="30"></textarea>
                  </div>

                  <div class="form-grid-4">
                    <a class="btn-modern btn-save" onclick="javascript:
                        if (document.getElementById('txtlaboregi').value == '')
                        {
                          swal({
                              title: 'Nomor Pemeriksaan Kosong' ,
                              text: 'Anda belum memilih Pasien, silah periksa lagi',
                              icon: 'warning',
                              });
                        }

                        else if (document.getElementById('hidlabodoct').value == '')
                        {
                            swal({
                                title: 'Nama Petugas medis kosong' ,
                                text: 'Nama Petugas Medis Kosong, silah periksa lagi',
                                icon: 'warning',
                                });
                        }


                        else if (document.getElementById('hidmastcode').value == '')
                        {
                            swal({
                                title: 'Jenis Pemeriksaan Kosong' ,
                                text: 'Anda belum memilih Jenis Pemeriksaan, silah periksa lagi',
                                icon: 'warning',
                                });
                        }

                        else if (document.getElementById('txtlaborslt').value == '')
                        {
                            swal({
                                title: 'Nilai Pemeriksaan Kosong' ,
                                text: 'Anda belum mengisi Nilai Hasil Pemeriksaan, silah periksa lagi',
                                icon: 'warning',
                                });
                        }

                        else if (document.getElementById('txtlabonote').value == '')
                        {
                            swal({
                                title: 'Catatan Hasil Pemeriksaan Kosong' ,
                                text: 'Anda belum mengisi Penilaian Pemeriksaan, silah periksa lagi',
                                icon: 'warning',
                                });
                        }


                        else
                        {
                            var inlaboregi = document.getElementById('txtlaboregi').value;

                            var inlabodoct = document.getElementById('hidlabodoct').value;

                            var inmastcode = document.getElementById('hidmastcode').value;

                            var inlaborslt = document.getElementById('txtlaborslt').value;

                            var inlabonote = document.getElementById('txtlabonote').value;

                            input(inlaboregi,inlabodoct,inmastcode,inlaborslt,inlabonote);
                          }
                        ">Input Hasil
                    </a>

                    <a class="btn-modern btn-refresh" onclick="javascript:
                        if (document.getElementById('txtlaboregi').value == '')
                        {
                          swal({
                              title: 'Data Hasil belum di pilih' ,
                              text: 'Anda belum memilih Data Hasil Pemeriksaan Laboratorium, silah periksa lagi',
                              icon: 'warning',
                              });                      
                        }
                        else
                        {
                          var outlaboregi = document.getElementById('txtlaboregi').value;
                          location.href ='TRXALABO05P.php?laboregi='+outlaboregi;                      
                        } 
                        ">Print Hasil
                    </a>

                    <a class="btn-modern btn-delete" onclick="javascript: 
                        document.getElementById('tblviewresult').style.visibility = 'hidden';
                        document.getElementById('tblviewresult').innerHTML = '';

                        document.getElementById('txtlaboregi').value = '';
                        document.getElementById('txtpaticode').value = '';
                        document.getElementById('txtmastcode').value = '';
                        document.getElementById('txtmainname').value = '';
                        document.getElementById('txtmaingend').value = '';
                        document.getElementById('hidmaingend').value = '';

                        document.getElementById('txtmainage').value = '';
                        document.getElementById('txtbirtdate').value = '';

                        document.getElementById('txtmainaddr').value = '';
                        document.getElementById('txtregipaym').value = '';

                        document.getElementById('txtmastcode').setAttribute('disabled','true');
                        document.getElementById('txtlaborslt').setAttribute('disabled','true');

                        document.getElementById('txtlabovalu').setAttribute('disabled','true');
                        document.getElementById('txtlabonote').setAttribute('disabled','true');

                        ambilscreen('',document.getElementById('hidlabodoct').value);
                        ">Close
                    </a>
                  </div>

                  <!--
                  <fieldset>
                    <div id="tblviewresult">
                  </fieldset>

                  <fieldset>
                    <label for="txtsearch">Cari...</label>
                    <input type="text" class="pure-input-rounded" name="txtsearch" id="txtsearch" maxlength="20"
                      style="width: 200px;"
                      onkeyup="if (value.length > 0) { ambilscreen(this.value,document.getElementById('hidlabodoct').value); } else {ambilscreen('',document.getElementById('hidlabodoct').value)};"
                      onkeydown="if (event.keyCode == 13 && value.length > 0) 
                        { 
                        document.getElementById('txtsearch').value = '';
                        document.getElementById('txtsearch').focus()
                        }">
                  </fieldset>

                  <fieldset>
                    <div id="tblscreen">
                  </fieldset>

                  <fieldset>
                    <div id="tbllabomast" style="position: absolute; 
                  top: 400px;
                  left: calc(70% - 150px);
                background-color: white; 
                visibility: hidden; 
                z-index: 100">
                  </fieldset>

                  <fieldset>
                    <div id="tblsatuan" style="position: absolute; 
                  top: 300px;
                  left: calc(70% - 150px);
                background-color: white; 
                visibility: hidden; 
                z-index: 100">
                </fieldset> -->
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    </div>
    </div>

    <div class="footerdate">
      <span class="labelTime Time"><b>Date :</b>
        <?php $tgl = date('d-m-Y');
        echo $tgl; ?>
      </span>
    </div>
    <div class="footertime">
      <span class="labelTime Time" id="timestamp"></span>
    </div>
    <script src="js/TRXALABO05.js"></script>
    <script src="js/ui.js"></script>
  </body>

  </html>
  <?php
} else {
  header("Location: " . "signin.php");
}
?>
