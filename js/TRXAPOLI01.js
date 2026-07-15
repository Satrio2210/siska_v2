// periksa akses
var aksesku;
function periksaakses(fieldid) {
  aksesku = buatajaxakses();
  var url = "TRXAPOLI01X-AKSES.php";
  aksesku.onreadystatechange = stateChangedAkses;
  var params = "q=" + fieldid;
  //alert('Parameter adalah '+fieldid);
  aksesku.open("POST", url, true);
  aksesku.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  aksesku.setRequestHeader("Content-length", params.length);
  aksesku.setRequestHeader("Connection", "close");
  aksesku.send(params);

}

function buatajaxakses() {
  if (window.XMLHttpRequest) {
    return new XMLHttpRequest();
  }
  if (window.ActiveXObject) {
    return new ActiveXObject("Microsoft.XMLHTTP");
  }
  return null;
}

function stateChangedAkses() {
  var data;
  if (aksesku.readyState == 4) {

    xdata = aksesku.responseText;
    data = xdata.trim();

    // Halaman daftar pasien (TRXAPOLI00): tidak ada form pemeriksaan,
    // langsung tampilkan daftar pasien tanpa mencoba disable field yang tidak ada.
    if (document.getElementById('tblscreen') && !document.getElementById('infoPasien')) {
      ambilscreen('');
      return;
    }

    // Auto-load pasien dari URL (?exam=...&pati=...): jangan disable field,
    // karena viewcode() akan mengisi & meng-enable field. Menghindari race
    // antara response periksaakses (disable) dan viewcode (enable).
    if (new URLSearchParams(window.location.search).get('exam')) {
      return;
    }

    if (data.length > 1) {

      document.getElementById('txtexamhght').setAttribute('disabled', 'true');
      document.getElementById('txtexamwght').setAttribute('disabled', 'true');
      document.getElementById('txtexamwaist').setAttribute('disabled', 'true');
      document.getElementById('txtexambmi').setAttribute('disabled', 'true');
      document.getElementById('txtexamblod').setAttribute('disabled', 'true');
      document.getElementById('txtexamtemp').setAttribute('disabled', 'true');
      document.getElementById('txtexamrr').setAttribute('disabled', 'true');
      document.getElementById('txtexamhr').setAttribute('disabled', 'true');
      document.getElementById('txtexamcomp').setAttribute('disabled', 'true');

      document.getElementById('optmedialle-no').setAttribute('disabled', 'true');
      document.getElementById('optmedialle-yes').setAttribute('disabled', 'true');

      document.getElementById('optfoodalle-no').setAttribute('disabled', 'true');
      document.getElementById('optfoodalle-yes').setAttribute('disabled', 'true');

      document.getElementById('optchrodsse-no').setAttribute('disabled', 'true');
      document.getElementById('optchrodsse-yes').setAttribute('disabled', 'true');

      document.getElementById('optothrdsse-no').setAttribute('disabled', 'true');
      document.getElementById('optothrdsse-yes').setAttribute('disabled', 'true');

      document.getElementById('optpaticare-no').setAttribute('disabled', 'true');
      document.getElementById('optpaticare-yes').setAttribute('disabled', 'true');

      document.getElementById('optpatisurge-no').setAttribute('disabled', 'true');
      document.getElementById('optpatisurge-yes').setAttribute('disabled', 'true');

      document.getElementById('optpatismoke-no').setAttribute('disabled', 'true');
      document.getElementById('optpatismoke-yes').setAttribute('disabled', 'true');


      document.getElementById('txtexamanam').setAttribute('disabled', 'true');
      document.getElementById('txtexambody').setAttribute('disabled', 'true');
      document.getElementById('txtlistdiag').setAttribute('disabled', 'true');
      document.getElementById('txtexamdiag').setAttribute('disabled', 'true');
      document.getElementById('txtexamprsc').setAttribute('disabled', 'true');

      if (document.getElementById('tblscreen')) {
        ambilscreen(document.getElementById('hidexamdoct').value);
      }
    }
    else {
      document.getElementById('txtexamcode').setAttribute('disabled', 'true');

      document.getElementById('txtexamhght').setAttribute('disabled', 'true');
      document.getElementById('txtexamwght').setAttribute('disabled', 'true');
      document.getElementById('txtexamwaist').setAttribute('disabled', 'true');
      document.getElementById('txtexambmi').setAttribute('disabled', 'true');
      document.getElementById('txtexamblod').setAttribute('disabled', 'true');
      document.getElementById('txtexamtemp').setAttribute('disabled', 'true');
      document.getElementById('txtexamrr').setAttribute('disabled', 'true');
      document.getElementById('txtexamhr').setAttribute('disabled', 'true');
      document.getElementById('txtexamcomp').setAttribute('disabled', 'true');

      document.getElementById('optmedialle-no').setAttribute('disabled', 'false');
      document.getElementById('optmedialle-yes').setAttribute('disabled', 'false');

      document.getElementById('optfoodalle-no').setAttribute('disabled', 'true');
      document.getElementById('optfoodalle-yes').setAttribute('disabled', 'true');

      document.getElementById('optchrodsse-no').setAttribute('disabled', 'true');
      document.getElementById('optchrodsse-yes').setAttribute('disabled', 'true');

      document.getElementById('optothrdsse-no').setAttribute('disabled', 'true');
      document.getElementById('optothrdsse-yes').setAttribute('disabled', 'true');

      document.getElementById('optpaticare-no').setAttribute('disabled', 'true');
      document.getElementById('optpaticare-yes').setAttribute('disabled', 'true');

      document.getElementById('optpatisurge-no').setAttribute('disabled', 'true');
      document.getElementById('optpatisurge-yes').setAttribute('disabled', 'true');

      document.getElementById('optpatismoke-no').setAttribute('disabled', 'true');
      document.getElementById('optpatismoke-yes').setAttribute('disabled', 'true');

      document.getElementById('optmedialle-no').setAttribute('disabled', 'true');
      document.getElementById('optmedialle-yes').setAttribute('disabled', 'true');

      document.getElementById('txtexamanam').setAttribute('disabled', 'true');
      document.getElementById('txtexambody').setAttribute('disabled', 'true');
      document.getElementById('txtlistdiag').setAttribute('disabled', 'true');
      document.getElementById('txtexamdiag').setAttribute('disabled', 'true');
      document.getElementById('txtexamprsc').setAttribute('disabled', 'true');


    }
  }
}
// end periksa akses  

