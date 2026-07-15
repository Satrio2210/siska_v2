  // periksa akses
    var aksesku;
  function periksaakses(fieldid)
  {
    aksesku = buatajaxakses();
    var url="ACCESS.php";
    aksesku.onreadystatechange=stateChangedAkses;
    var params = "q="+fieldid;
    //alert('Parameter adalah '+fieldid);
    aksesku.open("POST",url,true);
    aksesku.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    aksesku.setRequestHeader("Content-length", params.length);
    aksesku.setRequestHeader("Connection", "close");
    aksesku.send(params);

  }

  function buatajaxakses()
  {
    if (window.XMLHttpRequest)
    {
    return new XMLHttpRequest();
    }
    if (window.ActiveXObject)
    {
    return new ActiveXObject("Microsoft.XMLHTTP");
    }
    return null;
  }

  function stateChangedAkses()
  {
  var data;
  if (aksesku.readyState==4)
    {

    xdata=aksesku.responseText;
    data=xdata.trim();
    
    var urlParams = new URLSearchParams(window.location.search);
    var hasRegicode = !!urlParams.get('regicode');
    console.log('stateChangedAkses', {dataLen: data.length, hasRegicode, url: window.location.href});
    
    // Auto-load pasien dari URL (?regicode=...&paticode=...): jangan disable field,
    // karena viewcode() akan mengisi & meng-enable field. Menghindari race
    // antara response periksaakses (disable) dan viewcode (enable).
    if (hasRegicode) {
      console.log('stateChangedAkses: return early karena regicode di URL');
      return;
    }
    
    // Jika di halaman daftar pasien (TRXASALE00), langsung tampilkan tabel
    if (document.getElementById('tblscreen')) {
      console.log('stateChangedAkses: tblscreen ditemukan, ambilscreen');
      ambilscreen('');
      return;
    }
    
    if(data.length>1)
      {
        console.log('stateChangedAkses: data>1, reset form');
        document.getElementById('hidregicode').value = '';
        document.getElementById('hidpaticode').value = '';      
        document.getElementById('hidregidoct').value = '';      
        document.getElementById('hidregipoli').value = '';      
        document.getElementById('txtpaymtota').value = '';
        document.getElementById('txtpaymamnt').value = '';
        document.getElementById('txtpaymamnt').setAttribute('disabled','true');
        document.getElementById('txtpaymdisc').setAttribute('disabled','true');
        document.getElementById('optpaymmode').setAttribute('disabled','true');

        ambilscreen('');
      }
      else
      {
        console.log('stateChangedAkses: data<=1, disable field');
        document.getElementById('txtpaymamnt').setAttribute('disabled','true');
        document.getElementById('txtpaymdisc').setAttribute('disabled','true');
        document.getElementById('optpaymmode').setAttribute('disabled','true');

      }
    }
  }
// end periksa akses  

  // Input Data Pembayaran Pasien dari form ke Tabel 
