var aksesku;
function periksaakses(fieldid) {
  aksesku = buatajax();
  var url = "ACCESS.php";
  aksesku.onreadystatechange = stateChangedAkses;
  var params = "q=" + fieldid;
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
    var data = aksesku.responseText.trim();
    if (data.length > 1) {
      ambilscreen('', document.getElementById('hidlabodoct').value);
    }
  }
}

function viewcode(regicode, paticode) {
  try {
    var ajaxview = buatajax();
    ajaxview.onreadystatechange = function () {
      if (ajaxview.readyState == 4) {
        var data = ajaxview.responseText;
        if (data.length > 1) {
          var res = data.split("|");
          document.getElementById("txtlaboregi").value = res[1];
          document.getElementById("txtpaticode").value = res[2];
          document.getElementById("txtmainname").value = res[3];
          document.getElementById("txtmaingend").value = res[5];
          document.getElementById("hidmaingend").value = res[4];

          if (res[6] == 'Tn.') document.getElementById("hidmaintitl").value = 'M';
          else if (res[6] == 'Ny.' || res[6] == 'Nn.') document.getElementById("hidmaintitl").value = 'F';
          else if (res[6] == 'An.') document.getElementById("hidmaintitl").value = 'C';

          document.getElementById("txtmainage").value = res[7];
          document.getElementById("txtbirtdate").value = res[8];
          document.getElementById("txtmainaddr").value = res[9];
          document.getElementById("txtregipaym").value = res[10];

          ambilhasil(res[1]);
          loadPemeriksaan(res[1]);
        }
      }
    };
    ajaxview.open("POST", "TRXALABO05C.php", true);
    ajaxview.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajaxview.send("q=" + encodeURIComponent(regicode + "|" + paticode));
  } catch (err) {
    alert(err.message);
  }
}

function loadPemeriksaan(regicode) {
  var sel = document.getElementById("txtpemeriksaan");
  sel.innerHTML = '<option value="">Memuat...</option>';
  document.getElementById("hidmedicode").value = "";
  document.getElementById("hidtempcode").value = "";
  clearLabItems("Memuat pemeriksaan...");

  $.ajax({
    url: "TRXALABO05C-PEMERIKSAAN.php",
    type: "POST",
    dataType: "json",
    data: { regicode: regicode },
    success: function (res) {
      sel.innerHTML = '<option value="">-- Pilih pemeriksaan --</option>';
      if (!res || res.status !== "ok" || !res.data || res.data.length === 0) {
        sel.innerHTML = '<option value="">Tidak ada order lab di trxatret</option>';
        clearLabItems("Tidak ada order lab. Anda bisa tambah item manual.");
        return;
      }
      for (var i = 0; i < res.data.length; i++) {
        var d = res.data[i];
        var opt = document.createElement("option");
        opt.value = d.medi_code;
        opt.text = (d.medi_name || d.medi_code) + (d.temp_code ? "" : " (tanpa template)");
        opt.setAttribute("data-temp", d.temp_code || "");
        opt.setAttribute("data-name", d.medi_name || d.medi_code);
        sel.appendChild(opt);
      }
      if (res.data.length === 1) {
        sel.selectedIndex = 1;
        onPemeriksaanChange(sel);
      }
    },
    error: function () {
      sel.innerHTML = '<option value="">Gagal load pemeriksaan</option>';
      clearLabItems("Gagal memuat daftar pemeriksaan");
    }
  });
}

function onPemeriksaanChange(sel) {
  var opt = sel.options[sel.selectedIndex];
  if (!opt || !opt.value) {
    document.getElementById("hidmedicode").value = "";
    document.getElementById("hidtempcode").value = "";
    clearLabItems("Pilih pemeriksaan untuk memuat item lab");
    return;
  }
  var medicode = opt.value;
  var tempcode = opt.getAttribute("data-temp") || "";
  document.getElementById("hidmedicode").value = medicode;
  document.getElementById("hidtempcode").value = tempcode;
  getLabTemplate(medicode, tempcode);
}