// inexamcode,inexamdoct,inexamhght,inexamwght,inexamblod,inexamtemp,inmedialle,infoodalle,inchrodsse,inothrdsse,inpaticare,inpatisurge,inpatismoke,inexamanam,inexambody,inexamdiag,inexamprsc
var ajaxinput;
function input(examcode, examdoct, examhght, examwght, examwaist, exambmi, examblod, examtemp, examrr, examhr, examcomp, medialle, foodalle, chrodsse, othrdsse, paticare, patisurge, patismoke, examanam, exambody, examdiag, examprsc) {
  ajaxinput = buatajaxinput();
  var url = "TRXAPOLI01E.php";
  ajaxinput.onreadystatechange = stateChangedInput;
  var params = "q=" + examcode + "|"
    + examdoct + "|"
    + examhght + "|"
    + examwght + "|"
    + examwaist + "|"
    + exambmi + "|"
    + examblod + "|"
    + examtemp + "|"
    + examrr + "|"
    + examhr + "|"
    + examcomp + "|"
    + medialle + "|"
    + foodalle + "|"
    + chrodsse + "|"
    + othrdsse + "|"
    + paticare + "|"
    + patisurge + "|"
    + patismoke + "|"
    + examanam + "|"
    + exambody + "|"
    + examdiag + "|"
    + examprsc;
  //alert(params);
  ajaxinput.open("POST", url, true);
  ajaxinput.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  //ajaxinput.setRequestHeader("Content-length", params.length);
  //ajaxinput.setRequestHeader("Connection", "close");
  ajaxinput.send(params);
}

function buatajaxinput() {
  if (window.XMLHttpRequest) {
    return new XMLHttpRequest();
  }
  if (window.ActiveXObject) {
    return new ActiveXObject("Microsoft.XMLHTTP");
  }
  return null;
}