//inregicode,inpaticode,inregidoct,inregipoli,inregipaym,inpaymtota,inpaymamnt,inpaymdisc,inpaymmode
  var ajaxinput;
  function input(regicode,paticode,regidoct,regipoli,regipaym,paymtota,paymamnt,paymdisc,paymmode)
  {
    ajaxinput = buatajaxinput();
    var url="TRXASALE01E.php";
    ajaxinput.onreadystatechange=stateChangedInput;
    var params = "q="+regicode+"|"+paticode+"|"+regidoct+"|"+regipoli+"|"+regipaym+"|"+paymtota+"|"+paymamnt+"|"+paymdisc+"|"+paymmode;
    //alert(params);
    ajaxinput.open("POST",url,true);
    ajaxinput.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    //ajaxinput.setRequestHeader("Content-length", params.length);
    //ajaxinput.setRequestHeader("Connection", "close");
    ajaxinput.send(params);
  }

  function buatajaxinput()
  {
    if (window.XMLHttpRequest)
    {
    return new XMLHttpRequest();
    }
    if (window.ActiveXObject)
    {
    return new ActiveXObject("Microsoft.XMLHTTP");
    }
    return null;
  }

  function stateChangedInput()
  {
  if (ajaxinput.readyState==4)
    {

        let xregicode = document.getElementById('hidregicode').value;
        ambilviewinvc(xregicode);

        document.getElementById('hidregicode').value = '';
        document.getElementById('hidpaticode').value = '';
        document.getElementById('hidregidoct').value = '';
        document.getElementById('hidregipoli').value = '';
        document.getElementById('hidregipaym').value = '';
        document.getElementById('hidpaymtota').value = '';

        document.getElementById('txtpaymtota').value = '';
        document.getElementById('txtpaymamnt').value = '';
        document.getElementById('txtpaymkemb').value = '0';
        document.getElementById('txtpaymamnt').setAttribute('disabled','true');

        document.getElementById('txtpaymdisc').value = '0';
        document.getElementById('txtpaymdisc').setAttribute('disabled','true');

        document.getElementById('optpaymmode').setAttribute('disabled','true');
        
        document.getElementById('txtregidate').value = '';
        document.getElementById('txtpatiname').value = '';
        document.getElementById('txtpatigend').value = '';
        document.getElementById('txtpatibirt').value = '';
        document.getElementById('txtregidoctdisp').value = '';
        document.getElementById('txtregipolidisp').value = '';
        document.getElementById('txtregipaymdisp').value = '';

        if (document.getElementById('tblscreen')) {
          let xcari = document.getElementById('txtsearch').value;
          ambilscreen(xcari);
        }

        swal({
          title: 'Sukses',
          text: 'Pembayaran berhasil',
          icon: 'success'
        });

        //}
    }
  }
  // Selesai Input Data Wall ke Tabel tblitype

// Tampilkan Data Register   yang telah di pilih pada tabel untuk di ubah pada form 
var ajaxview
function viewcode(regicode,paticode)
{
  try 
  {
    console.log('viewcode dipanggil', {regicode, paticode});
    ajaxview = buatajaxview();
    var url="TRXASALE01C.php";
    ajaxview.onreadystatechange=stateChangedView;
    var params = "q="+regicode+"|"+paticode;
    console.log('viewcode kirim request', {url, params});
    //alert(params);
    ajaxview.open("POST",url,true);
    ajaxview.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajaxview.setRequestHeader("Content-length", params.length);
    ajaxview.setRequestHeader("Connection", "close");
    ajaxview.send(params);

  } 
  catch(err){ alert(err.message); }
}

  function buatajaxview()
  {
    if (window.XMLHttpRequest)
    {
    return new XMLHttpRequest();
    }
    if (window.ActiveXObject)
    {
    return new ActiveXObject("Microsoft.XMLHTTP");
    }
    return null;
  }

function stateChangedView()
{
  var data;
  if (ajaxview.readyState==4)
    {
      data=ajaxview.responseText;
      console.log('stateChangedView response', {data, length: data.length});
      if(data.length>1)
      {
        var res = data.split("|");
        console.log('stateChangedView parsed', res);
        document.getElementById("hidregicode").value = res[1];
        document.getElementById("hidpaticode").value = res[2];
        document.getElementById("hidregidoct").value = res[3];
        document.getElementById("hidregipoli").value = res[4];
        document.getElementById("hidregipaym").value = res[5];
        document.getElementById("hidpaymtota").value = res[6];

        if (res[6] > 0)
        {
          document.getElementById("txtpaymtota").value = res[7];
        }
        else
        {
          document.getElementById("txtpaymtota").value = 0;  
        }

        document.getElementById("txtregidate").value = res[8] || '';
        document.getElementById("txtpatiname").value = res[9] || '';
        document.getElementById("txtpatigend").value = res[10] || '';
        document.getElementById("txtpatibirt").value = res[11] || '';
        document.getElementById("txtregidoctdisp").value = res[12] || '';
        document.getElementById("txtregipolidisp").value = res[13] || '';
        document.getElementById("txtregipaymdisp").value = res[14] || '';

        hitungKembalian();

        document.getElementById("txtpaymamnt").removeAttribute('disabled');
        document.getElementById("txtpaymdisc").removeAttribute('disabled');
        document.getElementById("optpaymmode").removeAttribute('disabled');
        ambilviewinvc(res[1]);
        document.getElementById("txtpaymamnt").focus();
        console.log('stateChangedView: form populated');

      }      
      else {
        console.log('stateChangedView: data kosong');
      }
    }
    else {
      console.log('stateChangedView: readyState=' + ajaxview.readyState);
    }
  }