function getLabTemplate(medicode, tempcode) {
  clearLabItems("Memuat template...");
  var laboregi = document.getElementById("txtlaboregi")
    ? document.getElementById("txtlaboregi").value
    : "";
  $.ajax({
    url: "TRXALABO05C-TEMPLATE.php",
    type: "POST",
    dataType: "json",
    data: {
      kode_pemeriksaan: medicode || "",
      temp_code: tempcode || "",
      laboregi: laboregi || ""
    },
    success: function (res) {
      if (res && res.template && res.template.temp_code) {
        document.getElementById("hidtempcode").value = res.template.temp_code;
      }
      if (res && res.items && res.items.length > 0) {
        renderLabItems(res.items);
      } else {
        clearLabItems("Template kosong. Klik Tambah Item untuk input manual.");
      }
    },
    error: function () {
      clearLabItems("Gagal memuat template");
    }
  });
}

function clearLabItems(msg) {
  var tbody = document.getElementById("lab-items-container");
  tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#888;">' + (msg || "") + "</td></tr>";
}

function escapeHtml(str) {
  if (str == null) return "";
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");
}

function renderLabItems(items) {
  var tbody = document.getElementById("lab-items-container");
  tbody.innerHTML = "";
  var no = 0;
  for (var i = 0; i < items.length; i++) {
    var it = items[i];
    var isHeader = (it.item_is_header === "Y" || it.item_is_header === "y");
    no++;
    var tr = document.createElement("tr");
    if (isHeader) tr.className = "row-header";

    var rujukan = it.item_rujukan || "";
    var satuan = it.item_satuan || "";
    var name = it.item_name || "";
    var dtlId = it.dtl_id || "";

    if (isHeader) {
      tr.innerHTML =
        '<td>' + no + "</td>" +
        '<td colspan="4"><strong>' + escapeHtml(name) + "</strong>" +
        '<input type="hidden" name="item_name[]" value="' + escapeHtml(name) + '">' +
        '<input type="hidden" name="item_hasil[]" value="">' +
        '<input type="hidden" name="item_rujukan[]" value="">' +
        '<input type="hidden" name="item_satuan[]" value="">' +
        '<input type="hidden" name="item_dtl_id[]" value="' + escapeHtml(dtlId) + '">' +
        '<input type="hidden" name="item_is_header[]" value="Y">' +
        "</td>" +
        '<td><button type="button" class="btn-icon-del" title="Hapus" onclick="hapusBaris(this)"><i class="bi bi-trash"></i></button></td>';
    } else {
      tr.innerHTML =
        '<td class="col-no">' + no + "</td>" +
        '<td><input class="form-control" type="text" name="item_name[]" value="' + escapeHtml(name) + '" readonly>' +
        '<input type="hidden" name="item_dtl_id[]" value="' + escapeHtml(dtlId) + '">' +
        '<input type="hidden" name="item_is_header[]" value="N"></td>' +
        '<td><input class="form-control" type="text" name="item_hasil[]" value="" maxlength="50" placeholder="Hasil"></td>' +
        '<td><div class="item-rujukan">' + escapeHtml(rujukan) + "</div>" +
        '<input type="hidden" name="item_rujukan[]" value="' + escapeHtml(rujukan) + '"></td>' +
        '<td><input class="form-control" type="text" name="item_satuan[]" value="' + escapeHtml(satuan) + '" readonly></td>' +
        '<td><button type="button" class="btn-icon-del" title="Hapus" onclick="hapusBaris(this)"><i class="bi bi-trash"></i></button></td>';
    }
    tbody.appendChild(tr);
  }
  renumberRows();
}

