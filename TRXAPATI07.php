<!doctype html>
<?php include "conf/config.php";
include "inc/sanie.php";
session_start();
if (isset($_SESSION['username'])) {
  $user = $_SESSION['username'];
  ?>
  <html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Klinik Pratama">
    <title>Pemeriksaan Awal Pasien</title>
    <link rel="shortcut icon" href="assets/img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="assets/css/layouts/header.css">
    <link rel="stylesheet" href="assets/css/layouts/side-menu.css">
    <link rel="stylesheet" href="assets/css/trxapati-shared.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/jquery.js"></script>
    <script src="js/sweetalert.min.js"></script>
    <script>
      $(document).ready(function () { setInterval(timestamp, 1000); });
      function timestamp() { $.ajax({ url: 'inc/timestamp.php', success: function (data) { $('#timestamp').html(data); }, }); }
    </script>

  <body onLoad="periksaakses('PASS_REGI_ENTR');">
    <div id="wrapper">
      <!-- Side Menu -->
      <?php include "inc/side-menu.php"; ?>
      <!-- tampilan menu -->
      <div id="content-wrapper">
        <?php include "inc/header.php"; ?>
        <div class="content">

          <form name="frmtrxaregi" method="post" action="">
            <div class="content-modern">
              <div class="card-modern" id="cariDaftar">
                <div class="card-title" id="cariDaftarTitle" tabindex="-1" style="outline: none;">&#x1F50D; Daftar Pasien
                </div>
                <input type="text" name="txtsearch" id="txtsearch" class="form-control"
                  style="margin-bottom: 10px; width: 250px;" placeholder="Ketik nama untuk mencari pasien..."
                  autocomplete="off"
                  onkeyup="if (value.length > 0) { ambilscreen(this.value); } else { ambilscreen(''); }"
                  onkeydown="if (event.keyCode == 13 && value.length > 0) { document.getElementById('txtsearch').value = ''; document.getElementById('txtsearch').focus(); }">

                <div id="tblscreen"></div>
              </div>

              <div class="card-modern" id="dataKunjungan">
                <div class="card-title" id="dataKunjunganTitle" tabindex="-1">&#x1F3E5; Data Kunjungan & Pasien</div>
                <div class="form-grid">
                  <div class="form-group">
                    <label class="form-label" for="hidexamcode">Kode Pendaftaran</label>
                    <input type="hidden" name="hidexamcode" id="hidexamcode">
                    <input type="text" name="tglregidate" id="tglregidate" class="form-control" readonly
                      placeholder="Tanggal Berobat">
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="txtmainname">Nama Pasien</label>
                    <input type="text" name="txtmainname" id="txtmainname" class="form-control" maxlength="50" readonly>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="txtmastcode">Rekam Medis</label>
                    <input type="text" name="txtmastcode" id="txtmastcode" class="form-control" maxlength="10" readonly>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="txtmainbirt">Tanggal Lahir</label>
                    <input type="text" name="txtmainbirt" id="txtmainbirt" class="form-control" maxlength="50" readonly>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="txtmaingend">L/P</label>
                    <input type="text" name="txtmaingend" id="txtmaingend" class="form-control" maxlength="50" readonly>
                  </div>
                </div>
              </div>
              <div class="card-modern">
                <div class="card-title">&#x1F4C8; Pengukuran Antropometri &amp; TTV</div>
                <div class="form-grid">
                  <div class="form-group">
                    <label class="form-label" for="txtexamhght">Tinggi Badan</label>
                    <div class="input-group">
                      <input type="text" name="txtexamhght" id="txtexamhght" class="form-control" maxlength="10"
                        onkeydown="if (event.keyCode == 13 && value.length > 0) { document.getElementById('txtexamwght').focus(); }">
                      <span class="input-group-text">cm</span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="txtexamwght">Berat Badan</label>
                    <div class="input-group">
                      <input type="text" name="txtexamwght" id="txtexamwght" class="form-control" maxlength="10"
                        onkeydown="if (event.keyCode == 13 && value.length > 0) { document.getElementById('txtexamwaist').focus(); }">
                      <span class="input-group-text">kg</span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="txtexamwaist">Lingkar Perut</label>
                    <div class="input-group">
                      <input type="text" name="txtexamwaist" id="txtexamwaist" class="form-control" maxlength="10"
                        onkeydown="if (event.keyCode == 13 && value.length > 0) { document.getElementById('txtexamblod').focus(); }">
                      <span class="input-group-text">cm</span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="txtexambmi">IMT (Indeks Massa Tubuh)</label>
                    <div class="input-group">
                      <input type="text" name="txtexambmi" id="txtexambmi" class="form-control" maxlength="10" readonly>
                      <span class="input-group-text">kg/m2</span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="txtexamblod">Tekanan Darah</label>
                    <div class="input-group">
                      <input type="text" name="txtexamblod" id="txtexamblod" class="form-control" maxlength="10"
                        onkeydown="if (event.keyCode == 13 && value.length > 0) { document.getElementById('txtexamtemp').focus(); }">
                      <span class="input-group-text">mmHg</span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="txtexamtemp">Suhu Tubuh</label>
                    <div class="input-group">
                      <input type="text" name="txtexamtemp" id="txtexamtemp" class="form-control" maxlength="10"
                        onkeydown="if (event.keyCode == 13 && value.length > 0) { document.getElementById('txtexamrr').focus(); }">
                      <span class="input-group-text">C</span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="txtexamrr">Respiratory Rate</label>
                    <div class="input-group">
                      <input type="text" name="txtexamrr" id="txtexamrr" class="form-control" maxlength="10"
                        onkeydown="if (event.keyCode == 13 && value.length > 0) { document.getElementById('txtexamhr').focus(); }">
                      <span class="input-group-text">/ min</span>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="txtexamhr">Heart Rate</label>
                    <div class="input-group">
                      <input type="text" name="txtexamhr" id="txtexamhr" class="form-control" maxlength="10"
                        onkeydown="if (event.keyCode == 13 && value.length > 0) { document.getElementById('txtexamcomp').focus(); }">
                      <span class="input-group-text">bpm</span>
                    </div>
                  </div>
                  <div class="form-group full">
                    <label class="form-label" for="txtexamcomp">Keluhan / Riwayat Penyakit</label>
                    <input type="text" name="txtexamcomp" id="txtexamcomp" class="form-control" maxlength="50"
                      onkeydown="if (event.keyCode == 13 && value.length > 0) { document.getElementById('btnSubmit').focus(); }">
                  </div>
                </div>
              </div>
              <div class="card-modern" id="submitCard" style="text-align:right;">
                <!-- <div class="card-title">Submit</div> -->
                <button type="button" id="btnSubmit" class="btn-modern btn-save" onclick="javascript:
                if (document.getElementById('hidexamcode').value.length == 0) {
                  swal({ title: 'Data Pendaftaran masih Kosong', text: 'Anda belum memilih Data pendaftaran, silah periksa lagi', icon: 'warning', });
                }
                else if (document.getElementById('txtexamhght').value.length == 0) {
                  swal({ title: 'Tinggi Badan kosong', text: 'Anda belum mengisi Tinggi Badan Pasien, silah periksa lagi', icon: 'warning', });
                }
                else if (document.getElementById('txtexamwght').value.length == 0) {
                  swal({ title: 'Berat Badan Kosong', text: 'Anda belum mengisi Berat Badan Pasien', icon: 'warning', });
                }
                else if (document.getElementById('txtexamblod').value.length == 0) {
                  swal({ title: 'Tekanan Darah kosong', text: 'Anda belum mengisi Tekanan Darah Pasien, silah periksa lagi', icon: 'warning', });
                }
                else if (document.getElementById('txtexamtemp').value.length == 0) {
                  swal({ title: 'Suhu Tubuh kosong', text: 'Anda belum mengisi Suhu Tubuh Pasien, silah periksa lagi', icon: 'warning', });
                }
                else {
                  var inexamcode = document.getElementById('hidexamcode').value;
                  var inexamhght = document.getElementById('txtexamhght').value;
                  var inexamwght = document.getElementById('txtexamwght').value;
                  var inexamwaist = document.getElementById('txtexamwaist').value;
                  var inexambmi = document.getElementById('txtexambmi').value;
                  var inexamblod = document.getElementById('txtexamblod').value;
                  var inexamtemp = document.getElementById('txtexamtemp').value;
                  var inexamrr = document.getElementById('txtexamrr').value;
                  var inexamhr = document.getElementById('txtexamhr').value;
                  var inexamcomp = document.getElementById('txtexamcomp').value;
                  input(inexamcode,inexamhght,inexamwght,inexamwaist,inexambmi,inexamblod,inexamtemp,inexamrr,inexamhr,inexamcomp);
                  setTimeout(function(){
                    document.getElementById('cariDaftar').scrollIntoView({ behavior:'smooth', block:'start' });
                    document.getElementById('txtsearch').focus();
                  }, 300);
                }">
                  &#x2714; Submit
                </button>
              </div>
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
    <script src="js/TRXAPATI07.js"></script>
    <script src="js/ui.js"></script>
    <script>
      function hitungIMT() {
        var tb = parseFloat(document.getElementById('txtexamhght').value);
        var bb = parseFloat(document.getElementById('txtexamwght').value);
        if (tb > 0 && bb > 0) {
          var imt = bb / Math.pow((tb / 100), 2);
          document.getElementById('txtexambmi').value = imt.toFixed(2);
        } else {
          document.getElementById('txtexambmi').value = '';
        }
      }
      document.getElementById('txtexamhght').addEventListener('keyup', hitungIMT);
      document.getElementById('txtexamwght').addEventListener('keyup', hitungIMT);
    </script>

  </body>

  </html>
  <?php
} else {
  header("Location: " . "signin.php");
}
?>