function hitungKembalian()
{
  var total = parseFloat(convertToAngka(document.getElementById("txtpaymtota").value)) || 0;
  var bayar = parseFloat(convertToAngka(document.getElementById("txtpaymamnt").value)) || 0;
  var kembalian = bayar - total;
  document.getElementById("txtpaymkemb").value = kembalian > 0 ? convertToRupiah(kembalian) : '0';
}


// Tampilkan data yang diinput dalam datable
var drz;
function ambilscreen(kata)
{
  var tblscreen = document.getElementById("tblscreen");
  if (!tblscreen) return;
  
  if(kata.length > 8)
  {
  tblscreen.style.visibility = "hidden";
  }
  else
  {
  drz = buatajaxscreen();
  //var url="TRXAPOLI01V.php";
  var url="TRXASALE01V.php";
  drz.onreadystatechange=stateChangedscreen;
  var params = "q="+kata;
  drz.open("POST",url,true);
  //beberapa http header harus kita set kalau menggunakan POST
  drz.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  drz.setRequestHeader("Content-length", params.length);
  drz.setRequestHeader("Connection", "close");
  drz.send(params);
  }
} 

  function buatajaxscreen()
  {
    if (window.XMLHttpRequest)
    {
    return new XMLHttpRequest();
    }
    if (window.ActiveXObject)
    {
    return new ActiveXObject("Microsoft.XMLHTTP");
    }
    return null;
  }

function stateChangedscreen()
{
  var tblscreen = document.getElementById("tblscreen");
  if (!tblscreen) return;
  
  var datapost;
  if (drz.readyState==4 && drz.status==200)
  {
  datapost=drz.responseText;
    if(datapost.length>0)
    {
    tblscreen.innerHTML = datapost;
    tblscreen.style.visibility = "";
    }
    else  
    {
    tblscreen.innerHTML = "";
    tblscreen.style.visibility = "hidden";
    }
  }
}


var asr;
function ambilviewinvc(regicode)
{
  asr = buatajaxviewinvc();
  var url="TRXASALE01V-INVC.php";
  asr.onreadystatechange=stateChangedviewinvc;
  var params = "q="+regicode;
  asr.open("POST",url,true);
  //beberapa http header harus kita set kalau menggunakan POST
  asr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
  asr.setRequestHeader("Content-length", params.length);
  asr.setRequestHeader("Connection", "close");
  asr.send(params);
  //}
} 

  function buatajaxviewinvc()
  {
    if (window.XMLHttpRequest)
    {
    return new XMLHttpRequest();
    }
    if (window.ActiveXObject)
    {
    return new ActiveXObject("Microsoft.XMLHTTP");
    }
    return null;
  }

function stateChangedviewinvc()
{
  var datapost;
  if (asr.readyState==4 && asr.status==200)
  {
  datapost=asr.responseText;
  //alert(datapost);
    if(datapost.length>0)
    {
    document.getElementById("tblviewinvc").innerHTML = datapost;
    document.getElementById("tblviewinvc").style.visibility = "";
    }
    else  
    {
    document.getElementById("tblviewinvc").innerHTML = "";
    document.getElementById("tblviewinvc").style.visibility = "hidden";
    }
  }
}

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
    })
    .catch(err => {
      console.error('Error fetch panggil_queue POLI:', err);
    });
});

// ============================================
// AUTO-LOAD PATIENT FROM URL PARAMS
// Used when navigating from TRXASALE00 to TRXASALE01
// ============================================
(function () {
  var params = new URLSearchParams(window.location.search);
  var patiCode = params.get('paticode');
  var regicode = params.get('regicode');
  console.log('Auto-load init', {patiCode, regicode, fullUrl: window.location.href});
  if (patiCode && regicode) {
    console.log('Auto-load: schedule viewcode dalam 500ms');
    setTimeout(function () {
      console.log('Auto-load: timeout fired, cek viewcode');
      if (typeof viewcode === 'function') {
        console.log('Auto-load: panggil viewcode', regicode, patiCode);
        viewcode(regicode, patiCode);
      } else {
        console.log('Auto-load: viewcode tidak ditemukan');
      }
    }, 500);
  }
})();