function stateChangedInput() {
  if (ajaxinput.readyState == 4) {
    document.getElementById('txtregidate').value = '';
    document.getElementById('txtexamcode').value = '';

    var examdoct = document.getElementById('hidexamdoct').value;

    document.getElementById('txtpaticode').value = '';
    document.getElementById('txtmainname').value = '';
    document.getElementById('txtmaingend').value = '';
    document.getElementById('txtmainage').value = '';
    document.getElementById('txtbirtdate').value = '';
    document.getElementById('txtmainaddr').value = '';
    document.getElementById('txtregipaym').value = '';

    document.getElementById('txtexamhght').value = '';
    document.getElementById('txtexamhght').setAttribute('disabled', 'true');

    document.getElementById('txtexamwght').value = '';
    document.getElementById('txtexamwght').setAttribute('disabled', 'true');

    document.getElementById('txtexamwaist').value = '';
    document.getElementById('txtexamwaist').setAttribute('disabled', 'true');

    document.getElementById('txtexambmi').value = '';
    document.getElementById('txtexambmi').setAttribute('disabled', 'true');

    document.getElementById('txtexamblod').value = '';
    document.getElementById('txtexamblod').setAttribute('disabled', 'true');

    document.getElementById('txtexamtemp').value = '';
    document.getElementById('txtexamtemp').setAttribute('disabled', 'true');

    document.getElementById('txtexamrr').value = '';
    document.getElementById('txtexamrr').setAttribute('disabled', 'true');

    document.getElementById('txtexamhr').value = '';
    document.getElementById('txtexamhr').setAttribute('disabled', 'true');

    document.getElementById('txtexamcomp').value = '';
    document.getElementById('txtexamcomp').setAttribute('disabled', 'true');

    document.getElementById('optmedialle-no').checked = true;
    document.getElementById('optmedialle-yes').checked = false;
    document.getElementById('hidmedialle').value = '';
    document.getElementById('optmedialle-no').setAttribute('disabled', 'false');
    document.getElementById('optmedialle-yes').setAttribute('disabled', 'true');

    document.getElementById('optfoodalle-no').checked = true;
    document.getElementById('optfoodalle-yes').checked = false;
    document.getElementById('hidfoodalle').value = 'N';
    document.getElementById('optfoodalle-no').setAttribute('disabled', 'false');
    document.getElementById('optfoodalle-yes').setAttribute('disabled', 'true');

    document.getElementById('optchrodsse-no').checked = true;
    document.getElementById('optchrodsse-yes').checked = false;
    document.getElementById('hidchrodsse').value = 'N';
    document.getElementById('optchrodsse-no').setAttribute('disabled', 'true');
    document.getElementById('optchrodsse-yes').setAttribute('disabled', 'true');

    document.getElementById('optothrdsse-no').checked = true;
    document.getElementById('optothrdsse-yes').checked = false;
    document.getElementById('hidothrdsse').value = 'N';
    document.getElementById('optothrdsse-no').setAttribute('disabled', 'true');
    document.getElementById('optothrdsse-yes').setAttribute('disabled', 'true');

    document.getElementById('optpaticare-no').checked = true;
    document.getElementById('optpaticare-yes').checked = false;
    document.getElementById('hidpaticare').value = 'N';
    document.getElementById('optpaticare-no').setAttribute('disabled', 'true');
    document.getElementById('optpaticare-yes').setAttribute('disabled', 'true');

    document.getElementById('optpatisurge-no').checked = true;
    document.getElementById('optpatisurge-yes').checked = false;
    document.getElementById('hidpatisurge').value = 'N';
    document.getElementById('optpatisurge-no').setAttribute('disabled', 'true');
    document.getElementById('optpatisurge-yes').setAttribute('disabled', 'true');

    document.getElementById('optpatismoke-no').checked = true;
    document.getElementById('optpatismoke-yes').checked = false;
    document.getElementById('hidpatismoke').value = 'N';
    document.getElementById('optpatismoke-no').setAttribute('disabled', 'true');
    document.getElementById('optpatismoke-yes').setAttribute('disabled', 'true');

    document.getElementById('txtexamanam').value = '';
    document.getElementById('txtexamanam').setAttribute('disabled', 'true');

    document.getElementById('txtexambody').value = '';
    document.getElementById('txtexambody').setAttribute('disabled', 'true');

    document.getElementById('txtexamdiag').value = '';
    document.getElementById('txtexamdiag').setAttribute('disabled', 'true');

    document.getElementById('txtexamprsc').value = '';
    document.getElementById('txtexamprsc').setAttribute('disabled', 'true');

    ambilscreen(examdoct);
    ambilscreendiagnosa('');

    // Reset and disable new card controls
    if (document.getElementById("jenis_surat_baru")) {
      document.getElementById("jenis_surat_baru").value = "";
      document.getElementById("jenis_surat_baru").setAttribute('disabled', 'true');
      document.getElementById("btn-cetak-surat").setAttribute('disabled', 'true');

      document.getElementById("radarujuk_rs").checked = false;
      document.getElementById("radarujuk_lab").checked = false;
      document.getElementById("radarujuk_rs").setAttribute('disabled', 'true');
      document.getElementById("radarujuk_lab").setAttribute('disabled', 'true');
      document.getElementById("txtrujuknote").value = "";
      document.getElementById("txtrujuknote").setAttribute('disabled', 'true');
      document.getElementById("btn-rujuk").setAttribute('disabled', 'true');

      document.getElementById("txtmedicode").value = "";
      document.getElementById("txtmedicode").setAttribute('disabled', 'true');

      document.getElementById("txttretquty").value = "1";
      document.getElementById("txttretquty").setAttribute('disabled', 'true');

      document.getElementById("btn-input-tindakan").setAttribute('disabled', 'true');

      document.getElementById("hidmedicode").value = "";
      document.getElementById("hidmedirate").value = "";
      document.getElementById("hidmediroom").value = "";
      document.getElementById("hidregipaym").value = "";

      document.getElementById("tbltindakan").innerHTML = "";
      document.getElementById("tbltindakan").style.display = "none";
      document.getElementById("tbltreatment").innerHTML = "";
    }
    // document.getElementById('txtexamcode').focus();
    if (document.getElementById("daftarPasien")) {
      document.getElementById("daftarPasien").scrollIntoView({
        behavior: "smooth",
        block: "start"
      });
    }

    // Notifikasi sukses lalu kembali ke daftar pasien (TRXAPOLI00)
    swal({
      title: "Berhasil!",
      text: "Data pemeriksaan pasien berhasil disimpan.",
      icon: "success",
      timer: 1500,
      buttons: false
    });
    setTimeout(function () {
      window.location.href = "TRXAPOLI00.php";
    }, 1600);
  }
}
// Selesai Input Data Wall ke Tabel tblitype

// Tampilkan Data Order   yang telah di pilih pada tabel untuk di ubah pada form 
var ajaxview
function viewcode(regicode, paticode) {
  try {
    ajaxview = buatajaxview();
    var url = "TRXAPOLI01C.php";
    ajaxview.onreadystatechange = stateChangedView;
    var params = "q=" + regicode + "|" + paticode;
    //alert(params);
    ajaxview.open("POST", url, true);
    ajaxview.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajaxview.setRequestHeader("Content-length", params.length);
    ajaxview.setRequestHeader("Connection", "close");
    ajaxview.send(params);

  }
  catch (err) { alert(err.message); }
}

function buatajaxview() {
  if (window.XMLHttpRequest) {
    return new XMLHttpRequest();
  }
  if (window.ActiveXObject) {
    return new ActiveXObject("Microsoft.XMLHTTP");
  }
  return null;
}

