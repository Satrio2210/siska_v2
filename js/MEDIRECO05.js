// MEDIRECO05 — Daftar & Detail Rekam Medis

var aksesku;
var drz;
var currentQ = '';
var currentPage = 1;
var searchTimer = null;

function periksaakses(fieldid) {
  aksesku = buatajax();
  var url = "MEDIRECO01X-AKSES.php";
  aksesku.onreadystatechange = stateChangedAkses;
  var params = "q=" + encodeURIComponent(fieldid);
  aksesku.open("POST", url, true);
  aksesku.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  aksesku.send(params);
}

function buatajax() {
  if (window.XMLHttpRequest) return new XMLHttpRequest();
  if (window.ActiveXObject) return new ActiveXObject("Microsoft.XMLHTTP");
  return null;
}

function stateChangedAkses() {
  if (aksesku.readyState == 4) {
    var data = (aksesku.responseText || '').trim();
    if (data.length <= 1) {
      if (typeof swal === 'function') {
        swal({
          title: "Akses Ditolak",
          text: "Anda tidak memiliki akses ke modul Rekam Medis.",
          icon: "error",
          button: "OK"
        }).then(function () {
          window.location.href = "index.php";
        });
      } else {
        alert("Anda tidak memiliki akses ke modul Rekam Medis.");
        window.location.href = "index.php";
      }
    }
  }
}

function ambilscreen(kata, page) {
  currentQ = (typeof kata === 'undefined' || kata === null) ? '' : String(kata);
  currentPage = page ? parseInt(page, 10) : 1;
  if (isNaN(currentPage) || currentPage < 1) currentPage = 1;

  drz = buatajax();
  var url = "MEDIRECO05V.php";
  drz.onreadystatechange = stateChangedScreen;
  var params = "q=" + encodeURIComponent(currentQ) + "&page=" + currentPage;
  drz.open("POST", url, true);
  drz.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  drz.send(params);
}

function stateChangedScreen() {
  if (drz.readyState == 4) {
    var el = document.getElementById("tblscreen");
    if (el) {
      el.innerHTML = drz.responseText;
    }
    // sync URL tanpa reload penuh
    if (window.history && window.history.replaceState) {
      var qs = "?q=" + encodeURIComponent(currentQ) + "&page=" + currentPage;
      window.history.replaceState({}, "", "MEDIRECO05.php" + qs);
    }
  }
}

function goPage(e, page) {
  if (e) e.preventDefault();
  if (page < 1) return false;
  var qEl = document.getElementById("txtsearch");
  var q = qEl ? qEl.value : currentQ;
  ambilscreen(q, page);
  return false;
}

// live search (debounce)
document.addEventListener("DOMContentLoaded", function () {
  var qEl = document.getElementById("txtsearch");
  var form = document.getElementById("frmSearchRm05");

  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var q = qEl ? qEl.value : "";
      ambilscreen(q, 1);
    });
  }

  if (qEl) {
    qEl.addEventListener("keyup", function () {
      clearTimeout(searchTimer);
      var val = this.value;
      searchTimer = setTimeout(function () {
        ambilscreen(val, 1);
      }, 350);
    });
  }
});