function tambahBarisManual() {
  var tbody = document.getElementById("lab-items-container");
  if (tbody.querySelector("td[colspan]")) {
    tbody.innerHTML = "";
  }
  var tr = document.createElement("tr");
  tr.innerHTML =
    '<td class="col-no"></td>' +
    '<td><input class="form-control" type="text" name="item_name[]" value="" placeholder="Nama item" maxlength="100">' +
    '<input type="hidden" name="item_dtl_id[]" value="">' +
    '<input type="hidden" name="item_is_header[]" value="N"></td>' +
    '<td><input class="form-control" type="text" name="item_hasil[]" value="" maxlength="50" placeholder="Hasil"></td>' +
    '<td><input class="form-control" type="text" name="item_rujukan[]" value="" placeholder="Nilai rujukan"></td>' +
    '<td><input class="form-control" type="text" name="item_satuan[]" value="" placeholder="Satuan"></td>' +
    '<td><button type="button" class="btn-icon-del" title="Hapus" onclick="hapusBaris(this)"><i class="bi bi-trash"></i></button></td>';
  tbody.appendChild(tr);
  renumberRows();
  var inp = tr.querySelector('input[name="item_name[]"]');
  if (inp) inp.focus();
}

function hapusBaris(btn) {
  var tr = btn.closest("tr");
  if (tr) tr.parentNode.removeChild(tr);
  var tbody = document.getElementById("lab-items-container");
  if (!tbody.querySelector("tr")) {
    clearLabItems("Tidak ada item. Klik Tambah Item atau pilih pemeriksaan.");
  } else {
    renumberRows();
  }
}

function renumberRows() {
  var rows = document.querySelectorAll("#lab-items-container tr");
  var n = 0;
  for (var i = 0; i < rows.length; i++) {
    if (rows[i].querySelector("td[colspan='6']")) continue;
    n++;
    var cell = rows[i].querySelector(".col-no") || rows[i].cells[0];
    if (cell) cell.textContent = n;
  }
}

function submitHasilLab() {
  var laboregi = document.getElementById("txtlaboregi").value;
  var labodoct = document.getElementById("hidlabodoct").value;
  var medicode = document.getElementById("hidmedicode").value;
  var tempcode = document.getElementById("hidtempcode").value;
  var noteEl = document.getElementById("txtlabonote");
  var labonote = noteEl ? noteEl.value : "";

  if (!laboregi) {
    swal({ title: "Nomor Pemeriksaan Kosong", text: "Anda belum memilih Pasien", icon: "warning" });
    return;
  }
  if (!labodoct) {
    swal({ title: "Petugas kosong", text: "Nama petugas medis kosong", icon: "warning" });
    return;
  }

  var names = document.querySelectorAll('#lab-items-container input[name="item_name[]"]');
  var hasils = document.querySelectorAll('#lab-items-container input[name="item_hasil[]"]');
  var headers = document.querySelectorAll('#lab-items-container input[name="item_is_header[]"]');
  var filled = 0;
  for (var i = 0; i < names.length; i++) {
    var isH = headers[i] && headers[i].value === "Y";
    if (!isH && names[i].value.trim() !== "" && hasils[i] && hasils[i].value.trim() !== "") {
      filled++;
    }
  }
  if (filled === 0) {
    swal({ title: "Hasil kosong", text: "Isi minimal 1 item hasil pemeriksaan", icon: "warning" });
    return;
  }

  var payload = {
    laboregi: laboregi,
    labodoct: labodoct,
    medicode: medicode,
    tempcode: tempcode,
    labonote: labonote,
    "item_name[]": [],
    "item_hasil[]": [],
    "item_rujukan[]": [],
    "item_satuan[]": [],
    "item_dtl_id[]": [],
    "item_is_header[]": []
  };

  var rows = document.querySelectorAll("#lab-items-container tr");
  for (var r = 0; r < rows.length; r++) {
    var row = rows[r];
    if (row.querySelector("td[colspan='6']")) continue;
    var n = row.querySelector('input[name="item_name[]"]');
    var h = row.querySelector('input[name="item_hasil[]"]');
    var j = row.querySelector('input[name="item_rujukan[]"]');
    var s = row.querySelector('input[name="item_satuan[]"]');
    var d = row.querySelector('input[name="item_dtl_id[]"]');
    var ih = row.querySelector('input[name="item_is_header[]"]');
    if (!n) continue;
    payload["item_name[]"].push(n.value);
    payload["item_hasil[]"].push(h ? h.value : "");
    payload["item_rujukan[]"].push(j ? j.value : "");
    payload["item_satuan[]"].push(s ? s.value : "");
    payload["item_dtl_id[]"].push(d ? d.value : "");
    payload["item_is_header[]"].push(ih ? ih.value : "N");
  }

  $.ajax({
    url: "TRXALABO05E.php",
    type: "POST",
    dataType: "json",
    data: payload,
    traditional: true,
    success: function (res) {
      if (res && res.status === "ok") {
        swal({ title: "Berhasil", text: res.message || "Tersimpan", icon: "success" });
        ambilhasil(laboregi);
      } else {
        swal({ title: "Gagal", text: (res && res.message) || "Gagal simpan", icon: "error" });
      }
    },
    error: function (xhr) {
      swal({ title: "Error", text: "Gagal kirim data: " + (xhr.responseText || xhr.status), icon: "error" });
    }
  });
}

