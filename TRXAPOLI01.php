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
    <title>Pemeriksaan Pasien</title>
    <link rel="shortcut icon" href="assets/img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="assets/css/layouts/header.css">
    <link rel="stylesheet" href="assets/css/layouts/side-menu.css">
    <link rel="stylesheet" href="assets/css/trxapati-shared.css">

    <style>
      /* === FORM GRID VARIANTS === */
      .vital-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
      }

      /* === HISTORY ROWS (Riwayat Pasien) === */
      .history-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px 24px;
        margin-top: 10px;
      }

      .history-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 16px;
        min-height: 56px;
      }

      .history-label {
        font-size: 13px;
        font-weight: 600;
        color: #111827;
      }

      .history-option {
        display: flex;
        align-items: center;
        gap: 18px;
      }

      .history-option label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        cursor: pointer;
        color: #374151;
        margin: 0;
      }

      .history-option input[type="checkbox"] {
        width: 16px;
        height: 16px;
      }

      /* === DIAGNOSA === */
      .diagnosa-section {
        margin: 10px 0px;
      }

      .diagnosa-wrapper {
        position: relative;
        width: 100%;
        margin-top: 10px;
      }

      /* === SPLIT LAYOUT === */
      .split-layout {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 10px;
        align-items: stretch;
      }

      /* === SUBMIT SECTION === */
      .submit-card {
        margin-top: 24px !important;
      }

      .submit-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
      }

      .submit-right {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
      }

      .btn-secondary {
        background: #eef2ff;
        color: #3730a3;
      }

      .btn-secondary:hover {
        background: #e0e7ff;
      }

      .btn-primary {
        background: #10b981;
        color: white;
      }

      .btn-primary:hover {
        background: #059669;
      }

      /* === RESPONSIVE === */
      @media(max-width:992px) {
        .split-layout {
          grid-template-columns: 1fr;
        }

        .form-grid-2,
        .vital-grid {
          grid-template-columns: 1fr;
        }
      }

      @media(max-width:768px) {

        .exam-grid,
        .note-grid {
          grid-template-columns: 1fr;
        }

        .history-grid {
          grid-template-columns: 1fr;
        }

        .history-row {
          flex-direction: column;
          align-items: flex-start;
          gap: 10px;
        }

        .submit-wrapper {
          flex-direction: column;
          align-items: stretch;
        }

        .submit-right {
          width: 100%;
          justify-content: flex-start;
        }
      }
    </style>

  </head>
  <script type="text/javascript" src="js/jquery.js"></script>
  <script src="js/sanie.js"></script>
  <script src="js/sweetalert.min.js"></script>

  <script>
    $(document).ready(function () {
      setInterval(timestamp, 1000);
    });
    function timestamp() { $.ajax({ url: 'inc/timestamp.php', success: function (data) { $('#timestamp').html(data); }, }); }
  </script>

  <body onLoad="periksaakses('PASS_TRXA_POLI'); 