function stateChangedView() {
  var data;
  if (ajaxview.readyState == 4) {
    data = ajaxview.responseText;
    if (data.length > 1) {
      //alert(data);

      var res = data.split("|").map(function (item) {
        return item.trim();
      });
      document.getElementById("txtregidate").value = res[1];

      document.getElementById("txtexamcode").value = res[2];

      document.getElementById("txtpaticode").value = res[3];

      if (document.getElementById('tblrekammedis')) {
        ambilrekammedis(res[3]);
      }

      ambilscreendiagnosa(res[2]);

      document.getElementById("txtmainname").value = res[4];

      document.getElementById("txtmaingend").value = res[8];

      document.getElementById("txtmainage").value = res[5];

      document.getElementById("txtbirtdate").value = res[6];

      document.getElementById("txtmainaddr").value = res[7];


      document.getElementById("txtregipaym").value = res[9];

      document.getElementById("txtexamhght").removeAttribute('disabled');
      document.getElementById("txtexamhght").value = res[10];

      document.getElementById("txtexamwght").removeAttribute('disabled');
      document.getElementById("txtexamwght").value = res[11];

      document.getElementById("txtexamwaist").removeAttribute('disabled');
      document.getElementById("txtexamwaist").value = res[12];

      document.getElementById("txtexambmi").removeAttribute('disabled');
      document.getElementById("txtexambmi").value = res[13];

      document.getElementById("txtexamblod").removeAttribute('disabled');
      document.getElementById("txtexamblod").value = res[14];

      document.getElementById("txtexamtemp").removeAttribute('disabled');
      document.getElementById("txtexamtemp").value = res[15];

      document.getElementById("txtexamrr").removeAttribute('disabled');
      document.getElementById("txtexamrr").value = res[16];

      document.getElementById("txtexamhr").removeAttribute('disabled');
      document.getElementById("txtexamhr").value = res[17];

      document.getElementById("txtexamcomp").removeAttribute('disabled');
      document.getElementById("txtexamcomp").value = res[18];

      document.getElementById('optmedialle-no').removeAttribute('disabled');
      document.getElementById('optmedialle-yes').removeAttribute('disabled');
      document.getElementById('hidmedialle').value = res[19]

      if (res[19] == 'Y') {
        document.getElementById('optmedialle-no').checked = false;
        document.getElementById('optmedialle-yes').checked = true;
      }
      else if (res[19] == 'N') {
        document.getElementById('optmedialle-no').checked = true;
        document.getElementById('optmedialle-yes').checked = false;
      }
      else {
        document.getElementById('optmedialle-no').checked = false;
        document.getElementById('optmedialle-yes').checked = false;
      }

      document.getElementById('optfoodalle-no').removeAttribute('disabled');
      document.getElementById('optfoodalle-yes').removeAttribute('disabled');
      document.getElementById('hidfoodalle').value = res[20]

      if (res[20] == 'Y') {
        document.getElementById('optfoodalle-no').checked = false;
        document.getElementById('optfoodalle-yes').checked = true;
      }
      else if (res[20] == 'N') {
        document.getElementById('optfoodalle-no').checked = true;
        document.getElementById('optfoodalle-yes').checked = false;
      }
      else {
        document.getElementById('optfoodalle-no').checked = false;
        document.getElementById('optfoodalle-yes').checked = false;
      }

      document.getElementById('optchrodsse-no').removeAttribute('disabled');
      document.getElementById('optchrodsse-yes').removeAttribute('disabled');
      document.getElementById('hidchrodsse').value = res[21]

      if (res[21] == 'Y') {
        document.getElementById('optchrodsse-no').checked = false;
        document.getElementById('optchrodsse-yes').checked = true;
      }
      else if (res[21] == 'N') {
        document.getElementById('optchrodsse-no').checked = true;
        document.getElementById('optchrodsse-yes').checked = false;
      }
      else {
        document.getElementById('optchrodsse-no').checked = false;
        document.getElementById('optchrodsse-yes').checked = false;
      }

      document.getElementById('optothrdsse-no').removeAttribute('disabled');
      document.getElementById('optothrdsse-yes').removeAttribute('disabled');
      document.getElementById('hidothrdsse').value = res[22]

      if (res[22] == 'Y') {
        document.getElementById('optothrdsse-no').checked = false;
        document.getElementById('optothrdsse-yes').checked = true;
      }
      else if (res[22] == 'N') {
        document.getElementById('optothrdsse-no').checked = true;
        document.getElementById('optothrdsse-yes').checked = false;
      }
      else {
        document.getElementById('optothrdsse-no').checked = false;
        document.getElementById('optothrdsse-yes').checked = false;
      }


      document.getElementById('optpaticare-no').removeAttribute('disabled');
      document.getElementById('optpaticare-yes').removeAttribute('disabled');
      document.getElementById('hidpaticare').value = res[23]

      if (res[23] == 'Y') {
        document.getElementById('optpaticare-no').checked = false;
        document.getElementById('optpaticare-yes').checked = true;
      }
      else if (res[23] == 'N') {
        document.getElementById('optpaticare-no').checked = true;
        document.getElementById('optpaticare-yes').checked = false;
      }
      else {
        document.getElementById('optpaticare-no').checked = false;
        document.getElementById('optpaticare-yes').checked = false;
      }

      document.getElementById('optpatisurge-no').removeAttribute('disabled');
      document.getElementById('optpatisurge-yes').removeAttribute('disabled');
      document.getElementById('hidpatisurge').value = res[24]

      if (res[24] == 'Y') {
        document.getElementById('optpatisurge-no').checked = false;
        document.getElementById('optpatisurge-yes').checked = true;
      }
      else if (res[24] == 'N') {
        document.getElementById('optpatisurge-no').checked = true;
        document.getElementById('optpatisurge-yes').checked = false;
      }
      else {
        document.getElementById('optpatisurge-no').checked = false;
        document.getElementById('optpatisurge-yes').checked = false;
      }

      document.getElementById('optpatismoke-no').removeAttribute('disabled');
      document.getElementById('optpatismoke-yes').removeAttribute('disabled');
      document.getElementById('hidpatismoke').value = res[25]

      if (res[25] == 'Y') {
        document.getElementById('optpatismoke-no').checked = false;
        document.getElementById('optpatismoke-yes').checked = true;
      }
      else if (res[25] == 'N') {
        document.getElementById('optpatismoke-no').checked = true;
        document.getElementById('optpatismoke-yes').checked = false;
      }
      else {
        document.getElementById('optpatismoke-no').checked = false;
        document.getElementById('optpatismoke-yes').checked = false;

      }


      document.getElementById("txtexamanam").removeAttribute('disabled');
      document.getElementById("txtexamanam").value = res[26];

      document.getElementById("txtexambody").removeAttribute('disabled');
      document.getElementById("txtexambody").value = res[27];

      document.getElementById("txtlistdiag").removeAttribute('disabled');

      document.getElementById("txtexamdiag").removeAttribute('disabled');
      document.getElementById("txtexamdiag").value = res[28];

      document.getElementById("txtexamprsc").removeAttribute('disabled');
      document.getElementById("txtexamprsc").value = res[29];

      console.log(res);

      // Enable new card controls
      if (document.getElementById("jenis_surat_baru")) {
        // document.getElementById("jenis_surat_baru").removeAttribute('disabled');
        // document.getElementById("btn-cetak-surat").removeAttribute('disabled');
        document.getElementById("radarujuk_rs").removeAttribute('disabled');
        document.getElementById("radarujuk_lab").removeAttribute('disabled');
        document.getElementById("txtrujuknote").removeAttribute('disabled');
        document.getElementById("btn-rujuk").removeAttribute('disabled');
        document.getElementById("txtmedicode").removeAttribute('disabled');
        document.getElementById("txttretquty").removeAttribute('disabled');
        document.getElementById("btn-input-tindakan").removeAttribute('disabled');

        // Populate hidden fields for Tindakan room and payment code from AJAX response
        document.getElementById("hidmediroom").value = res[30];
        document.getElementById("hidregipaym").value = res[31];

        // Reset suggestion block
        document.getElementById("tbltindakan").innerHTML = "";
        document.getElementById("tbltindakan").style.display = "none";

        // Load patient treatment list (and auto-select default action)
        executeAmbilscreenTindakan(res[2]);
      }

      // document.getElementById("txtexamhght").focus();

      // var scrollTarget = document.getElementById("tblrekammedis") || document.getElementById("infoPasien");
      // if (scrollTarget) {
      //   scrollTarget.scrollIntoView({
      //     behavior: "smooth",
      //     block: "start"
      //   });
      // }

    }
    //}
  }
}