function printHasilLab() {
  var outlaboregi = document.getElementById("txtlaboregi").value;
  if (!outlaboregi) {
    swal({
      title: "Data Hasil belum di pilih",
      text: "Anda belum memilih Data Hasil Pemeriksaan Laboratorium",
      icon: "warning"
    });
    return;
  }
  window.open("TRXALABO05P.php?laboregi=" + encodeURIComponent(outlaboregi), "_blank");
}

var drz;
function ambilscreen(kata, nakes) {
  if (nakes.length > 5) {
    var el = document.getElementById("tblscreen");
    if (el) el.style.visibility = "hidden";
    return;
  }
  drz = buatajax();
  drz.onreadystatechange = function () {
    if (drz.readyState == 4 && drz.status == 200) {
      var el = document.getElementById("tblscreen");
      if (!el) return;
      var datapost = drz.responseText;
      if (datapost.length > 0) {
        el.innerHTML = datapost;
        el.style.visibility = "";
      } else {
        el.innerHTML = "";
        el.style.visibility = "hidden";
      }
    }
  };
  drz.open("POST", "TRXALABO05V.php", true);
  drz.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  drz.send("q=" + encodeURIComponent((kata || "") + "|" + nakes));
}

var resultku;
function ambilhasil(regicode) {
  if (!regicode || regicode.length > 14) {
    var el = document.getElementById("tblviewresult");
    if (el) {
      el.innerHTML = "";
      el.style.visibility = "hidden";
    }
    return;
  }
  resultku = buatajax();
  resultku.onreadystatechange = function () {
    if (resultku.readyState == 4 && resultku.status == 200) {
      var el = document.getElementById("tblviewresult");
      if (!el) return;
      var data = resultku.responseText;
      if (data.length > 3) {
        el.innerHTML = data;
        el.style.visibility = "";
      } else {
        el.innerHTML = "";
        el.style.visibility = "hidden";
      }
    }
  };
  resultku.open("POST", "TRXALABO05C-LABORESULT.php", true);
  resultku.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  resultku.send("q=" + encodeURIComponent(regicode));
}

var ajaxhapus;
function hapuscode(regicode, hasilid) {
  ajaxhapus = buatajax();
  ajaxhapus.onreadystatechange = function () {
    if (ajaxhapus.readyState == 4) {
      ambilhasil(document.getElementById("txtlaboregi").value);
    }
  };
  ajaxhapus.open("POST", "TRXALABO05D.php", true);
  ajaxhapus.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  ajaxhapus.send("q=" + encodeURIComponent(regicode + "|" + hasilid));
}