">
    <div id="wrapper">
      <!-- Side Menu -->
      <?php include "inc/side-menu.php"; ?>

      <!-- tampilan menu -->
      <div id="content-wrapper">
        <?php include "inc/header.php"; ?>
        <div class="content">

          <!-- REKAM MEDIS (moved to top) -->
          <div class="content-modern">
            <div class="card-modern">
              <div class="card-title">
                Riwayat Medis
              </div>
              <div id="tblrekammedis"></div>
            </div>

            <!-- Form Input -->

            <form name="frmtrxapoli" method="post" action="">

              <div class="card-modern" id="infoPasien">
                <div class="card-title">
                  Informasi Pasien
                </div>

                <div class="form-grid-3">
                  <div class="form-group">
                    <label class="form-label" for="txtregidate">Tgl. Berkunjung :</label>
                    <input type="text" name="txtregidate" id="txtregidate" maxlength="14" class="form-control"
                      readonly="true">
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="txtexamcode">No. Daftar :</label>
                    <input type="text" name="txtexamcode" id="txtexamcode" maxlength="14" class="form-control"
                      readonly="true">
                  </div>

                  <div class="form-group">
                    <input type="hidden" name="hidexamdoct" id="hidexamdoct" value="<?php echo $user; ?>">

                    <label class="form-label" for="txtpaticode">Rekam Medis :</label>
                    <input type="text" name="txtpaticode" id="txtpaticode" maxlength="10" class="form-control"
                      readonly="true">
                  </div>
                </div>

                <div class="form-grid">
                  <div class="form-group">
                    <label class="form-label" for="txtmainname">Nama Lengkap :</label>
                    <input type="text" name="txtmainname" id="txtmainname" maxlength="50" class="form-control"
                      readonly="true">
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="txtmaingend">Jenis Kelamin :</label>
                    <input type="text" name="txtmaingend" id="txtmaingend" maxlength="10" class="form-control"
                      readonly="true">
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="txtmainage">Usia :</label>
                    <input type="text" name="txtmainage" id="txtmainage" maxlength="50" class="form-control"
                      readonly="true">
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="txtbirtdate">Tgl Lahir :</label>
                    <input type="text" name="txtbirtdate" id="txtbirtdate" maxlength="14" class="form-control"
                      readonly="true">
                  </div>
                </div>

                <div class="form-grid">
                  <div class="form-group">
                    <label class="form-label" for="txtmainaddr">Alamat :</label>
                    <input type="text" name="txtmainaddr" id="txtmainaddr" maxlength="50" class="form-control"
                      readonly="true">
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="txtregipaym">Pembayaran :</label>
                    <input type="text" name="txtregipaym" id="txtregipaym" maxlength="50" class="form-control"
                      readonly="true">
                  </div>
                </div>
              </div>

              <div class="form-grid-2">

                <!-- KIRI -->
                <div class="card-modern">

                  <div class="card-title">
                    Keluhan
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="txtexamcomp">
                      Keluhan Utama
                    </label>

                    <textarea id="txtexamcomp" name="txtexamcomp" rows="3" class="form-control" readonly="true"
                      style="resize: vertical;">
                                                                                                                                                                                                                                                                                                                    </textarea>
                  </div>
                </div>

                <!-- KANAN -->
                <div class="card-modern">

                  <div class="card-title">
                    Vital Sign
                  </div>

                  <div class="vital-grid">

                    <div class="form-group">
                      <label class="form-label" for="txtexamhght">Tinggi</label>
                      <div class="input-group">
                        <input type="text" name="txtexamhght" id="txtexamhght" maxlength="10" class="form-control">
                        <span class="input-group-text">cm</span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="form-label" for="txtexamwght">
                        Berat
                      </label>
                      <div class="input-group">
                        <input type="text" name="txtexamwght" id="txtexamwght" maxlength="10" class="form-control">
                        <span class="input-group-text">kg</span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="form-label" for="txtexamwaist">
                        Lingkar Perut
                      </label>
                      <div class="input-group">
                        <input type="text" name="txtexamwaist" id="txtexamwaist" maxlength="10" class="form-control">
                        <span class="input-group-text">cm</span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="form-label" for="txtexambmi">
                        Index Masa Tubuh
                      </label>
                      <div class="input-group">
                        <input type="text" name="txtexambmi" id="txtexambmi" maxlength="10" class="form-control">
                        <span class="input-group-text">kg/m2</span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="form-label" for="txtexamblod">
                        Tekanan Darah
                      </label>
                      <div class="input-group">
                        <input type="text" name="txtexamblod" id="txtexamblod" maxlength="10" class="form-control">
                        <span class="input-group-text">mmHg</span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="form-label" for="txtexamtemp">
                        Suhu
                      </label>
                      <div class="input-group">
                        <input type="text" name="txtexamtemp" id="txtexamtemp" maxlength="10" class="form-control">
                        <span class="input-group-text">&#8451;</span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="form-label" for="txtexamrr">
                        Respiratory Rate
                      </label>
                      <div class="input-group">
                        <input type="text" name="txtexamrr" id="txtexamrr" maxlength="10" class="form-control">
                        <span class="input-group-text">/minute</span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="form-label" for="txtexamhr">
                        Heart Rate
                      </label>
                      <div class="input-group">
                        <input type="text" name="txtexamhr" id="txtexamhr" maxlength="10" class="form-control">
                        <span class="input-group-text">bpm</span>
                      </div>
                    </div>

                  </div>
                </div>
              </div>

              <div class="card-modern">

                <div class="card-title">
                  Riwayat Pasien
                </div>

                <div class="history-grid">

                  <!-- ALERGI OBAT -->
                  <div class="history-row">

                    <div class="history-label">
                      Alergi Obat
                    </div>

                    <div class="history-option">

                      <label>
                        <input type="checkbox" checked="checked" name="optmedialle-no" id="optmedialle-no" value="true"
                          onclick="
                        if (checked == true){
                            document.getElementById('optmedialle-yes').checked = false;
                            document.getElementById('hidmedialle').value = 'N';
                        }">
                        Tidak Ada
                      </label>

                      <label>
                        <input type="checkbox" name="optmedialle-yes" id="optmedialle-yes" value="true" onclick="
                        if (checked == true){
                            document.getElementById('optmedialle-no').checked = false;
                            document.getElementById('hidmedialle').value = 'Y';
                        }">
                        Ada
                      </label>

                      <input name="hidmedialle" id="hidmedialle" type="hidden">

                    </div>

                  </div>

                  <!-- ALERGI MAKANAN -->
                  <div class="history-row">

                    <div class="history-label">
                      Alergi Makanan
                    </div>

                    <div class="history-option">

                      <label>
                        <input type="checkbox" checked="checked" name="optfoodalle-no" id="optfoodalle-no" value="true"
                          onclick="
                        if (checked == true){
                            document.getElementById('optfoodalle-yes').checked = false;
                            document.getElementById('hidfoodalle').value = 'N';
                        }">
                        Tidak Ada
                      </label>

                      <label>
                        <input type="checkbox" name="optfoodalle-yes" id="optfoodalle-yes" value="true" onclick="
                        if (checked == true){
                            document.getElementById('optfoodalle-no').checked = false;
                            document.getElementById('hidfoodalle').value = 'Y';
                        }">
                        Ada
                      </label>

                      <input name="hidfoodalle" id="hidfoodalle" type="hidden">

                    </div>

                  </div>

                  <!-- PENYAKIT KRONIS -->
                  <div class="history-row">

                    <div class="history-label">
                      Penyakit Kronis
                    </div>

                    <div class="history-option">

                      <label>
                        <input type="checkbox" checked="checked" name="optchrodsse-no" id="optchrodsse-no" value="true"
                          onclick="
                        if (checked == true){
                            document.getElementById('optchrodsse-yes').checked = false;
                            document.getElementById('hidchrodsse').value = 'N';
                        }">
                        Tidak Ada
                      </label>

                      <label>
                        <input type="checkbox" name="optchrodsse-yes" id="optchrodsse-yes" value="true" onclick="
                        if (checked == true){
                            document.getElementById('optchrodsse-no').checked = false;
                            document.getElementById('hidchrodsse').value = 'Y';
                        }">
                        Ada
                      </label>

                      <input name="hidchrodsse" id="hidchrodsse" type="hidden">

                    </div>

                  </div>

                  <!-- PENYAKIT LAIN -->
                  <div class="history-row">

                    <div class="history-label">
                      Penyakit Lainnya
                    </div>

                    <div class="history-option">

                      <label>
                        <input type="checkbox" checked="checked" name="optothrdsse-no" id="optothrdsse-no" value="true"
                          onclick="
                        if (checked == true){
                            document.getElementById('optothrdsse-yes').checked = false;
                            document.getElementById('hidothrdsse').value = 'N';
                        }">
                        Tidak Ada
                      </label>

                      <label>
                        <input type="checkbox" name="optothrdsse-yes" id="optothrdsse-yes" value="true" onclick="
                        if (checked == true){
                            document.getElementById('optothrdsse-no').checked = false;
                            document.getElementById('hidothrdsse').value = 'Y';
                        }">
                        Ada
                      </label>

                      <input name="hidothrdsse" id="hidothrdsse" type="hidden">

                    </div>

                  </div>

                  <!-- RAWAT INAP -->
                  <div class="history-row">

                    <div class="history-label">
                      Rawat Inap
                    </div>

                    <div class="history-option">

                      <label>
                        <input type="checkbox" checked="checked" name="optpaticare-no" id="optpaticare-no" value="true"
                          onclick="
                        if (checked == true){
                            document.getElementById('optpaticare-yes').checked = false;
                            document.getElementById('hidpaticare').value = 'N';
                        }">
                        Tidak
                      </label>

                      <label>
                        <input type="checkbox" name="optpaticare-yes" id="optpaticare-yes" value="true" onclick="
                        if (checked == true){
                            document.getElementById('optpaticare-no').checked = false;
                            document.getElementById('hidpaticare').value = 'Y';
                        }">
                        Ya
                      </label>

                      <input name="hidpaticare" id="hidpaticare" type="hidden">

                    </div>

                  </div>

                  <!-- OPERASI -->
                  <div class="history-row">

                    <div class="history-label">
                      Operasi
                    </div>

                    <div class="history-option">

                      <label>
                        <input type="checkbox" checked="checked" name="optpatisurge-no" id="optpatisurge-no" value="true"
                          onclick="
                        if (checked == true){
                            document.getElementById('optpatisurge-yes').checked = false;
                            document.getElementById('hidpatisurge').value = 'N';
                        }">
                        Tidak
                      </label>

                      <label>
                        <input type="checkbox" name="optpatisurge-yes" id="optpatisurge-yes" value="true" onclick="
                        if (checked == true){
                            document.getElementById('optpatisurge-no').checked = false;
                            document.getElementById('hidpatisurge').value = 'Y';
                        }">
                        Ya
                      </label>

                      <input name="hidpatisurge" id="hidpatisurge" type="hidden">

                    </div>

                  </div>

                  <!-- MEROKOK -->
                  <div class="history-row">

                    <div class="history-label">
                      Merokok
                    </div>

                    <div class="history-option">

                      <label>
                        <input type="checkbox" checked="checked" name="optpatismoke-no" id="optpatismoke-no" value="true"
                          onclick="
                        if (checked == true){
                            document.getElementById('optpatismoke-yes').checked = false;
                            document.getElementById('hidpatismoke').value = 'N';
                        }">
                        Tidak
                      </label>

                      <label>
                        <input type="checkbox" name="optpatismoke-yes" id="optpatismoke-yes" value="true" onclick="
                        if (checked == true){
                            document.getElementById('optpatismoke-no').checked = false;
                            document.getElementById('hidpatismoke').value = 'Y';
                        }">
                        Ya
                      </label>

                      <input name="hidpatismoke" id="hidpatismoke" type="hidden">
                    </div>
                  </div>
                </div>
              </div>

              <div class="card-modern">

                <div class="card-title">
                  Pemeriksaan
                </div>

                <!-- TOP -->
                <div class="form-grid-3">
                  <!-- ANAMNESA -->
                  <div class="card-exam">
                    <div class="form-label">
                      Anamnesa
                    </div>

                    <textarea id="txtexamanam" name="txtexamanam" rows="3"></textarea>
                  </div>

                  <!-- PEMERIKSAAN FISIK -->
                  <div class="card-exam">

                    <div class="form-label">
                      Pemeriksaan Fisik
                    </div>

                    <textarea id="txtexambody" name="txtexambody" rows="3" class="form-control"></textarea>

                  </div>

                  <!-- TERAPI -->
                  <div class="card-exam">

                    <div class="form-label">
                      Terapi Non Obat
                    </div>

                    <select name="" id="" style="height: 35px; padding: 5px; font-size: 14px;">
                      <option value="">- PILIH -</option>
                      <optgroup label="Edukasi dan Modifikasi Gaya Hidup">
                        <option value="">Konseling Diet / Gizi</option>
                        <option value="">Edukasi Berhenti Merokok</option>
                        <option value="">Aktifitas Fisik</option>
                      </optgroup>
                      <optgroup label="Terapi Fisik">
                        <option value="">Terapi Panas / dingin</option>
                        <option value="">Latihan Rentang Gerak</option>
                        <option value="">Pemijatan</option>
                      </optgroup>
                      <optgroup label="Perawatan Luka dan Tindakan">
                        <option value="">Perawatan Luka</option>
                        <option value="">Imobilisasi</option>
                        <option value="">Ekstraksi Corpus Alienum</option>
                      </optgroup>
                      <optgroup label="Terapi Psikologis">
                        <option value="">Managemen Stres</option>
                        <option value="">Konseling Psikologis</option>
                      </optgroup>
                      <optgroup label="Terapi Pendukung Lainnya">
                        <option value="">Akupuntur / Akupresur</option>
                        <option value="">Edukasi Postur</option>
                      </optgroup>
                    </select>

                  </div>

                </div>

                <!-- DIAGNOSA -->
                <div class="diagnosa-section">

                  <div class="form-label">
                    Diagnosa
                  </div>

                  <div class="diagnosa-wrapper">

                    <!-- SEARCH -->
                    <div class="diagnosa-search">

                      <input type="text" name="txtlistdiag" id="txtlistdiag" maxlength="200" autocomplete="off"
                        class="form-control" placeholder="Cari diagnosa ICD..." onkeyup="
                if (value.length > 0) {
                    let regicode = document.getElementById('txtexamcode').value;
                    ambildiagnosa(regicode,this.value);
                } else {
                    document.getElementById('tbllistdiag').style.display = 'none';
                }">

                    </div>

                    <div id="tbllistdiag">
                    </div>

                    <div id="tbldiagnosa">
                    </div>


                  </div>

                </div>

                <!-- NOTE + RESEP -->
                <div class="form-grid-2">

                  <!-- NOTE -->
                  <div class="card-exam">

                    <div class="form-label">
                      Note
                    </div>

                    <textarea id="txtexamdiag" name="txtexamdiag" rows="3" class="form-control"></textarea>

                  </div>

                  <!-- RESEP -->
                  <div class="card-exam">

                    <div class="form-label">
                      Terapi Obat / Resep
                    </div>

                    <textarea id="txtexamprsc" name="txtexamprsc" rows="3" class="form-control" onblur="
                    var lines = this.value.split(/\r?\n/);var out = [];
                    for(var i=0; i<lines.length; i++) {
                      var line = lines[i].trim();
                        if(line !== '') {
                        if(!line.startsWith('-')) {
                          line = '-' + line;
                        }
                        out.push(line);
                      }
                    }
                        this.value = out.join('\n');
                    "></textarea>

                  </div>
                </div>
              </div>

              <!-- CARD LAIN LAIN (KIRI) & PERAWATAN PASIEN (KANAN) -->
              <div class="form-grid-2">

                <!-- CARD LAIN LAIN -->
                <div class="card-modern">
                  <div class="card-title">
                    Lain Lain
                  </div>

                  <!-- Fitur Cetak Surat -->
                  <div class="form-group">
                    <label class="form-label" for="jenis_surat_baru">Cetak Surat</label>
                    <select id="jenis_surat_baru" style="height: 35px; padding: 5px; font-size: 14px;" disabled="true">
                      <option value="">-- PILIH JENIS SURAT --</option>
                      <option value="SKBW">SKBW</option>
                      <option value="SURAT_SEHAT">Surat Sehat</option>
                      <option value="SURAT_KETERANGAN">Surat Keterangan</option>
                    </select>
                    <button type="button" id="btn-cetak-surat" class="btn-modern btn-refresh"
                      onclick="executeCetakSurat()" style="margin-top: 10px; align-self: flex-start;"
                      disabled="true">Cetak Surat</button>
                  </div>


                  <!-- Fitur Rujuk Pasien -->
                  <div class="form-group">
                    <label class="form-label">Rujuk Pasien</label>
                    <div style="margin: 5px 0;">
                      <label class="form-label"
                        style="display: inline-flex; align-items: center; margin-right: 15px; font-weight: normal; cursor: pointer;">
                        <input type="radio" name="radarujuk" id="radarujuk_rs" value="RS" onclick="toggleRujukNotes(true)"
                          disabled style="margin-right: 5px; width: auto !important;">
                        Rujuk Rumah Sakit (Eksternal)
                      </label>
                      <label class="form-label"
                        style="display: inline-flex; align-items: center; font-weight: normal; cursor: pointer;">
                        <input type="radio" name="radarujuk" id="radarujuk_lab" value="LB"
                          onclick="toggleRujukNotes(false)" disabled style="margin-right: 5px; width: auto !important;">
                        Rujuk Laboratorium (Internal)
                      </label>
                    </div>

                    <div id="div_rujuk_notes" style="margin-top: 5px;">
                      <textarea id="txtrujuknote" name="txtrujuknote"
                        placeholder="Note Rujukan / Nama Rumah Sakit / Pemeriksaan Laboratorium" disabled rows="2"
                        class="form-control" style="min-height: 60px;"></textarea>
                    </div>

                    <button type="button" id="btn-rujuk" class="btn-modern btn-refresh" onclick="executeRujukPasien()"
                      disabled style="margin-top: 10px; align-self: flex-start;">Rujuk Pasien</button>
                  </div>
                </div>

                <!-- CARD PERAWATAN PASIEN -->
                <div class="card-modern">
                  <div class="card-title">
                    Perawatan Pasien
                  </div>

                  <div class="form-group" style="position: relative;">
                    <label class="form-label" for="txtmedicode">Layanan/Tindakan :</label>
                    <input type="text" name="txtmedicode" id="txtmedicode" maxlength="100" autocomplete="off"
                      class="form-control" placeholder="Cari layanan/tindakan..." onkeyup="
                      if (this.value.length >= 0) {
                          let regipoli = document.getElementById('hidmediroom').value;
                          let regipaym = document.getElementById('hidregipaym').value;
                          cariTindakan(this.value, regipoli, regipaym);
                      } else {
                          document.getElementById('tbltindakan').style.display = 'none';
                      }
                    " disabled>

                    <!-- Hasil Search Tindakan -->
                    <div id="tbltindakan"
                      style="position: absolute; top: 100%; left: 0; width: 100%; background-color: white; display: none; z-index: 1000; margin-top: 10px; border-radius: 12px; overflow-y: auto; max-height: 250px; box-shadow: 0 4px 6px rgba(0,0,0,0.15); border: 1px solid #d1d5db;">
                    </div>
                  </div>

                  <div class="form-group" style="margin-top: 15px;">
                    <label class="form-label" for="txttretquty">Qty :</label>
                    <input type="number" name="txttretquty" id="txttretquty" min="1" value="1" disabled
                      class="form-control" style="width: 80px;"
                      onkeydown="if(event.keyCode==13){ event.preventDefault(); }">
                  </div>

                  <input type="hidden" name="hidmedicode" id="hidmedicode">
                  <input type="hidden" name="hidmedirate" id="hidmedirate">
                  <input type="hidden" name="hidmediroom" id="hidmediroom">
                  <input type="hidden" name="hidregipaym" id="hidregipaym">

                  <button type="button" id="btn-input-tindakan" class="btn-modern btn-save"
                    onclick="executeInputTindakan()" disabled style="margin-top: 15px; align-self: flex-start;">Input
                    Tindakan</button>

                  <!-- Tabel Tindakan Terpilih -->
                  <div id="tbltreatment" style="margin-top: 10px; width: 100%;"></div>
                </div>

              </div>

              <div class="card-modern" id="card-odontogram" style="display: block;">
                <div class="card-title">
                  Pemeriksaan Gigi (Odontogram)
                </div>

                <div style="margin-bottom: 15px; display: flex; gap: 10px; font-size: 12px;">
                  <div><span
                      style="display:inline-block; width:12px; height:12px; background:white; border:1px solid #ccc;"></span>
                    Sehat</div>
                  <div><span style="display:inline-block; width:12px; height:12px; background:red;"></span> Karies
                    (Berlubang)</div>
                  <div><span style="display:inline-block; width:12px; height:12px; background:blue;"></span> Tambalan
                  </div>
                  <div><span style="display:inline-block; width:12px; height:12px; background:black;"></span> Missing
                    (Hilang)</div>
                </div>

                <div id="odontogram-wrapper"
                  style="overflow-x: auto; padding: 10px; border: 1px solid #ddd; background: #fafafa; border-radius: 8px;">
                  <div id="odontogram-canvas">
                    <?php ?>
                  </div>
                </div>

                <input type="hidden" name="hidodontogram" id="hidodontogram" value="{}">
              </div>

              <!-- SUBMIT PEMERIKSAAN CARD (CLEANED) -->
              <div class="card-modern">
                <button type="button" class="btn-modern btn-save" onclick="
                if (document.getElementById('txtexamhght').value == '')
                {
                    swal({
                        title: 'Tinggi Badan Kosong' ,
                        text: 'Anda belum mengisi Tinggi Badan, silah periksa lagi',
                        icon: 'warning',
                    });
                }
                else if (document.getElementById('txtexamwght').value == '')
                {
                    swal({
                        title: 'Berat Badan Kosong' ,
                        text: 'Anda belum mengisi Berat Badan, silah periksa lagi',
                        icon: 'warning',
                    });
                }
                else if (document.getElementById('txtexamblod').value == '')
                {
                    swal({
                        title: 'Tekanan Darah Kosong' ,
                        text: 'Anda belum mengisi Tekanan Darah, silah periksa lagi',
                        icon: 'warning',
                    });
                }
                else if (document.getElementById('txtexamtemp').value == '')
                {
                    swal({
                        title: 'Temperatur Kosong' ,
                        text: 'Anda belum mengisi Temperatur Suhu, silah periksa lagi',
                        icon: 'warning',
                    });
                }
                else if (document.getElementById('txtexamanam').value == '')
                {
                    swal({
                        title: 'Anamnesa Kosong' ,
                        text: 'Anda belum mengisi Anamnesa, silah periksa lagi',
                        icon: 'warning',
                    });
                }
                else if (document.getElementById('txtexamprsc').value == '')
                {
                    swal({
                        title: 'Resep Kosong' ,
                        text: 'Anda belum mengisi E-Resep, silah periksa lagi',
                        icon: 'warning',
                    });
                }
                else
                {
                    var inexamcode = document.getElementById('txtexamcode').value;
                    var inexamdoct = document.getElementById('hidexamdoct').value;
                    var inexamhght = document.getElementById('txtexamhght').value;
                    var inexamwght = document.getElementById('txtexamwght').value;
                    var inexamwaist = document.getElementById('txtexamwaist').value;
                    var inexambmi = document.getElementById('txtexambmi').value;
                    var inexamblod = document.getElementById('txtexamblod').value;
                    var inexamtemp = document.getElementById('txtexamtemp').value;
                    var inexamrr = document.getElementById('txtexamrr').value;
                    var inexamhr = document.getElementById('txtexamhr').value;
                    var inexamcomp = document.getElementById('txtexamcomp').value;
                    var inmedialle = document.getElementById('hidmedialle').value;
                    var infoodalle = document.getElementById('hidfoodalle').value;
                    var inchrodsse = document.getElementById('hidchrodsse').value;
                    var inothrdsse = document.getElementById('hidothrdsse').value;
                    var inpaticare = document.getElementById('hidpaticare').value;
                    var inpatisurge = document.getElementById('hidpatisurge').value;
                    var inpatismoke = document.getElementById('hidpatismoke').value;
                    var inexamanam = document.getElementById('txtexamanam').value;
                    var inexambody = document.getElementById('txtexambody').value;
                    var inexamdiag = document.getElementById('txtexamdiag').value;
                    var inexamprsc = document.getElementById('txtexamprsc').value;

                    input(
                        inexamcode,
                        inexamdoct,
                        inexamhght,
                        inexamwght,
                        inexamwaist,
                        inexambmi,
                        inexamblod,
                        inexamtemp,
                        inexamrr,
                        inexamhr,
                        inexamcomp,
                        inmedialle,
                        infoodalle,
                        inchrodsse,
                        inothrdsse,
                        inpaticare,
                        inpatisurge,
                        inpatismoke,
                        inexamanam,
                        inexambody,
                        inexamdiag,
                        inexamprsc
                    );

                    document.getElementById('tblrekammedis').style.visibility = 'hidden';
                    document.getElementById('tblrekammedis').innerHTML = '';
                }
                ">
                  Simpan Pemeriksaan
                </button>
              </div>
          </div>
        </div>

        </form>
      </div>
    </div>
    <div class="footerdate">
      <span class="labelTime Time"><b>Date :</b> <?php $tgl = date('d-m-Y');
      echo $tgl; ?></span>
    </div>
    <div class="footertime">
      <span class="labelTime Time" id="timestamp"></span>
    </div>

    </div>
    </div>
    <script src="js/TRXAPOLI01.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/ui.js"></script>
    <script>
      document.addEventListener('click', function (e) {
        const btn = e.target.closest('.button-panggil');
        if (!btn) return;

        const nomor = btn.getAttribute('data-noantri') || '';
        const nama = btn.getAttribute('data-nama') || '';
        const poli = btn.getAttribute('data-poli') || '';
        const channel = btn.getAttribute('data-channel') || 'POLI';

        const params = "channel=" + encodeURIComponent(channel)
          + "&nomor=" + encodeURIComponent(nomor)
          + "&nama=" + encodeURIComponent(nama)
          + "&poli=" + encodeURIComponent(poli);

        fetch('panggil_queue.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: params
        })
          .then(r => r.json())
          .then(res => {
            console.log('RESP panggil_queue POLI:', res);
            // kalau mau: if (!res.ok) alert(res.error);
          })
          .catch(err => {
            console.error('Error fetch panggil_queue POLI:', err);
          });
      });
    </script>


  </body>

  </html>
  <?php
} else {
  header("Location: " . "signin.php");
}
?>