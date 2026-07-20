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
    <title>Penjualan Obat Bebas</title>
    <link rel="shortcut icon" href="assets/img/icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/layouts/header.css">
    <link rel="stylesheet" href="assets/css/bootstrap/bootstrap.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/layouts/side-menu.css">
    <link rel="stylesheet" href="assets/css/trxapati-shared.css">
    <link rel="stylesheet" href="assets/css/modern-table.css">
    <style>
      .content {
        background: #f8fafc;
        padding: 20px;
      }

      .card-modern {
        background: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 2px solid #e5e7eb;
        border-radius: 18px;
        padding: 16px 18px;
        margin-bottom: 16px;
      }

      .card-title {
        font-size: 16px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 14px;
      }

      .form-grid-drug {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px 16px;
      }

      .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
      }

      .form-group.full {
        grid-column: 1 / -1;
      }

      .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #4B5563;
      }

      .form-control {
        border: 1px solid #D1D5DB;
        color: #1F2937;
        font-size: 14px;
        height: 38px;
        font-weight: 500;
        padding: 0 14px;
        border-radius: 12px;
        width: 100%;
        box-sizing: border-box;
      }

      .form-control:focus {
        outline: none;
        border-color: #059669;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.12);
      }

      .form-control:disabled,
      .form-control[readonly] {
        background: #f0f0f0;
        cursor: not-allowed;
      }

      .checkbox-row {
        display: flex;
        gap: 20px;
        align-items: center;
        padding-top: 8px;
      }

      .checkbox-row label {
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
      }

      .action-row {
        display: flex;
        gap: 10px;
        margin-top: 14px;
      }

      .btn-modern {
        border: none;
        height: 42px;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        padding: 0 20px;
        transition: .2s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
      }

      .btn-save {
        background: #059669;
        color: white;
        width: auto;
      }

      .btn-save:hover {
        background: #047857;
        color: white;
      }

      .btn-pay {
        background: #0ea5e9;
        color: white;
      }

      .btn-pay:hover {
        background: #0284c7;
        color: white;
      }

      .dropdown-panel {
        position: absolute;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .1);
        z-index: 100;
        max-height: 240px;
        overflow: auto;
        visibility: hidden;
        min-width: 280px;
      }

      @media(max-width:900px) {
        .form-grid-drug {
          grid-template-columns: 1fr;
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

  <body onLoad="periksaakses('PASS_DRUG_ENTR');">
    <div id="wrapper">
      <?php include "inc/side-menu.php"; ?>

      <div id="content-wrapper">
        <?php include "inc/header.php"; ?>
        <div class="content">
          <div class="content-modern">

            <form name="frmtrxadrug" method="post" action="TRXADRUG02U.php">

              <div class="card-modern">
                <div class="card-title">Penjualan Obat Bebas</div>
                <div class="form-grid-drug">
                  <div class="form-group">
                    <label class="form-label" for="txtdrugcode">Faktur</label>
                    <input type="text" name="txtdrugcode" id="txtdrugcode" maxlength="14" class="form-control" readonly>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="optpaymmode">Cara Bayar</label>
                    <select name="optpaymmode" id="optpaymmode" class="form-control"
                      onchange="document.getElementById('optpaymmode').focus();">
                      <option value="TUN">Tunai/Cash</option>
                      <option value="BCA">Debit BCA</option>
                      <option value="MAN">Debit Mandiri</option>
                      <option value="BNI">Debit BNI</option>
                      <option value="BCM">Transfer BCA</option>
                      <option value="LIN">Transfer Link Aja</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="txtpaymamnt">Pembayaran</label>
                    <input type="text" name="txtpaymamnt" id="txtpaymamnt" maxlength="10" class="form-control" readonly>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="txtpaymdisc">Diskon</label>
                    <input type="text" name="txtpaymdisc" id="txtpaymdisc" maxlength="10" class="form-control" value="0"
                      onkeydown="if (event.keyCode == 13 && value.length > 0) {
                        if (isNaN(this.value)) {
                          document.getElementById('txtpaymdisc').value = '0';
                          document.getElementById('txtpaymdisc').focus();
                        } else {
                          var inRupiah = convertToRupiah(this.value);
                          document.getElementById('txtpaymdisc').value = inRupiah;
                          document.getElementById('txtstockcode').focus();
                        }
                      }"
                      onclick="if (isNaN(this.value)) {
                        var inAngka = convertToAngka(this.value);
                        document.getElementById('txtpaymdisc').value = inAngka;
                        document.getElementById('txtpaymdisc').focus();
                      }">
                  </div>
                </div>
              </div>

              <div class="card-modern">
                <div class="card-title">Input Item Obat</div>
                <div class="form-grid-drug">
                  <div class="form-group" style="position:relative;">
                    <label class="form-label" for="txtstockcode">Obat</label>
                    <input type="text" name="txtstockcode" id="txtstockcode" maxlength="100" class="form-control"
                      autocomplete="off"
                      onkeyup="if (value.length > 0) {
                        ambilobat(this.value);
                      } else {
                        document.getElementById('tblobat').innerHTML = '';
                        document.getElementById('tblobat').style.visibility = 'hidden';
                      }">
                    <input type="hidden" name="hidstockcode" id="hidstockcode">
                    <input type="hidden" name="hidstockpric" id="hidstockpric">
                    <input type="hidden" name="hidstockquty" id="hidstockquty">
                    <div id="tblobat" class="dropdown-panel"></div>
                  </div>
                  <div class="form-group" style="position:relative;">
                    <label class="form-label" for="txtstockbtch">Kode Batch</label>
                    <input type="text" name="txtstockbtch" id="txtstockbtch" maxlength="10" class="form-control"
                      autocomplete="off"
                      onkeyup="if (value.length > 0) {
                        let stockcode = document.getElementById('hidstockcode').value;
                        ambilbatch(this.value,stockcode);
                      } else {
                        document.getElementById('tblbatch').innerHTML = '';
                        document.getElementById('tblbatch').style.visibility = 'hidden';
                      }">
                    <div id="tblbatch" class="dropdown-panel"></div>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="txtstockquty">Qty</label>
                    <input type="text" name="txtstockquty" id="txtstockquty" maxlength="10" class="form-control" value="1"
                      onkeydown="if (event.keyCode == 13 && value.length > 0) {
                        if (isNaN(this.value)) {
                          this.value = '1';
                          this.focus();
                        } else {
                          document.getElementById('txtdrugsigna').focus();
                        }
                      }"
                      onclick="if (isNaN(this.value)) {
                        this.value = '0';
                        this.focus();
                      }">
                  </div>
                  <div class="form-group">
                    <label class="form-label">Jenis</label>
                    <div class="checkbox-row">
                      <label for="optnonracikan">
                        <input type="checkbox" name="optnonracikan" id="optnonracikan" value="true"
                          onclick="if (checked == true) {
                            document.getElementById('optracikan').checked = false;
                            document.getElementById('hiddrugconc').value = 'N';
                          }">
                        Bukan Racikan
                      </label>
                      <label for="optracikan">
                        <input type="checkbox" name="optracikan" id="optracikan" value="true"
                          onclick="if (checked == true) {
                            document.getElementById('optnonracikan').checked = false;
                            document.getElementById('hiddrugconc').value = 'Y';
                          }">
                        Racikan
                      </label>
                    </div>
                    <input name="hiddrugconc" id="hiddrugconc" type="hidden">
                  </div>
                  <div class="form-group" style="position:relative;">
                    <label class="form-label" for="txtdrugsigna">Aturan Makan</label>
                    <input type="text" name="txtdrugsigna" id="txtdrugsigna" maxlength="100" class="form-control"
                      onkeyup="if (value.length > 0) {
                        ambilsignacode(this.value);
                      } else {
                        document.getElementById('tblsigna').style.visibility = 'hidden';
                      }">
                    <input type="hidden" name="hiddrugsigna" id="hiddrugsigna">
                    <div id="tblsigna" class="dropdown-panel"></div>
                  </div>
                  <div class="form-group full">
                    <label class="form-label" for="txtdrugusage">Cara Pemakaian</label>
                    <input type="text" name="txtdrugusage" id="txtdrugusage" maxlength="100" class="form-control"
                      readonly>
                  </div>
                </div>

                <div class="action-row">
                  <a class="btn-modern btn-save" onclick="javascript:

                  var ambil = parseInt(document.getElementById('txtstockquty').value);
                  var tersedia = parseInt(document.getElementById('hidstockquty').value);

                  if (document.getElementById('hidstockcode').value == '')
                  {
                    swal({
                        title: 'Item Obat Kosong' ,
                        text: 'Anda belum memilih item Obat, silah periksa lagi',
                        icon: 'warning',
                        });
                  }
                  else if (document.getElementById('txtstockbtch').value == '')
                  {
                      swal({
                          title: 'Batch Code Kosong' ,
                          text: 'Anda belum mengisi Batch Code, silah periksa lagi',
                          icon: 'warning',
                          });
                  }
                  else if (document.getElementById('txtstockquty').value == '0')
                  {
                      swal({
                          title: 'Qty Kosong' ,
                          text: 'Anda belum mengisi Kuantiti, silah periksa lagi',
                          icon: 'warning',
                          });
                  }
                  else if (document.getElementById('txtstockquty').value == '')
                  {
                      swal({
                          title: 'Qty Kosong' ,
                          text: 'Anda belum mengisi Kuantiti, silah periksa lagi',
                          icon: 'warning',
                          });
                  }
                  else if (isNaN(document.getElementById('txtstockquty').value))
                  {
                      swal({
                          title: 'Masukkan Angka' ,
                          text: 'Anda memasukkan Huruf, bukan angka, silah periksa lagi',
                          icon: 'warning',
                          });
                  }
                  else if ( ambil > tersedia)
                  {
                      swal({
                          title: 'Over Kuantiti' ,
                          text: 'Jumlah kuantiti anda melebihi jumlah stock saat ini, kurangi kuantiti anda',
                          icon: 'warning',
                          });
                  }
                  else if (document.getElementById('hiddrugconc').value == '')
                  {
                      swal({
                          title: 'Jenis Racikan Obat Kosong' ,
                          text: 'Anda belum memilih Jenis Racikan , silah periksa lagi',
                          icon: 'warning',
                          });
                  }
                  else if (document.getElementById('hiddrugsigna').value == '')
                  {
                      swal({
                          title: 'Signa Kosong' ,
                          text: 'Anda belum mengisi Signa, silah periksa lagi',
                          icon: 'warning',
                          });
                  }
                   else
                   {
                       var indrugcode = document.getElementById('txtdrugcode').value;
                       var inpaymmode = document.getElementById('optpaymmode').value;
                       var inpaymamnt = document.getElementById('txtpaymamnt').value;
                       var inpaymdisc = document.getElementById('txtpaymdisc').value;
                       var instockcode = document.getElementById('hidstockcode').value;
                       var instockpric = document.getElementById('hidstockpric').value;
                       var instockbtch = document.getElementById('txtstockbtch').value;
                       var instockquty = document.getElementById('txtstockquty').value;
                       var indrugconc = document.getElementById('hiddrugconc').value;
                       var indrugsgna = document.getElementById('hiddrugsigna').value;
                       var indrugusag = document.getElementById('txtdrugusage').value;
                       input(indrugcode,inpaymmode,inpaymamnt,inpaymdisc,instockcode,instockpric,instockbtch,instockquty,indrugconc, indrugsgna, indrugusag);
                    }
        ">Input</a>

                  <a class="btn-modern btn-pay"
                    onclick="javascript: if (document.getElementById('txtdrugcode').value.length == 14)
                                  {
                                    if (confirm ('Are You Sure To Create Order ?'))
                                        {
                                          document.frmtrxadrug.submit();
                                        }
                                    else
                                        {
                                          alert('Harap Lengkap isian Form Order');
                                          document.getElementById('txtstockcode').focus();
                                        }
                                  }
        ">Bayar</a>
                </div>
              </div>

              <div class="card-modern">
                <div class="card-title">Daftar Item</div>
                <div id="tblscreen"></div>
              </div>

              <div id="tblregi" class="dropdown-panel"></div>

            </form>

          </div>
        </div>
      </div>
    </div>

    <script src="js/TRXADRUG02.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/ui.js"></script>
  </body>

  </html>
  <?php
} else {
  header("Location: " . "signin.php");
}
?>