// Tampilkan data yang diinput dalam datable
var drz;
function ambilscreen(dokter) {
  if (!document.getElementById('tblscreen')) return;
  drz = buatajaxscreen();
  var url = "TRXAPOLI01V.php";
  drz.onreadystatechange = stateChangedscreen;
  var params = "q=" + dokter;
  drz.open("POST", url, true);
  //beberapa http header harus kita set kalau menggunakan POST
  drz.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  drz.setRequestHeader("Content-length", params.length);
  drz.setRequestHeader("Connection", "close");
  drz.send(params);
}

function buatajaxscreen() {
  if (window.XMLHttpRequest) {
    return new XMLHttpRequest();
  }
  if (window.ActiveXObject) {
    return new ActiveXObject("Microsoft.XMLHTTP");
  }
  return null;
}

function stateChangedscreen() {
  var datapost;
  if (drz.readyState == 4 && drz.status == 200) {
    datapost = drz.responseText;
    if (datapost.length > 0) {
      document.getElementById("tblscreen").innerHTML = datapost;
      document.getElementById("tblscreen").style.visibility = "";
    }
    else {
      document.getElementById("tblscreen").innerHTML = "";
      document.getElementById("tblscreen").style.visibility = "hidden";
    }
  }
}

// ambil Teks Anamnesa tabel trxaexam

var anamnesaku;
function ambilanamnesa(kata) {
  if (kata.length > 5) {
    document.getElementById("tblanamnesa").innerHTML = "";
    document.getElementById("tblanamnesa").style.visibility = "hidden";
  }
  else {
    anamnesaku = buatajaxanamnesa();
    //var url="WAREMAST05C-WARE.php";
    var url = "TRXAPOLI01C-ANAMNESA.php";
    anamnesaku.onreadystatechange = stateChangedexamanam;
    var params = "q=" + kata;
    anamnesaku.open("POST", url, true);
    anamnesaku.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    anamnesaku.setRequestHeader("Content-length", params.length);
    anamnesaku.setRequestHeader("Connection", "close");
    anamnesaku.send(params);
  }
}

function buatajaxanamnesa() {
  if (window.XMLHttpRequest) {
    return new XMLHttpRequest();
  }
  if (window.ActiveXObject) {
    return new ActiveXObject("Microsoft.XMLHTTP");
  }
  return null;
}

function stateChangedexamanam() {
  var data;
  if (anamnesaku.readyState == 4 && anamnesaku.status == 200) {
    data = anamnesaku.responseText;
    if (data.length > 3) {
      document.getElementById("tblanamnesa").innerHTML = data;
      document.getElementById("tblanamnesa").style.visibility = "";
    }
    else {
      document.getElementById("tblanamnesa").innerHTML = "";
      document.getElementById("tblanamnesa").style.visibility = "hidden";
    }
  }
}

function isiexamanam(outexamanam) {
  try {
    document.getElementById("txtexamanam").value = outexamanam;
    document.getElementById("txtexambody").focus();
    document.getElementById("tblanamnesa").style.visibility = "hidden";
    document.getElementById("tblanamnesa").innerHTML = "";

  }
  catch (err) { alert(err.message); }
}

// tabel Rekam Medis Pasien
var rekamku;

