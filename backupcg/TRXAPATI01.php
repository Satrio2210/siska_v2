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
  <title>Daftar Pasien</title>
  <link rel="shortcut icon" href="assets/img/icon.png">
  <link rel="stylesheet" href="assets/css/pure/pure-min.css">
  <link rel="stylesheet" href="assets/css/layouts/side-menu.css">
  <style type="text/css">
    input[type="date"] { color: #ff0000; }
  </style>
  <style>
    :root { --primary: #10b981; --primary-dark: #059669; --bg: #f8fafc; --card: #ffffff; --border: #e2e8f0; --text: #0f172a; --muted: #64748b; }
    .content-modern { margin-top: 20px; }
    .card-modern { background: #fff; border-radius: 18px; padding: 20px 22px; box-shadow: 0 4px 15px rgba(0,0,0,.08); border: 3px solid #cbd5e1; margin-bottom: 20px; }
    .card-title { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 16px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .form-group { display: flex; flex-direction: column; }
    .form-group.full { grid-column: 1 / -1; }
    .form-label { font-size: 13px; font-weight: 600; color: #43474d; margin-bottom: 6px; }
    .form-control { height: 38px; border: 1px solid #dbe2ea; border-radius: 12px; padding: 0 14px; font-size: 13px; transition: .2s; font-weight: 500; width: 100%; box-sizing: border-box; }
    textarea.form-control { height: 60px; padding-top: 12px; resize: none; }
    .form-control:focus { outline: none; border-color: #10b981; box-shadow: 0 0 0 4px rgba(16,185,129,.15); }
    .form-control[readonly] { background: #f8fafc; }
    .form-control:disabled { background: #f1f5f9; cursor: not-allowed; opacity: 0.7; }
    .checkbox-group { display: flex; flex-wrap: wrap; gap: 8px 16px; align-items: center; margin-top: 4px; }
    .checkbox-group label { font-size: 13px; color: #475569; font-weight: 500; margin: 0; cursor: pointer; }
    .checkbox-group input[type="checkbox"] { margin-right: 4px; accent-color: #10b981; width: 15px; height: 15px; cursor: pointer; }
    .btn-modern { border: none; height: 44px; padding: 0 24px; border-radius: 12px; font-weight: 600; cursor: pointer; transition: .2s; font-size: 14px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; }
    .btn-modern:hover { transform: translateY(-1px); }
    .btn-save { background: #10b981; color: white; }
    .search-dropdown { position: absolute; top: 100%; left: 0; margin-top: 6px; z-index: 999; width: 100%; background: white; border-radius: 14px; box-shadow: 0 10px 25px rgba(0,0,0,.08); overflow: auto; max-height: 300px; animation: dropdownFade .15s ease; }
    @keyframes dropdownFade { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }
    .alert-hint { font-size: 12px; color: #ef4444; font-weight: 500; margin-bottom: 14px; padding: 8px 12px; background: #fef2f2; border-left: 3px solid #ef4444; border-radius: 0 8px 8px 0; }
    @media(max-width:1100px) { .form-grid { grid-template-columns: 1fr; } }
  </style>

  <style>
    /* Eye strain reduction - Background vs Card */
    body { background-color: #F3F4F6; }
    .card-modern { background: #ffffff; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border: none; }
    .card-title { font-size: 16px; font-weight: 600; color: #374151; }
    .form-label { font-size: 14px; font-weight: 600; color: #4B5563; }
    .form-control { border: 1px solid #D1D5DB; color: #1F2937; font-size: 14px; }
    .form-control:focus { border-color: #059669; box-shadow: 0 0 0 3px rgba(5,150,105,0.12); }
    .btn-save { background: #059669; }
    .btn-modern:hover { box-shadow: 0 2px 6px rgba(0,0,0,0.12); }
    .search-dropdown { border: 1px solid #E5E7EB; box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
  </style>
</head>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/sanie.js"></script>
<script src="js/sweetalert.min.js"></script>
<script>
  $(document).ready(function () { setInterval(timestamp, 1000); });
  function timestamp() { $.ajax({ url: 'inc/timestamp.php', success: function (data) { $('#timestamp').html(data); }, }); }
</script>
<body onLoad="periksaakses('PASS_REGI_ENTR');">
<div id="layout">
  <!-- Menu toggle -->
  <a href="#menu" id="menuLink" class="menu-link"><span></span></a>
  <!-- Menu Kiri -->
  <div id="menu">
    <div class="pure-menu">
      <a class="pure-menu-heading" href="#"><?php echo $_SESSION['username']; ?></a>
      <ul class="pure-menu-list">
        <li class="pure-menu-item menu-item-divided pure-menu-selected" onclick="javascript: location.href = 'index.php'"><a class="pure-menu-link">ADMISI</a></li>
        <li class="pure-menu-item" onclick="javascript: location.href = 'TRXAPATI06.php'"><a class="pure-menu-link">Harga</a></li>
        <li class="pure-menu-item" onclick="javascript: location.href = 'REPOPATI01.php'"><a class="pure-menu-link">Report</a></li>
        <li class="pure-menu-item" onclick="javascript: location.href = 'signout.php'"><a class="pure-menu-link">EXIT</a></li>
      </ul>
    </div>
  </div><!-- div menu -->
  <!-- tampilan menu -->
  <div id="main">
    <div class="header">
					<div class="header-text">
						<h1 id="login">Sistem Informasi Klinik Pratama</h1>
						<h2>SISKA</h2>
					</div>
					<img height="<?php echo $width_logo; ?>" width="<?php echo $height_logo; ?>" src="assets/img/logo.png"
						onerror="this.src='https://placehold.co/100x100/e2e8f0/475569?text=LOGO'" alt="Logo Klinik"
						style="border-radius: 12px; object-fit: cover;">
				</div>
    <div class="content">
      <!-- Tab Menu -->
      <div class="pure-menu pure-menu-horizontal">
        <ul class="pure-menu-list">
          <li class="pure-menu-item pure-menu-disabled">Daftar Pasien</li>
          <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TRXAPATI02.php'"><a class="pure-menu-link">Pasien Berobat</a></li>
          <li class="pure-menu-item pure-menu-selected" onclick="javascript: location.href = 'TRXAPATI07.php'"><a class="pure-menu-link">TTV & Antropometri</a></li>
        </ul>
      </div>
      <!-- Tab Menu -->
      <form name="frmtrxapati" method="post" action="TRXAPATI01E.php">
        <div class="content-modern">
          <div class="card-modern">
            <div class="card-title">&#x1F9D1; Data Pasien</div>
            <div class="form-grid">
              <div class="form-group full" style="position:relative;">
                <div class="alert-hint">** Cari pasien dengan Nama Lengkap / Tanggal Lahir (Tahun-Bulan-Tanggal)</div>
                <label class="form-label" for="txtsearch">Cari Pasien</label>
                <input type="text" name="txtsearch" id="txtsearch" class="form-control" autocomplete="off" maxlength="50"
                  onkeyup="
                        if (value.length > 1)
                        {
                          ambilpaticode(this.value);
                        }
                        else
                        {
                          document.getElementById('tblpati').style.display='none';
                        }
                        "
                  placeholder="Ketik nama atau tanggal lahir...">
                <div id="tblpati" class="search-dropdown" style="display:none;"></div>
              </div>
              <div class="form-group">
                <label class="form-label" for="txtmastcode">Nomor Rekam Medis</label>
                <input type="text" name="txtmastcode" id="txtmastcode" class="form-control" maxlength="10" readonly>
              </div>
              <div class="form-group">
                <label class="form-label" for="txtmainpidn">NIK</label>
                <input type="text" name="txtmainpidn" id="txtmainpidn" class="form-control" maxlength="16" autocomplete="off"
                  onblur="periksanik(this.value)"
                  onkeyup="if (value.length > 12) { let inmastcode = value.substr(8,4); document.getElementById('txtmastcode').value = inmastcode; periksanomor('1'); }"
                  onkeydown="if (event.keyCode == 13 && value.length == 16) { periksanik(value); document.getElementById('opttn').removeAttribute('disabled','true'); document.getElementById('optny').removeAttribute('disabled','true'); document.getElementById('optnn').removeAttribute('disabled','true'); document.getElementById('optan').removeAttribute('disabled','true'); document.getElementById('txtmainname').removeAttribute('disabled','true'); document.getElementById('optmale').removeAttribute('disabled','true'); document.getElementById('optfemale').removeAttribute('disabled','true'); let indaybirt = value.substr(6,2); let inmonthbirt = value.substr(8,2); let inyear = value.substr(10,2); let inyearbirt = '19'+inyear; let rawdate = inyearbirt+'-'+inmonthbirt+'-'+indaybirt; var birtdate = new Date(rawdate); var inmainbirt = convertDate(birtdate); document.getElementById('tglmainbirt').removeAttribute('disabled','true'); document.getElementById('tglmainbirt').value = inmainbirt; document.getElementById('optblooda').removeAttribute('disabled','true'); document.getElementById('optbloodb').removeAttribute('disabled','true'); document.getElementById('optbloodab').removeAttribute('disabled','true'); document.getElementById('optbloodo').removeAttribute('disabled','true'); document.getElementById('optbloodx').removeAttribute('disabled','true'); document.getElementById('txtmainaddr').removeAttribute('disabled','true'); document.getElementById('txtmainward').removeAttribute('disabled','true'); document.getElementById('txtmaindist').removeAttribute('disabled','true'); document.getElementById('txtmaincity').removeAttribute('disabled','true'); document.getElementById('txtmainprov').removeAttribute('disabled','true'); document.getElementById('txtmainreli').removeAttribute('disabled','true'); document.getElementById('optwni').removeAttribute('disabled','true'); document.getElementById('optwna').removeAttribute('disabled','true'); document.getElementById('optmainstat').removeAttribute('disabled','true'); document.getElementById('txtmainprof').removeAttribute('disabled','true'); document.getElementById('optmaineduc').removeAttribute('disabled','true'); document.getElementById('txtmainphne').removeAttribute('disabled','true'); document.getElementById('txtmainmail').removeAttribute('disabled','true'); document.getElementById('txtmainprnt').removeAttribute('disabled','true'); document.getElementById('opttn').focus(); }">
              </div>
              <div class="form-group full">
                <label class="form-label">Nama Pasien</label>
                <div class="checkbox-group">
                  <input type="checkbox" name="opttn" id="opttn" value="true" disabled onclick="if (checked == true) { document.getElementById('optny').checked = false; document.getElementById('optnn').checked = false; document.getElementById('optan').checked = false; document.getElementById('hidmaintitl').value = 'Tn.'; document.getElementById('txtmainname').focus(); }">
                  <label for="opttn">Tn.</label>
                  <input type="checkbox" name="optny" id="optny" value="true" disabled onclick="if (checked == true) { document.getElementById('opttn').checked = false; document.getElementById('optnn').checked = false; document.getElementById('optan').checked = false; document.getElementById('hidmaintitl').value = 'Ny.'; document.getElementById('txtmainname').focus(); }">
                  <label for="optny">Ny.</label>
                  <input type="checkbox" name="optnn" id="optnn" value="true" disabled onclick="if (checked == true) { document.getElementById('opttn').checked = false; document.getElementById('optny').checked = false; document.getElementById('optan').checked = false; document.getElementById('hidmaintitl').value = 'Nn.'; document.getElementById('txtmainname').focus(); }">
                  <label for="optnn">Nn.</label>
                  <input type="checkbox" name="optan" id="optan" value="true" disabled onclick="if (checked == true) { document.getElementById('opttn').checked = false; document.getElementById('optny').checked = false; document.getElementById('optnn').checked = false; document.getElementById('hidmaintitl').value = 'An.'; document.getElementById('txtmainname').focus(); }">
                  <label for="optan">An.</label>
                  <input name="hidmaintitl" id="hidmaintitl" type="hidden">
                </div>
                <input type="text" name="txtmainname" id="txtmainname" class="form-control" maxlength="50" style="margin-top:6px;" disabled
                  oninput="this.value = this.value.toUpperCase()" autocomplete="off"
                  onkeyup="if (event.keyCode == 222) { alert('Single Quote Error'); this.value = ''; this.focus(); }"
                  onkeydown="if (event.keyCode == 13 && value.length > 0) document.getElementById('optmale').focus()">
              </div>
              <div class="form-group">
                <label class="form-label">Jenis Kelamin</label>
                <div class="checkbox-group">
                  <input type="checkbox" name="optmale" id="optmale" value="true" disabled onclick="if (checked == true) { document.getElementById('optfemale').checked = false; document.getElementById('hidmaingend').value = 'M'; }">
                  <label for="optmale">Laki-Laki</label>
                  <input type="checkbox" name="optfemale" id="optfemale" value="true" disabled onclick="if (checked == true) { document.getElementById('optmale').checked = false; document.getElementById('hidmaingend').value = 'F'; }">
                  <label for="optfemale">Perempuan</label>
                  <input name="hidmaingend" id="hidmaingend" type="hidden">
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Tanggal Lahir <span style="font-size:11px;color:#94a3b8;font-weight:normal;">(Bulan/Tanggal/Tahun)</span></label>
                <input type="date" name="tglmainbirt" id="tglmainbirt" class="form-control" disabled>
              </div>
              <div class="form-group">
                <label class="form-label">Golongan Darah</label>
                <div class="checkbox-group">
                  <input type="checkbox" name="optblooda" id="optblooda" value="true" disabled onclick="if (checked == true) { document.getElementById('optbloodb').checked = false; document.getElementById('optbloodab').checked = false; document.getElementById('optbloodo').checked = false; document.getElementById('optbloodx').checked = false; document.getElementById('hidmainblod').value = 'A'; }">
                  <label for="optblooda">[A]</label>
                  <input type="checkbox" name="optbloodb" id="optbloodb" value="true" disabled onclick="if (checked == true) { document.getElementById('optblooda').checked = false; document.getElementById('optbloodab').checked = false; document.getElementById('optbloodo').checked = false; document.getElementById('optbloodx').checked = false; document.getElementById('hidmainblod').value = 'B'; }">
                  <label for="optbloodb">[B]</label>
                  <input type="checkbox" name="optbloodab" id="optbloodab" value="true" disabled onclick="if (checked == true) { document.getElementById('optblooda').checked = false; document.getElementById('optbloodb').checked = false; document.getElementById('optbloodo').checked = false; document.getElementById('optbloodx').checked = false; document.getElementById('hidmainblod').value = 'AB'; }">
                  <label for="optbloodab">[AB]</label>
                  <input type="checkbox" name="optbloodo" id="optbloodo" value="true" disabled onclick="if (checked == true) { document.getElementById('optblooda').checked = false; document.getElementById('optbloodb').checked = false; document.getElementById('optbloodab').checked = false; document.getElementById('optbloodx').checked = false; document.getElementById('hidmainblod').value = 'O'; }">
                  <label for="optbloodo">[O]</label>
                  <input type="checkbox" name="optbloodx" id="optbloodx" value="true" disabled onclick="if (checked == true) { document.getElementById('optblooda').checked = false; document.getElementById('optbloodb').checked = false; document.getElementById('optbloodab').checked = false; document.getElementById('optbloodo').checked = false; document.getElementById('hidmainblod').value = 'X'; }">
                  <label for="optbloodx">[Tidak Tahu]</label>
                  <input name="hidmainblod" id="hidmainblod" type="hidden">
                </div>
              </div>
            </div>
          </div>
          <div class="card-modern">
            <div class="card-title">&#x1F4CD; Alamat &amp; Data Diri</div>
            <div class="form-grid">
              <div class="form-group full">
                <label class="form-label" for="txtmainaddr">Alamat</label>
                <textarea name="txtmainaddr" id="txtmainaddr" class="form-control" maxlength="100" disabled
                  oninput="this.value = this.value.toUpperCase()"
                  onkeyup="if (event.keyCode == 222) { alert('Single Quote Error'); this.value = ''; this.focus(); }"
                  onkeydown="if (event.keyCode == 13 && value.length > 0) document.getElementById('txtmainward').focus()"></textarea>
              </div>
              <div class="form-group">
                <label class="form-label" for="txtmainward">Kelurahan</label>
                <input type="text" name="txtmainward" id="txtmainward" class="form-control" maxlength="50" disabled oninput="this.value = this.value.toUpperCase()" onkeydown="if (event.keyCode == 13 && value.length > 0) document.getElementById('txtmaindist').focus()">
              </div>
              <div class="form-group">
                <label class="form-label" for="txtmaindist">Kecamatan</label>
                <input type="text" name="txtmaindist" id="txtmaindist" class="form-control" maxlength="50" disabled oninput="this.value = this.value.toUpperCase()" onkeydown="if (event.keyCode == 13 && value.length > 0) document.getElementById('txtmaincity').focus()">
              </div>
              <div class="form-group">
                <label class="form-label" for="txtmaincity">Kota</label>
                <input type="text" name="txtmaincity" id="txtmaincity" class="form-control" maxlength="50" disabled oninput="this.value = this.value.toUpperCase()" onkeydown="if (event.keyCode == 13 && value.length > 0) document.getElementById('txtmainprov').focus()">
              </div>
              <div class="form-group">
                <label class="form-label" for="txtmainprov">Provinsi</label>
                <input type="text" name="txtmainprov" id="txtmainprov" class="form-control" maxlength="50" disabled oninput="this.value = this.value.toUpperCase()" onkeydown="if (event.keyCode == 13 && value.length > 0) document.getElementById('txtmainreli').focus()">
              </div>
              <div class="form-group">
                <label class="form-label" for="txtmainreli">Agama</label>
                <input type="text" name="txtmainreli" id="txtmainreli" class="form-control" maxlength="50" disabled oninput="this.value = this.value.toUpperCase()" onkeydown="if (event.keyCode == 13 && value.length > 0) { document.getElementById('optwni').checked = true; document.getElementById('hidmainctzn').value = 'WNI'; document.getElementById('optmainstat').focus(); }">
              </div>
              <div class="form-group">
                <label class="form-label">Warga Negara</label>
                <div class="checkbox-group">
                  <input type="checkbox" name="optwni" id="optwni" value="true" disabled onclick="if (checked == true) { document.getElementById('optwna').checked = false; document.getElementById('hidmainctzn').value = 'WNI'; }">
                  <label for="optwni">[Indonesia]</label>
                  <input type="checkbox" name="optwna" id="optwna" value="true" disabled onclick="if (checked == true) { document.getElementById('optwni').checked = false; document.getElementById('hidmainctzn').value = 'WNA'; }">
                  <label for="optwna">[Asing]</label>
                  <input name="hidmainctzn" id="hidmainctzn" type="hidden">
                </div>
              </div>
              <div class="form-group">
                <label class="form-label" for="optmainstat">Status</label>
                <select name="optmainstat" id="optmainstat" class="form-control" disabled onchange="document.getElementById('txtmainprof').focus();">
                  <option value="Lajang">Belum Menikah</option>
                  <option value="Menikah">Sudah Menikah</option>
                  <option value="Janda">Janda</option>
                  <option value="Duda">Duda</option>
                  <option value="Anak">Anak Anak</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label" for="txtmainprof">Profesi</label>
                <input type="text" name="txtmainprof" id="txtmainprof" class="form-control" maxlength="50" disabled oninput="this.value = this.value.toUpperCase()" onkeydown="if (event.keyCode == 13 && value.length > 0) document.getElementById('optmaineduc').focus()">
              </div>
              <div class="form-group">
                <label class="form-label" for="optmaineduc">Pendidikan</label>
                <select name="optmaineduc" id="optmaineduc" class="form-control" disabled onchange="document.getElementById('txtmainphne').focus();">
                  <option value="SD">SD</option>
                  <option value="SMP">SMP</option>
                  <option value="SMA">SMA/SMK</option>
                  <option value="D3">Diploma 3</option>
                  <option value="S1">Strata 1</option>
                  <option value="S2">Strata 2</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label" for="txtmainphne">Mobile</label>
                <input type="text" name="txtmainphne" id="txtmainphne" class="form-control" maxlength="18" disabled onkeydown="if (event.keyCode == 13 && value.length > 0) document.getElementById('txtmainmail').focus()">
              </div>
              <div class="form-group">
                <label class="form-label" for="txtmainmail">E-Mail</label>
                <input type="text" name="txtmainmail" id="txtmainmail" class="form-control" placeholder="user@domain.com" maxlength="50" disabled onkeydown="if (event.keyCode == 13 && value.length > 0) document.getElementById('txtmainprnt').focus()">
              </div>
              <div class="form-group full">
                <label class="form-label" for="txtmainprnt">Nama Ibu Kandung</label>
                <input type="text" name="txtmainprnt" id="txtmainprnt" class="form-control" maxlength="100" disabled oninput="this.value = this.value.toUpperCase()"
                  onkeydown="if (event.keyCode == 13 && value.length > 0) { if (document.getElementById('txtmainpidn').value.length == 0) { swal({ title: 'NIK Kosong', text: 'Anda belum mengisi NIK, silah periksa lagi', icon: 'warning', }); document.getElementById('txtmainpidn').value = ''; document.getElementById('txtmainpidn').focus(); } else if (document.getElementById('hidmaintitl').value.length == 0) { swal({ title: 'Title Belum di Pilih', text: 'Anda belum memilih Inisial, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('txtmainname').value.length == 0) { swal({ title: 'Nama Pasien Kosong', text: 'Anda belum mengisi Nama Pasien, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('hidmaingend').value.length == 0) { swal({ title: 'Gender Kosong', text: 'Anda belum memilih Gender / Jenis Kelamin Pasien, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('hidmainblod').value.length == 0) { swal({ title: 'Golongan Darah Kosong', text: 'Anda belum memilih Golongan Darah, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('txtmainaddr').value.length == 0) { swal({ title: 'Alamat Kosong', text: 'Anda belum mengisi Alamat Pasien, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('txtmainward').value.length == 0) { swal({ title: 'Kelurahan Kosong', text: 'Anda belum mengisi Nama Kelurahan, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('txtmaindist').value.length == 0) { swal({ title: 'Kecamatan Kosong', text: 'Anda belum mengisi Nama Kecamatan, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('txtmaincity').value.length == 0) { swal({ title: 'Kota Kosong', text: 'Anda belum mengisi Nama Kota, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('txtmainprov').value.length == 0) { swal({ title: 'Provinsi Kosong', text: 'Anda belum mengisi Nama Provinsi, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('txtmainreli').value.length == 0) { swal({ title: 'Agama Kosong', text: 'Anda belum mengisi Agama, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('hidmainctzn').value.length == 0) { swal({ title: 'Data Kewarga Negaraan Kosong', text: 'Anda belum memilih Data Kewarga Negaraan, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('txtmainphne').value.length == 0) { swal({ title: 'Nomor Telpon Kosong', text: 'Anda belum mengisi Nomor Telpon, silah periksa lagi', icon: 'warning', }); } else { document.frmtrxapati.submit(); } }">
              </div>
            </div>
          </div>
          <div class="card-modern" style="text-align:right;">
            <button type="button" class="btn-modern btn-save" onclick="javascript: if (document.getElementById('txtmainpidn').value.length == 0) { swal({ title: 'NIK Kosong', text: 'Anda belum mengisi NIK, silah periksa lagi', icon: 'warning', }); document.getElementById('txtmainpidn').value = ''; document.getElementById('txtmainpidn').focus(); } else if (document.getElementById('hidmaintitl').value.length == 0) { swal({ title: 'Title Belum di Pilih', text: 'Anda belum memilih Inisial, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('txtmainname').value.length == 0) { swal({ title: 'Nama Pasien Kosong', text: 'Anda belum mengisi Nama Pasien, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('hidmaingend').value.length == 0) { swal({ title: 'Gender Kosong', text: 'Anda belum memilih Gender / Jenis Kelamin Pasien, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('hidmainblod').value.length == 0) { swal({ title: 'Golongan Darah Kosong', text: 'Anda belum memilih Golongan Darah, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('txtmainaddr').value.length == 0) { swal({ title: 'Alamat Kosong', text: 'Anda belum mengisi Alamat Pasien, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('txtmainward').value.length == 0) { swal({ title: 'Kelurahan Kosong', text: 'Anda belum mengisi Nama Kelurahan, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('txtmaindist').value.length == 0) { swal({ title: 'Kecamatan Kosong', text: 'Anda belum mengisi Nama Kecamatan, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('txtmaincity').value.length == 0) { swal({ title: 'Kota Kosong', text: 'Anda belum mengisi Nama Kota, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('txtmainprov').value.length == 0) { swal({ title: 'Provinsi Kosong', text: 'Anda belum mengisi Nama Provinsi, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('txtmainreli').value.length == 0) { swal({ title: 'Agama Kosong', text: 'Anda belum mengisi Agama, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('hidmainctzn').value.length == 0) { swal({ title: 'Data Kewarga Negaraan Kosong', text: 'Anda belum memilih Data Kewarga Negaraan, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('txtmainphne').value.length == 0) { swal({ title: 'Nomor Telpon Kosong', text: 'Anda belum mengisi Nomor Telpon, silah periksa lagi', icon: 'warning', }); } else if (document.getElementById('txtmainprnt').value.length == 0) { swal({ title: 'Nama Ibu Kandung', text: 'Anda belum mengisi Nama Ibu Kandung Pasien, silah periksa lagi', icon: 'warning', }); } else { document.frmtrxapati.submit(); }">
              &#x2714; Submit
            </button>
          </div>
        </div>
      </form>
    </div><!-- div content -->
    <div class="footerdate">
      <span class="labelTime Time"><b>Date :</b> <?php $tgl = date('d-m-Y'); echo $tgl; ?></span>
    </div>
    <div class="footertime">
      <span class="labelTime Time" id="timestamp"></span>
    </div>
  </div><!-- div main -->
</div><!-- div layout -->
<script src="js/TRXAPATI01.js?v=20260108-1"></script>
<script src="js/ui.js"></script>
</body>
</html>
<?php
} else {
  header("Location: " . "signin.php");
}
?>