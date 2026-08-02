// TRXAPATI08 - Data Pasien Rujukan RS

var rmModalInstance = null;

// Tampilkan list pasien rujukan RS
function ambilscreen(kata, page) {
  if (kata && kata.length > 13) {
    document.getElementById("tblscreen").style.visibility = "hidden";
    return;
  }
  if (typeof page === 'undefined') { page = 1; }

  $.ajax({
    url: "TRXAPATI08V.php",
    type: "POST",
    data: { q: kata, page: page },
    success: function (data) {
      if (data && data.length > 0) {
        document.getElementById("tblscreen").innerHTML = data;
        document.getElementById("tblscreen").style.visibility = "";
      } else {
        document.getElementById("tblscreen").innerHTML = "";
        document.getElementById("tblscreen").style.visibility = "hidden";
      }
    }
  });
}

function tblScreenGo(e, page) {
  if (e && e.preventDefault) { e.preventDefault(); }
  if (page < 1) { return false; }
  var kata = document.getElementById('txtsearch') ? document.getElementById('txtsearch').value : '';
  ambilscreen(kata, page);
  return false;
}

// Buka modal rekam medis pasien
function lihatRekam(regicode) {
  var body = document.getElementById('rekamMedisBody');
  if (!body) { return; }

  body.innerHTML = '<div class="rm-empty" style="text-align:center;color:#9ca3af;padding:30px;">Memuat data...</div>';

  if (!rmModalInstance) {
    var el = document.getElementById('modalRekamMedis');
    if (window.bootstrap && el) {
      rmModalInstance = new bootstrap.Modal(el);
    }
  }

  $.ajax({
    url: "TRXAPATI08C.php",
    type: "POST",
    data: { q: regicode },
    success: function (data) {
      if (data && data.length > 0) {
        body.innerHTML = data;
      } else {
        body.innerHTML = '<div class="rm-empty" style="text-align:center;color:#9ca3af;padding:30px;">Data rekam medis tidak ditemukan.</div>';
      }
    },
    error: function () {
      body.innerHTML = '<div class="rm-empty" style="text-align:center;color:#9ca3af;padding:30px;">Gagal memuat data.</div>';
    }
  });

  if (rmModalInstance) {
    rmModalInstance.show();
  }
}