function ambilrekammedis(rekammedis) {
  if (!document.getElementById('tblrekammedis')) return;
  if (rekammedis.length > 10) {
    document.getElementById("tblrekammedis").style.visibility = "hidden";
  }
  else {
    rekamku = buatajaxrekammedis();
    var url = "TRXAPOLI01C-REKAMMEDIS.php";
    rekamku.onreadystatechange = stateChangedrekammedis;
    var params = "q=" + rekammedis;
    rekamku.open("POST", url, true);
    rekamku.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    rekamku.setRequestHeader("Content-length", params.length);
    rekamku.setRequestHeader("Connection", "close");
    rekamku.send(params);
  }
}

function buatajaxrekammedis() {
  if (window.XMLHttpRequest) {
    return new XMLHttpRequest();
  }
  if (window.ActiveXObject) {
    return new ActiveXObject("Microsoft.XMLHTTP");
  }
  return null;
}

function stateChangedrekammedis() {
  var data;
  if (rekamku.readyState == 4 && rekamku.status == 200) {
    data = rekamku.responseText;
    if (data.length > 3) {
      document.getElementById("tblrekammedis").innerHTML = data;
      document.getElementById("tblrekammedis").style.visibility = "";
    }
    else {
      document.getElementById("tblrekammedis").innerHTML = "";
      document.getElementById("tblrekammedis").style.visibility = "hidden";
    }
  }
}

// ambil list diagnosa

var diagku;
function ambildiagnosa(regicode, diagcode) {
  if (diagcode.length > 20) {
    document.getElementById("tbllistdiag").style.display = "none";
  }
  else {
    diagku = buatajaxdiagnosa();
    var url = "TRXAPOLI01C-DIAGNOSA.php";
    diagku.onreadystatechange = stateChangeddiagnosa;
    var params = "q=" + regicode + "|" + diagcode;
    diagku.open("POST", url, true);
    diagku.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    diagku.setRequestHeader("Content-length", params.length);
    diagku.setRequestHeader("Connection", "close");
    diagku.send(params);
  }
}

function buatajaxdiagnosa() {
  if (window.XMLHttpRequest) {
    return new XMLHttpRequest();
  }
  if (window.ActiveXObject) {
    return new ActiveXObject("Microsoft.XMLHTTP");
  }
  return null;
}

function stateChangeddiagnosa() {
  var data;
  if (diagku.readyState == 4 && diagku.status == 200) {
    data = diagku.responseText;
    if (data.trim().length > 0) {
      document.getElementById("tbllistdiag").innerHTML = data;
      document.getElementById("tbllistdiag").style.display = "block";
    }
    else {
      document.getElementById("tbllistdiag").innerHTML = "";
      document.getElementById("tbllistdiag").style.display = "none";
    }
  }
}

function isidiagnosa(outregicode, outdiagcode, outdiagname) {
  try {

    inputdiagnosa(outregicode, outdiagcode, outdiagname);

    // document.getElementById("txtlistdiag").value = outdiagname;
    //document.getElementById("hidprocdivi").value = tbldivicode;
    document.getElementById("txtlistdiag").focus();
    document.getElementById("tbllistdiag").style.display = "none";
    document.getElementById("tbllistdiag").innerHTML = "";

  }
  catch (err) { alert(err.message); }
}

// Layer Control End


// input daftar diagnosa
var ajaxinputdiagnosa;
function inputdiagnosa(regicode, diagcode, diagname) {
  ajaxinputdiagnosa = buatajaxinputdiagnosa();
  var url = "TRXAPOLI01E-DIAGNOSA.php";
  ajaxinputdiagnosa.onreadystatechange = stateChangedInputdiagnosa;
  var params = "q=" + regicode + "|" + diagcode + "|" + diagname;
  //alert(params);
  ajaxinputdiagnosa.open("POST", url, true);
  ajaxinputdiagnosa.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  //ajaxinputdiagnosa.setRequestHeader("Content-length", params.length);
  //ajaxinputdiagnosa.setRequestHeader("Connection", "close");
  ajaxinputdiagnosa.send(params);
}

function buatajaxinputdiagnosa() {
  if (window.XMLHttpRequest) {
    return new XMLHttpRequest();
  }
  if (window.ActiveXObject) {
    return new ActiveXObject("Microsoft.XMLHTTP");
  }
  return null;
}

function stateChangedInputdiagnosa() {
  if (ajaxinputdiagnosa.readyState == 4) {
    document.getElementById('txtlistdiag').value = '';
    let regidiagcode = document.getElementById('txtexamcode').value;

    //alert(regidiagcode);

    ambilscreendiagnosa(regidiagcode);

    //}
  }
}
// Selesai Input Data Diagnosa

// Tampilkan data yang diinput dalam datable
var asr;
function ambilscreendiagnosa(regicode) {

  if (regicode.length > 14) {
    document.getElementById("tbldiagnosa").style.display = "none";
  }
  else {
    asr = buatajaxscreendiagnosa();
    var url = "TRXAPOLI01V-DIAGNOSA.php";
    asr.onreadystatechange = stateChangedscreendiagnosa;
    var params = "q=" + regicode;
    asr.open("POST", url, true);
    //beberapa http header harus kita set kalau menggunakan POST
    asr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // asr.setRequestHeader("Content-length", params.length);
    // asr.setRequestHeader("Connection", "close");
    asr.send(params);
  }
}

function buatajaxscreendiagnosa() {
  if (window.XMLHttpRequest) {
    return new XMLHttpRequest();
  }
  if (window.ActiveXObject) {
    return new ActiveXObject("Microsoft.XMLHTTP");
  }
  return null;
}

