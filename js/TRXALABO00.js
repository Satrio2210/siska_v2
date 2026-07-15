// periksa akses
var aksesku;
function periksaakses(fieldid) {
  aksesku = buatajaxakses();
  var url = "TRXALABO01X-AKSES.php";
  aksesku.onreadystatechange = stateChangedAkses;
  var params = "q=" + fieldid;
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

    // Halaman daftar pasien (TRXALABO00): tidak ada form pemeriksaan,
    // langsung tampilkan daftar pasien tanpa mencoba disable field yang tidak ada.
    if (document.getElementById('tblscreen') && !document.getElementById('infoPasien')) {
      ambillaboregis(document.getElementById('hidlabodoct').value);
      return;
    }
  }
}
// end periksa akses

// Tampilkan daftar pasien laboratorium
var drz;
function ambillaboregis(dokter) {
  if (!document.getElementById('tblscreen')) return;
  drz = buatajaxscreen();
  var url = "TRXALABO01V-REGI.php";
  drz.onreadystatechange = stateChangedscreen;
  var params = "q=" + dokter;
  drz.open("POST", url, true);
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
