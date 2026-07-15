<!doctype html>
<?php

include "conf/config.php";
include "inc/sanie.php";
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
    <title>Pasien berobat</title>
    <link rel="shortcut icon" href="assets/img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="assets/css/layouts/header.css">
    <link rel="stylesheet" href="assets/css/layouts/side-menu.css">
    <link rel="stylesheet" href="assets/css/trxapati-shared.css">
    <link rel="stylesheet" href="assets/css/trxapati-02.css">
  </head>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="js/jquery.js"></script>
  <script src="js/sweetalert.min.js"></script>

  <script>
    $(document).ready(function () {
      setInterval(timestamp, 1000);
    });
    function timestamp() { $.ajax({ url: 'inc/timestamp.php', success: function (data) { $('#timestamp').html(data); }, }); }
  </script>

  <body onLoad="periksaakses('PASS_REGI_ENTR'); ambilscreen('');">
    <div id="wrapper">
      <!-- Side Menu -->
      <?php include "inc/side-menu.php"; ?>

      <!-- tampilan menu -->
      <div id="content-wrapper">
        <?php include "inc/header.php"; ?>
        <div class="content">

          <!-- Tab Menu -->
          <!-- <div class="pure-menu pure-menu-horizontal">
            <ul class="pure-menu-list">
              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TRXAPATI01.php'">
                <a class="pure-menu-link">
                  Daftar Pasien
                </a>
              </li>
              <li class="pure-menu-item pure-menu-disabled" onclick="document">
                Pasien Berobat
              </li>
              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TRXAPATI07.php'">
                <a class="pure-menu-link">
                  TTV & Antropometri
                </a>
              </li> -->
          <!-- 
              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TRXAPATI03.php'">
                <a class="pure-menu-link">
                Ruangan
                </a>
              </li>
              <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TRXAPATI04.php'">
                <a class="pure-menu-link">
                Jadwal Dokter
                </a>
              </li> -->
          <!-- </ul>
          </div> -->
          <!-- Tab Menu -->

          <!-- Form Input -->
          <div class="content-modern">
            <!-- <div class="main-grid"> -->
            <div class="form-grid-2 pati">
              <!-- LEFT -->
              <div style="display: grid;">

                <div class="card-modern" id="dataPasien">

                  <div class="card-title">
                    &#x1F9D1; Data Pasien
                  </div>

                  <div class="form-grid">

                    <div class="form-group full" style="position:relative;">

                      <label class="form-label">
                        Cari Pasien
                      </label>

                      <input type="text" name="txtsearch" id="txtsearch" class="form-control"
                        placeholder="Cari Nama Pasien / Tanggal Lahir" autocomplete="off" onkeyup="
                        if (value.length > 1)
                        {
                          ambilpaticode(this.value);
                        }
                        else
                        {
                          document.getElementById('tblpati').style.display='none';
                        }
                        ">
                      <div id="tblpati" class="search-dropdown" style="display:none;"></div>

                    </div>

                    <div class="form-group">

                      <label class="form-label">
                        Tanggal Berobat
                      </label>

                      <input type="date" name="tglregidate" id="tglregidate" class="form-control"
                        value="<?php echo $datenow; ?>">

                    </div>

                    <div class="form-group">

                      <label class="form-label">
                        No Rekam Medis
                      </label>

                      <input type="text" name="txtmastcode" id="txtmastcode" class="form-control" readonly>

                      <input type="hidden" name="hidpaticode" id="hidpaticode">

                    </div>

                    <div class="form-group full">

                      <label class="form-label">
                        Nama Pasien
                      </label>

                      <input type="text" name="txtmainname" id="txtmainname" class="form-control" readonly>

                    </div>

                    <div class="form-group">

                      <label class="form-label">
                        Tanggal Lahir
                      </label>

                      <input type="text" name="txtmainbirt" id="txtmainbirt" class="form-control" readonly>

                    </div>

                    <div class="form-group">

                      <label class="form-label">
                        Jenis Kelamin
                      </label>

                      <input type="text" name="txtmaingend" id="txtmaingend" class="form-control" readonly>

                    </div>

                    <div class="form-group">

                      <label class="form-label">
                        Golongan Darah
                      </label>

                      <input type="text" name="txtmainblod" id="txtmainblod" class="form-control" readonly>

                    </div>

                    <div class="form-group">

                      <label class="form-label">
                        Kontak
                      </label>

                      <input type="text" name="txtmainphne" id="txtmainphne" class="form-control" readonly>

                    </div>

                    <div class="form-group full">

                      <label class="form-label">
                        Alamat
                      </label>

                      <textarea name="txtmainaddr" id="txtmainaddr" class="form-control" readonly></textarea>

                    </div>

                  </div>

                </div>

              </div>


              <!-- RIGHT -->
              <div class="right-grid">

                <!-- DETAIL -->
                <div class="card-modern" id="regidoct">

                  <div class="card-title">
                    &#x1F3E5; Detail Kunjungan
                  </div>

                  <div class="form-grid">

                    <div class="form-group full" style="position:relative;">

                      <label class="form-label">
                        Dokter / Bidan
                      </label>

                      <input type="text" name="txtregidoct" id="txtregidoct" class="form-control"
                        placeholder="Cari Dokter" autocomplete="off" onkeyup="
                          if (value.length > 0)
                          {
                            ambildoctuser(this.value);
                          }
                          else
                          {
                            document.getElementById('tbluser').style.visibility='hidden';
                          }
                          ">
                      <input type="hidden" name="hidregidoct" id="hidregidoct">

                      <div id="tbluser" class="search-dropdown" style="visibility:hidden;"></div>

                    </div>

                    <div class="form-group">

                      <label class="form-label">
                        Poli
                      </label>

                      <input type="text" name="txtregipoli" id="txtregipoli" class="form-control" readonly>

                      <input type="hidden" name="hidregipoli" id="hidregipoli">

                    </div>

                    <div class="form-group">

                      <label class="form-label">
                        Pembayaran
                      </label>

                      <select name="optregipaym" id="optregipaym" class="form-control" onchange="
                      if(this.value == 'B'){
                      document.getElementById('hidregifee').value='N';
                      document.getElementById('txtregifeeview').value='Tidak';}
                      else{document.getElementById('hidregifee').value='Y';
                      document.getElementById('txtregifeeview').value='Ya';}
                      ">

                        <option value="">- PILIH -</option>
                        <option value="U">Umum</option>
                        <option value="B">BPJS</option>
                        <option value="A">Asuransi</option>
                        <option value="P">Perusahaan</option>
                        <option value="H">Halodoc</option>

                      </select>

                    </div>

                    <div class="form-group full">

                      <label class="form-label">
                        Biaya Admin
                      </label>

                      <select name="hidregifee" id="hidregifee" class="form-control">

                        <option value="">- PILIH -</option>
                        <option value="Y">Ya</option>
                        <option value="N">Tidak</option>

                      </select>
                      <!-- <input type="text" id="txtregifeeview" class="form-control" autocomplete="off"> -->

                      <input type="hidden" id="hidregifee">

                    </div>

                  </div>

                  <div class="action-group pati">
                    <button type="button" class="btn-modern btn-save" onclick="simpanPendaftaran()">
                      Simpan
                    </button>

                    <button type="button" onclick="location.reload();" class="btn-modern btn-refresh">
                      Refresh
                    </button>
                  </div>

                </div>

                <!-- ANTRIAN -->
                <div class="card-modern">

                  <div class="card-title">
                    &#x1F3EB; Antrian
                  </div>

                  <div class="queue-box">

                    <div style="font-size:14px;color:#64748b;">
                      Nomor Antrian Anda
                    </div>

                    <div class="queue-number" id="queueNumber">
                      -
                    </div>

                    <div class="queue-estimation">
                      Estimasi Dilayani Â± 15 Menit
                    </div>
                    <div class="action-group pati">
                      <button id="btnPrintAntrian" class="btn-modern btn-save" style="width:100%;margin-top:10px;"
                        onclick="printAntrian();">
                        Cetak
                      </button>

                      <button id="btnSkipAntrian" class="btn-modern btn-reset" style="width:100%;margin-top:10px;"
                        onclick="resetFormAfterPrint();">
                        Skip
                      </button>
                    </div>

                  </div>

                </div>

              </div>

            </div>


            <!-- TABLE -->
            <div class="card-modern">

              <div class="card-title">
                &#x1F4CB; Daftar Pasien
              </div>

              <input type="text" class="form-control" placeholder="Cari Pasien..." autocomplete="off"
                style="max-width:250px;margin-bottom:20px;" onkeyup="
                if (value.length < 16)
                {
                  ambilscreen(this.value);
                }
                else
                {
                  ambilscreen('');
                }
                ">

              <div class="table-wrapper">
                <div id="tblscreen">
                </div>
              </div>
            </div>
          </div>

          <div id="tbluser" style="position: absolute; 
                 top: 200px;
                 left: calc(70% - 200px);
                 background-color: white; 
                 visibility: hidden; 
                 z-index: 100"></div>

          <div id="tblpati" style="position: absolute; 
                 top: 300px;
                 left: calc(70% - 250px);
                 background-color: white; 
                 visibility: hidden; 
                 z-index: 100"></div>

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

    <script src="js/TRXAPATI02.js?v=<?php echo time(); ?>"></script>
    <script src="js/ui.js"></script>

  </body>

  </html>
  <?php
} else {
  header("Location: " . "signin.php");
}
?>