function stateChangedscreendiagnosa() {
  var datapost;
  if (asr.readyState == 4 && asr.status == 200) {
    datapost = asr.responseText;
    if (datapost.length > 0) {
      document.getElementById("tbldiagnosa").innerHTML = datapost;
      document.getElementById("tbldiagnosa").style.display = "block";
    }
    else {
      document.getElementById("tbldiagnosa").innerHTML = "";
      document.getElementById("tbldiagnosa").style.display = "none";
    }
  }
}


// Hapus 
var ajaxhapus;
function hapuscode(examcode, diagcode) {
  ajaxhapus = buatajaxhapus();
  //var url="TBLIUNIT01D.php";
  var url = "TRXAPOLI01D.php";
  ajaxhapus.onreadystatechange = stateChangedhapus;
  var params = "q=" + examcode + "|" + diagcode;
  //alert(params);
  ajaxhapus.open("POST", url, true);
  ajaxhapus.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajaxhapus.setRequestHeader("Content-length", params.length);
  ajaxhapus.setRequestHeader("Connection", "close");
  ajaxhapus.send(params);
}

function buatajaxhapus() {
  if (window.XMLHttpRequest) {
    return new XMLHttpRequest();
  }
  if (window.ActiveXObject) {
    return new ActiveXObject("Microsoft.XMLHTTP");
  }
  return null;
}

function stateChangedhapus() {
  var data;
  if (ajaxhapus.readyState == 4) {
    var inexamcode = document.getElementById('txtexamcode').value;
    ambilscreendiagnosa(inexamcode);
    document.getElementById('txtlistdiag').focus();
  }
}
// Selesai hapus Data 



// ============================================
// INTEGRASI CARD PERAWATAN PASIEN & LAIN LAIN
// ============================================

var tindakanku;
function cariTindakan(kata, regipoli, regipaym) {
  tindakanku = buatajaxtindakan();
  if (!tindakanku) return;
  var url = "TRXAPOLI01C-TINDAKAN.php";
  tindakanku.onreadystatechange = stateChangedtindakan;
  var params = "q=" + encodeURIComponent(kata) + "|" + encodeURIComponent(regipoli) + "|" + encodeURIComponent(regipaym);
  tindakanku.open("POST", url, true);
  tindakanku.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  tindakanku.send(params);
}

function buatajaxtindakan() {
  if (window.XMLHttpRequest) return new XMLHttpRequest();
  if (window.ActiveXObject) return new ActiveXObject("Microsoft.XMLHTTP");
  return null;
}

function stateChangedtindakan() {
  if (tindakanku.readyState == 4 && tindakanku.status == 200) {
    var data = tindakanku.responseText;
    if (data.trim().length > 3) {
      document.getElementById("tbltindakan").innerHTML = data;
      document.getElementById("tbltindakan").style.display = "block";
    } else {
      document.getElementById("tbltindakan").innerHTML = "";
      document.getElementById("tbltindakan").style.display = "none";
    }
  }
}

function isitindakan(outmedicode, outmediname, outmedirate) {
  try {
    document.getElementById("txtmedicode").value = outmediname;
    document.getElementById("hidmedicode").value = outmedicode;
    document.getElementById("hidmedirate").value = outmedirate;
    document.getElementById("txttretquty").focus();
    document.getElementById("tbltindakan").style.display = "none";
    document.getElementById("tbltindakan").innerHTML = "";
  } catch (err) {
    alert(err.message);
  }
}

var ajaxinputtret;
function executeInputTindakan() {
  var tretcode = document.getElementById("txtexamcode").value;
  var tretdoct = document.getElementById("hidexamdoct").value;
  var medicode = document.getElementById("hidmedicode").value;
  var medirate = document.getElementById("hidmedirate").value;
  var tretquty = document.getElementById("txttretquty").value;
  var mediroom = document.getElementById("hidmediroom").value;

  if (!tretcode) {
    swal("Error", "Pendaftaran belum dipilih/diperiksa!", "error");
    return;
  }
  if (!medicode || document.getElementById("txtmedicode").value == "") {
    swal("Tindakan Kosong", "Anda belum memilih Layanan/Tindakan yang valid", "warning");
    return;
  }
  if (!tretquty || tretquty == "0" || isNaN(tretquty) || tretquty < 1) {
    swal("Qty Salah", "Harap masukkan Qty tindakan yang valid", "warning");
    return;
  }

  ajaxinputtret = buatajaxinput();
  var url = "TRXAPOLI01E-TINDAKAN.php";
  ajaxinputtret.onreadystatechange = stateChangedInputTindakan;
  var params = "q=" + tretcode + "|" + tretdoct + "|" + medicode + "|" + medirate + "|" + tretquty + "|" + mediroom;
  ajaxinputtret.open("POST", url, true);
  ajaxinputtret.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajaxinputtret.send(params);
}

function stateChangedInputTindakan() {
  if (ajaxinputtret.readyState == 4) {
    document.getElementById("txtmedicode").value = "";
    document.getElementById("hidmedicode").value = "";
    document.getElementById("hidmedirate").value = "";
    document.getElementById("txttretquty").value = "1";

    var tretcode = document.getElementById("txtexamcode").value;
    executeAmbilscreenTindakan(tretcode);
    document.getElementById("txtmedicode").focus();
  }
}

var ajaxscreentret;
function executeAmbilscreenTindakan(tretcode) {
  if (!tretcode) {
    document.getElementById("tbltreatment").innerHTML = "";
    return;
  }
  ajaxscreentret = buatajaxinput();
  var url = "TRXAPOLI01V-TINDAKAN.php";
  ajaxscreentret.onreadystatechange = stateChangedScreenTindakan;
  var params = "q=" + encodeURIComponent(tretcode);
  ajaxscreentret.open("POST", url, true);
  ajaxscreentret.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajaxscreentret.send(params);
}

