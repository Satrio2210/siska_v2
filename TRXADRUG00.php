<!-- TRXADRUG00.php -->

<!doctype html>
<?php include "conf/config.php";
session_start();

if (isset($_SESSION['username'])) {
  $user = $_SESSION['username'];

  ?>
  <html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Klinik Pratama">
    <title>Resep</title>
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
      :root {
        --primary: #16a34a;
        --primary-dark: #15803d;
        --primary-soft: #dcfce7;
        --bg: #f3f6fb;
        --card: #ffffff;
        --border: #dbe4ee;
        --text: #0f172a;
        --muted: #64748b;
        --danger: #dc2626;
        --shadow: 0 2px 6px rgba(15, 23, 42, .04), 0 8px 24px rgba(15, 23, 42, .06);
        --radius: 16px;
      }

      .modern-textarea {
        width: 100%;
        box-sizing: border-box;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 13px;
        background: white;
        transition: .2s;
      }

      .modern-textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(22, 163, 74, .12);
      }

      .modern-textarea {
        resize: none;
        min-height: 70px;
      }

      .patient-list-wrapper {
        margin-top: 0;
      }

      .patient-search {
        margin-bottom: 10px;
      }

      .patient-list-scroll {
        max-height: 420px;
        overflow: auto;
      }

      .workspace-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-top: 0;
      }

      .full-width {
        grid-column: 1/-1;
      }

      .form-label {
        display: block;
        margin-bottom: 4px;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
      }

      .checkbox-row {
        display: flex;
        gap: 18px;
        align-items: center;
      }

      .checkbox-modern {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 600;
      }

      .action-row {
        margin-top: 12px;
        display: flex;
        gap: 8px;
      }

      .btn-close {
        background: #e2e8f0;
        color: #334155;
      }

      .btn-close:hover {
        background: #cbd5e1;
        color: #0f172a;
      }

      .recipe-table-wrapper {
        margin-top: 0;
      }

      .recipe-table-scroll {
        max-height: 260px;
        overflow: auto;
      }

      #screen th {
        padding: 8px !important;
        font-size: 12px !important;
      }

      #screen td {
        padding: 7px !important;
        font-size: 12px !important;
      }

      #screen {
        font-size: 12px !important;
      }

      .input-resep-wrapper {
        position: relative;
      }

      #tblregi {
        position: relative !important;
        width: 100%;
        top: auto !important;
        left: auto !important;
        right: auto !important;
        background: transparent;
        border: none;
        border-radius: 0;
        box-shadow: none;
        overflow: visible;
        z-index: auto;
      }

      #tblsigna,
      #tblsigna_racik {
        position: absolute;
        background: white;
        border-radius: 10px;
        overflow: auto;
        box-shadow: 0 8px 10px rgba(0, 0, 0, .10);
        border: 1px solid #e2e8f0;
        z-index: 9999;
      }

      #tblsigna table tbody,
      #tblsigna_racik table tbody {
        display: block;
        max-height: 140px;
        overflow-y: auto;
        overflow-x: hidden;
      }

      #tblsigna table thead,
      #tblsigna table tbody tr,
      #tblsigna_racik table thead,
      #tblsigna_racik table tbody tr {
        display: table;
        width: auto;
        table-layout: fixed;
      }

      #tblresep,
      #tblresep_racik {
        position: absolute;
        top: 100%;
        left: 0;
        min-width: 500px;
        max-height: 220px;
        margin-top: 4px;
        background: #fff;
        border: 1px solid #a5a4a4;
        border-radius: 10px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        z-index: 9999;
        display: none;
        overflow-y: auto;
        overflow-x: hidden;
      }

      @media(max-width:1100px) {
        .workspace-grid {
          grid-template-columns: 1fr;
        }

        .form-grid {
          grid-template-columns: 1fr;
        }
      }

      #txtexamprsc {
        height: auto !important;
        padding: 10px !important;
        font-size: 13px;
        line-height: 1.6;
        background: white !important;
        color: #0f172a !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 10px;
        font-family: monospace;
      }

      .autocomplete-wrapper {
        position: relative;
        width: 100%;
      }

      /* RM static table (like TRXAPOLI01C-REKAMMEDIS) */
      .rm-static-wrap {
        width: 100%;
        overflow: auto;
        border: 1px solid #d1d5db;
        border-radius: 12px;
        background: #fff;
      }

      .rm-static-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        margin: 0;
      }

      .rm-static-table thead th {
        padding: 12px 10px;
        font-size: 13px;
        font-weight: 700;
        text-align: center;
        color: #fff;
        background: #0D9488;
        border: none;
        border-bottom: 1px solid #059669;
      }

      .rm-static-table tbody td {
        padding: 12px;
        font-size: 12px;
        font-weight: 500;
        color: #111827;
        vertical-align: top;
        border-bottom: 1px solid #e5e7eb;
        line-height: 1.55;
        word-wrap: break-word;
      }

      .rm-static-table .label-bold {
        font-weight: 700;
        color: #0f172a;
      }

      .rm-static-table .inner-table {
        width: 100%;
        border-collapse: collapse;
      }

      .rm-static-table .inner-table td {
        padding: 2px 0;
        border: none;
        font-size: 12px;
      }

      .rm-static-table .inner-table .label-bold {
        width: 70px;
        white-space: nowrap;
      }

      .recipe-doctor {
        margin-top: 12px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 10px;
        font-weight: 700;
      }

      .close-bar {
        display: flex;
        justify-content: center;
        padding: 8px 0 4px;
      }

      .close-bar .btn-modern {
        min-width: 160px;
        height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
      }

      #sectionForm {
        display: none;
      }

      #titlecarddp {
        /* Ganti 80px dengan tinggi header lu yang sebenarnya */
        scroll-margin-top: 88px;
      }
    </style>
  </head>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="js/jquery.js"></script>
  <script type="text/javascript" src="js/sanie.js"></script>
  <script src="js/sweetalert.min.js"></script>
  <script type="text/javascript" src="js/TRXASALE01.js"></script>

  <script>
    $(document).ready(function () {
      setInterval(timestamp, 1000);
    });
    function timestamp() { $.ajax({ url: 'inc/timestamp.php', success: function (data) { $('#timestamp').html(data); }, }); }
  </script>

  <script>
    $(document).ready(function () {
      var sudahRapi = false;

      function rapikanResep() {
        var prscBox = document.getElementById('txtexamprsc');
        if (prscBox && prscBox.value.length > 0 && !sudahRapi) {
          var rawText = prscBox.value;
          if (rawText.includes('-') && !rawText.includes('\n')) {
            var cleanText = rawText.replace(/-/g, '\n-');
            prscBox.value = cleanText.trim();
            sudahRapi = true;
          }
        }
      }

      rapikanResep();
      var interval = setInterval(function () {
        if (sudahRapi) {
          clearInterval(interval);
        } else {
          rapikanResep();
        }
      }, 1000);
    });
  </script>

  <audio id="notifAudio" preload="auto">
    <source src="assets/audio/notif.mp3" type="audio/mpeg">
  </audio>

  <body onLoad="periksaakses('PASS_DRUG_ENTR');">
    <div id="wrapper">
      <?php include "inc/side-menu.php"; ?>

      <div id="content-wrapper">
        <?php include "inc/header.php"; ?>
        <div class="content">
          <div class="content-modern">

            <form name="frmtrxadrug" class="pure-form" method="post">
              <input type="hidden" name="hidprscdoct" id="hidprscdoct" value="<?php echo $user; ?>">
              <input type="hidden" name="hidregipaym" id="hidregipaym">
              <input type="hidden" name="hidmediroom" id="hidmediroom">
              <input type="hidden" name="hidstockcode" id="hidstockcode">
              <input type="hidden" name="hidstockbtch" id="hidstockbtch">
              <input type="hidden" name="hidstockpric" id="hidstockpric">
              <input type="hidden" name="hidstockamnt" id="hidstockamnt">
              <input type="hidden" name="hidprscconc" id="hidprscconc">
              <input type="hidden" name="hidsigna" id="hidsigna">

              <!-- LIST PASIEN (default view) -->
              <div class="card-modern" id="sectionList">
                <div class="card-title">List Pasien / Registrasi</div>
                <div class="form-group">
                  <input type="text" name="txtsearch" id="txtsearch" class="form-control"
                    placeholder="Cari pasien / nomor RM..." autocomplete="off"
                    onkeyup="if (value.length > 0) { ambilregicode(this.value); } else { ambilregicode('X')};">
                </div>

                <div id="tblregi"></div>

              </div>

              <!-- FORM INPUT RESEP (setelah Periksa) -->
              <div id="sectionForm">

                <!-- Card Data Pasien & RM -->
                <div class="card-modern" id="titlecarddp">
                  <div class="card-title">Data Pasien &amp; Rekam Medis</div>
                  <div class="rm-static-wrap">
                    <table class="rm-static-table">
                      <thead>
                        <tr>
                          <th>Data Pasien</th>
                          <th>TTV Antropometri</th>
                          <th>Hasil Pemeriksaan</th>
                          <th>Tindak Lanjut</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>
                            <table class="inner-table">
                              <tr>
                                <td class="label-bold">No Reg</td>
                                <td style="width:5px;">:</td>
                                <td><span id="lblprsccode">-</span>
                                  <input type="hidden" id="txtprsccode">
                                </td>
                              </tr>
                              <tr>
                                <td class="label-bold">No RM</td>
                                <td>:</td>
                                <td><span id="lblpaticode">-</span>
                                  <input type="hidden" id="txtpaticode">
                                </td>
                              </tr>
                              <tr>
                                <td class="label-bold">Nama</td>
                                <td>:</td>
                                <td><span id="lblmainname">-</span>
                                  <input type="hidden" id="txtmainname">
                                </td>
                              </tr>
                              <tr>
                                <td class="label-bold">JK</td>
                                <td>:</td>
                                <td><span id="lblmaingend">-</span>
                                  <input type="hidden" id="txtmaingend">
                                </td>
                              </tr>
                              <tr>
                                <td class="label-bold">Usia</td>
                                <td>:</td>
                                <td><span id="lblmainage">-</span>
                                  <input type="hidden" id="txtmainage">
                                </td>
                              </tr>
                              <tr>
                                <td class="label-bold">Bayar</td>
                                <td>:</td>
                                <td><span id="lblregipaym">-</span>
                                  <input type="hidden" id="txtregipaym">
                                </td>
                              </tr>
                            </table>
                          </td>
                          <td>
                            <table class="inner-table">
                              <tr>
                                <td class="label-bold">- TD</td>
                                <td style="width:5px;">:</td>
                                <td id="lblttv_td">-</td>
                              </tr>
                              <tr>
                                <td class="label-bold">- BB</td>
                                <td>:</td>
                                <td id="lblttv_bb">-</td>
                              </tr>
                              <tr>
                                <td class="label-bold">- TB</td>
                                <td>:</td>
                                <td id="lblttv_tb">-</td>
                              </tr>
                              <tr>
                                <td class="label-bold">- Nadi</td>
                                <td>:</td>
                                <td id="lblttv_nadi">-</td>
                              </tr>
                              <tr>
                                <td class="label-bold">- Suhu</td>
                                <td>:</td>
                                <td id="lblttv_suhu">-</td>
                              </tr>
                              <tr>
                                <td class="label-bold">- RR</td>
                                <td>:</td>
                                <td id="lblttv_rr">-</td>
                              </tr>
                            </table>
                          </td>
                          <td>
                            <div><span class="label-bold">- Anamnesa</span><br><span id="lblhasil_anam">-</span></div>
                            <div style="margin-top:8px;"><span class="label-bold">- Diagnosis</span><br><span
                                id="lblhasil_diag">-</span>
                              <input type="hidden" id="txtexamdiag">
                            </div>
                            <div style="margin-top:8px;"><span class="label-bold">- Keluhan</span><br><span
                                id="lblhasil_keluhan">-</span></div>
                          </td>
                          <td>
                            <div><span class="label-bold">- Rencana / Catatan</span><br><span id="lbltl_rencana">-</span>
                            </div>
                            <div style="margin-top:8px;"><span class="label-bold">- Rujukan</span><br><span
                                id="lbltl_rujuk">-</span></div>
                            <div style="margin-top:8px;"><span class="label-bold">- Farmakoterapi</span><br><span
                                id="lbltl_resep">-</span></div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <div class="recipe-doctor">
                    <div style="margin-bottom:6px;">RESEP DOKTER</div>
                    <textarea id="txtexamprsc" readonly class="modern-textarea"
                      style="min-height:120px; background:#0f172a !important; color:white !important; border:none !important; font-family:monospace;"></textarea>
                  </div>
                </div>

                <!-- Input Non-Racikan | Racikan -->
                <div class="workspace-grid">
                  <div class="card-modern">
                    <div class="card-title" id="inputresep">Input Resep Non Racikan</div>

                    <div class="form-grid">

                      <div class="form-group">
                        <label class="form-label">Cari Obat</label>
                        <input type="text" name="txtstockcode" id="txtstockcode" class="form-control" autocomplete="off"
                          placeholder="Ketik nama obat..."
                          onkeyup="if (value.length > 0) { var regipoli = document.getElementById('hidmediroom').value; var regipaym = document.getElementById('hidregipaym').value; ambilresep(this.value,regipoli,regipaym); } else { document.getElementById('tblresep').innerHTML=''; document.getElementById('tblresep').style.display='none'; }">
                        <div id="tblresep"></div>
                      </div>

                      <div class="form-group">
                        <label class="form-label">Qty</label>
                        <input type="text" name="txtstockquty" id="txtstockquty" value="1" class="form-control">
                      </div>

                      <div class="form-group full">
                        <label class="form-label">Signa</label>
                        <input type="text" name="txtsigna" id="txtsigna" autocomplete="off" class="form-control"
                          placeholder="Aturan makan..."
                          onkeyup="if (value.length > 0) { ambilsignacode(this.value); } else { document.getElementById('tblsigna').style.display='none'; }">
                        <div id="tblsigna" style="display: none;"></div>
                      </div>

                      <div class="form-group full">
                        <label class="form-label">Cara Pemakaian</label>
                        <input name="txtusage" id="txtusage" class="form-control"
                          placeholder="Contoh: diminum sesudah makan..." readonly>
                      </div>

                      <div class="full-width">
                        <div class="checkbox-row" style="display:none;">
                          <label class="checkbox-modern">
                            <input type="checkbox" name="optnonracikan" id="optnonracikan"
                              onclick="if (checked == true) { document.getElementById('optracikan').checked=false; document.getElementById('hidprscconc').value='N'; }">
                            Non Racikan
                          </label>
                          <label class="checkbox-modern">
                            <input type="checkbox" name="optracikan" id="optracikan"
                              onclick="if (checked == true) { document.getElementById('optnonracikan').checked=false; document.getElementById('hidprscconc').value='Y'; }">
                            Racikan
                          </label>
                        </div>
                      </div>

                    </div>

                    <div>
                      <button type="button" class="btn-modern btn-input"
                        onclick="javascript: 
                          var ambil = parseInt(document.getElementById('txtstockquty').value); 
                          var tersedia = parseInt(document.getElementById('hidstockamnt').value); if (document.getElementById('txtstockcode').value == '') { swal({ title:'Item Obat Kosong', text:'Anda belum memilih item obat', icon:'warning' }); } else { var inprsccode = document.getElementById('txtprsccode').value; 
                          var inprscdoct = document.getElementById('hidprscdoct').value; var instockcode = document.getElementById('hidstockcode').value; var instockbtch = document.getElementById('hidstockbtch').value; 
                          var instockpric = document.getElementById('hidstockpric').value; 
                          var instockquty = document.getElementById('txtstockquty').value; 
                          var inprscconc = 'N'; 
                          var inprscsgna = document.getElementById('hidsigna').value; 
                          var inmediroom = document.getElementById('hidmediroom').value;
                          var insgnausag = document.getElementById('txtusage').value;
                          input( inprsccode, inprscdoct, instockcode, instockbtch, instockpric, instockquty, inprscconc, inprscsgna, inmediroom, insgnausag ); }">
                        Input Resep
                      </button>
                    </div>

                  </div>

                  <div class="card-modern">
                    <div class="card-title">Input Resep Racikan</div>
                    <div class="form-grid">

                      <div class="form-group">
                        <label class="form-label">Nama Racikan</label>
                        <input type="text" id="txtracikname" class="form-control" placeholder="Nama Racikan"
                          autocomplete="off">
                      </div>

                      <div class="form-group">
                        <label class="form-label">Qty</label>
                        <input type="number" id="txtracikqty" class="form-control" value="1" min="1">
                      </div>

                      <div class="form-group full" style="margin-bottom: 12px;">
                        <label class="form-label">Signa</label>
                        <input type="text" id="txtraciksigna" class="form-control" placeholder="Aturan makan..."
                          autocomplete="off"
                          onkeyup="if (value.length > 0) { ambilsignacode_racik(value); } else { document.getElementById('tblsigna_racik').style.display='none'; }">

                        <div id="tblsigna_racik"></div>

                        <input type="hidden" id="hidraciksignacode">
                        <input type="hidden" id="hidracikusage">
                      </div>

                    </div>

                    <div class="form-group">
                      <button type="button" class="btn-modern btn-input" onclick="tambahRacikanHead()">Tambah
                        Racikan</button>
                    </div>

                    <div>
                      <div id="tblracikhead" style="margin-top: 10px;"></div>
                    </div>

                    <div id="section_racik_detail">
                      <input type="hidden" id="hidselectedracikid">
                      <div id="lblSelectedRacik" style="font-size: 14px; margin-bottom:10px; font-weight: 700;">
                        <span>Racikan Terpilih: - </span>
                      </div>

                      <div class="form-grid-3 racik">

                        <div class="form-group">
                          <label class="form-label">Cari Obat</label>
                          <input type="text" id="txtracikstockname" class="form-control" placeholder="Ketik nama obat..."
                            autocomplete="off"
                            onkeyup="if (value.length > 0) { var regipoli = document.getElementById('hidmediroom').value; var regipaym = document.getElementById('hidregipaym').value; ambilresep_racik(this.value,regipoli,regipaym); } else { document.getElementById('tblresep_racik').innerHTML=''; document.getElementById('tblresep_racik').style.display='none'; }">
                          <div id="tblresep_racik"></div>
                          <input type="hidden" id="hidracikstockcode">
                          <input type="hidden" id="hidracikstockbtch">
                          <input type="hidden" id="hidracikstockpric">
                          <input type="hidden" id="hidracikstockamnt">
                        </div>

                        <div class="form-group">
                          <label class="form-label">Qty</label>
                          <input type="text" id="txtracikstockqty" class="form-control" value="1">
                        </div>

                        <div class="form-group">
                          <label class="form-label">Input</label>
                          <button type="button" class="btn-modern btn-save" style="height: 38px;"
                            onclick="tambahObatKeRacikan()">+</button>
                        </div>
                      </div>



                      <div>
                        <div id="tblracikdetail"></div>
                      </div>

                    </div>

                  </div>
                </div>

                <!-- Daftar Item Resep full width -->
                <div class="card-modern">
                  <div class="card-title">Daftar Item Resep</div>
                  <div class="recipe-table-wrapper">
                    <div class="recipe-table-scroll">
                      <div id="tblscreen"></div>
                    </div>
                  </div>
                </div>

                <!-- Tombol Tutup -->
                <div>
                  <button type="button" class="btn-modern btn-save" href="javascript:void(0)"
                    onclick="tutupResep()">Simpan</button>
                </div>

              </div>
            </form>

          </div>
        </div>
      </div>
    </div>

    <script src="js/TRXADRUG00.js?v=<?php echo time(); ?>"></script>
    <script src="js/ui.js"></script>

    <script>
      function playNotif() {
        var audio = document.getElementById('notifAudio');
        audio.currentTime = 0;
        audio.play();
      }
    </script>
  </body>

  </html>
  <?php
} else {
  header("Location: " . "signin.php");
}
?>