function stateChangedScreenTindakan() {
  if (ajaxscreentret.readyState == 4 && ajaxscreentret.status == 200) {
    var data = ajaxscreentret.responseText;
    document.getElementById("tbltreatment").innerHTML = data;
  }
}

var ajaxhapustret;
function executeHapusTindakan(tretcode, medicode) {
  if (!confirm("Apakah Anda yakin ingin menghapus tindakan ini?")) {
    return;
  }
  ajaxhapustret = buatajaxinput();
  var url = "TRXAPOLI01D-TINDAKAN.php";
  ajaxhapustret.onreadystatechange = stateChangedHapusTindakan;
  var params = "q=" + tretcode + "|" + medicode;
  ajaxhapustret.open("POST", url, true);
  ajaxhapustret.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajaxhapustret.send(params);
}

function stateChangedHapusTindakan() {
  if (ajaxhapustret.readyState == 4) {
    var tretcode = document.getElementById("txtexamcode").value;
    executeAmbilscreenTindakan(tretcode);
    document.getElementById("txtmedicode").focus();
  }
}

// Fitur Cetak Surat
function executeCetakSurat() {
  var jenis = document.getElementById("jenis_surat_baru").value;
  var regicode = document.getElementById("txtexamcode").value;
  var nama = document.getElementById("txtmainname").value;

  if (!regicode) {
    swal("Error", "Pasien belum dipilih/diperiksa!", "error");
    return;
  }
  if (!jenis) {
    swal("Pilih Jenis Surat", "Silakan pilih jenis surat yang ingin dicetak", "warning");
    return;
  }

  swal("Cetak Berhasil", "Mengirim perintah cetak untuk surat " + jenis + " pasien " + nama, "success");
}

// Fitur Rujuk Pasien
var ajaxrujuk;
function executeRujukPasien() {
  var regicode = document.getElementById("txtexamcode").value;
  var rsSelected = document.getElementById("radarujuk_rs").checked;
  var labSelected = document.getElementById("radarujuk_lab").checked;
  var rtype = "";
  if (rsSelected) rtype = "RS";
  if (labSelected) rtype = "LB";

  var rnote = document.getElementById("txtrujuknote").value;

  if (!regicode) {
    swal("Error", "Pasien belum dipilih/diperiksa!", "error");
    return;
  }
  if (!rtype) {
    swal("Pilih Opsi Rujukan", "Silakan pilih salah satu opsi rujukan (Rumah Sakit atau Laboratorium)", "warning");
    return;
  }
  if (rtype === "RS" && rnote.trim() === "") {
    swal("Note Kosong", "Silakan masukkan nama rumah sakit tujuan atau alasan rujukan pada note", "warning");
    return;
  }

  ajaxrujuk = buatajaxinput();
  var url = "TRXAPOLI01E-RUJUK.php";
  ajaxrujuk.onreadystatechange = stateChangedRujukPasien;
  var params = "q=" + regicode + "|" + rtype + "|" + encodeURIComponent(rnote);
  ajaxrujuk.open("POST", url, true);
  ajaxrujuk.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajaxrujuk.send(params);
}

function stateChangedRujukPasien() {
  if (ajaxrujuk.readyState == 4 && ajaxrujuk.status == 200) {
    var data = ajaxrujuk.responseText.trim();
    var parts = data.split("|");
    if (parts[0] === "SUCCESS") {
      var rtype = document.getElementById("radarujuk_rs").checked ? "RS" : "LB";
      if (rtype === "LB") {
        swal("Rujukan Internal Berhasil", "Pasien langsung terdaftar ke Admisi Laboratorium. Dokter tujuan: dr. Herawati Ningsih.", "success");
      } else {
        swal("Rujukan Eksternal Berhasil", "Status rujukan eksternal berhasil disimpan ke database.", "success");
      }

      // Reset radio buttons and textarea
      document.getElementById("radarujuk_rs").checked = false;
      document.getElementById("radarujuk_lab").checked = false;
      document.getElementById("txtrujuknote").value = "";
    } else {
      swal("Gagal", parts[1] || "Gagal menyimpan rujukan", "error");
    }
  }
}

function toggleRujukNotes(isRS) {
  var noteArea = document.getElementById("txtrujuknote");
  if (isRS) {
    noteArea.placeholder = "Masukkan Nama Rumah Sakit Rujukan / Keterangan";
    noteArea.focus();
  } else {
    noteArea.placeholder = "Keterangan Pemeriksaan Laboratorium";
  }
}

// Close suggestion box when clicking outside
document.addEventListener('click', function (e) {
  if (document.getElementById("tbltindakan") && document.getElementById("txtmedicode")) {
    if (!e.target.closest('#txtmedicode') && !e.target.closest('#tbltindakan')) {
      document.getElementById('tbltindakan').style.display = 'none';
    }
  }
});

// ============================================
// AUTO-LOAD PATIENT FROM URL PARAMS
// Used when navigating from TRXAPOLI00 to TRXAPOLI01
// ============================================
(function () {
  var params = new URLSearchParams(window.location.search);
  var patiCode = params.get('pati');
  var examCode = params.get('exam');
  if (patiCode && examCode) {
    setTimeout(function () {
      if (typeof viewcode === 'function') {
        viewcode(examCode, patiCode);
      }
    }, 500);
  }
})();
