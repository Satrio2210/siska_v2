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
    <title>Pembayaran Pasien</title>
    <link rel="shortcut icon" href="assets/img/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="assets/css/layouts/header.css">
    <link rel="stylesheet" href="assets/css/layouts/side-menu.css">
    <link rel="stylesheet" href="assets/css/trxapati-shared.css">

  </head>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="js/jquery.js"></script>
  <script type="text/javascript" src="js/sanie.js"></script>
  <script src="js/sweetalert.min.js"></script>

  <script>
    $(document).ready(function () {
      setInterval(timestamp, 1000);
    });
    function timestamp() { $.ajax({ url: 'inc/timestamp.php', success: function (data) { $('#timestamp').html(data); }, }); }
  </script>

  <body onLoad="periksaakses('PASS_SALE_ENTR'); 
">
    <div id="wrapper">
      <!-- Side Menu -->
      <?php include "inc/side-menu.php"; ?>

      <!-- tampilan menu -->
      <div id="content-wrapper">
        <?php include "inc/header.php"; ?>
        <div class="content">

          <form name="frmtrxasale" class="pure-form pure-form-aligned" method="post" action="TRXASALE01P.php">
            <div class="content-modern">
              <div class="card-modern">
                <div class="card-title">Pembayaran</div>

                <input type="hidden" name="hidregicode" id="hidregicode">
                <input type="hidden" name="hidpaticode" id="hidpaticode">
                <input type="hidden" name="hidregidoct" id="hidregidoct">
                <input type="hidden" name="hidregipoli" id="hidregipoli">
                <input type="hidden" name="hidregipaym" id="hidregipaym">
                <input type="hidden" name="hidpaymtota" id="hidpaymtota">
                <input type="hidden" name="hidsalemode" id="hidsalemode" value="BAYAR">
                <input type="hidden" name="txtpaymdisc" id="txtpaymdisc" value="0">

                <div class="form-grid-5">
                  <!-- style="display: flex; justify-content: flex-end; gap: 12px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 16px;"> -->
                  <div class="form-group">
                    <label class="form-label" for="txtpaymtota">Total Tagihan</label>
                    <input type="text" name="txtpaymtota" id="txtpaymtota" maxlength="20" class="form-control"
                      readonly="true">
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="txtpaymkemb">Kembalian</label>
                    <input type="text" name="txtpaymkemb" id="txtpaymkemb" maxlength="20" class="form-control"
                      readonly="true" value="0">
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="optpaymmode">Cara Bayar</label>
                    <select name="optpaymmode" id="optpaymmode" class="form-control" disabled
                      onchange="document.getElementById('optpaymmode').focus();">
                      <option value="TUN">Tunai/Cash</option>
                      <option value="BCA">Debit BCA</option>
                      <option value="MAN">Debit Mandiri</option>
                      <option value="BNI">Debit BNI</option>
                      <option value="BRI">Debit BRI</option>
                      <option value="BCM">Transfer BCA</option>
                      <option value="MAM">Transfer Mandiri</option>
                      <option value="BIM">Transfer BNI</option>
                      <option value="BRM">Transfer BRI</option>
                      <option value="qrBCA">Qris BCA</option>
                      <option value="qrMAN">Qris Mandiri</option>
                      <option value="qrBNI">Qris BNI</option>
                      <option value="qrBRI">Qris BRI</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label class="form-label" for="txtpaymamnt">Jumlah Bayar</label>
                    <input type="text" name="txtpaymamnt" id="txtpaymamnt" autocomplete="off" maxlength="20"
                      class="form-control" placeholder="0" disabled onkeyup="hitungKembalian()" onkeydown="if (event.keyCode == 13 && value.length > 0) 
                        {
                          if (isNaN(this.value)) 
                            {
                              document.getElementById('txtpaymamnt').value = '0';
                              document.getElementById('txtpaymamnt').focus();                          
                            }
                          else
                            {
                              var inRupiah = convertToRupiah(this.value);
                              document.getElementById('txtpaymamnt').value = inRupiah;
                              document.getElementById('optpaymmode').focus();
                            }
                        }" onclick="if (isNaN(this.value)) 
                        {
                          var inAngka = convertToAngka(this.value);
                          document.getElementById('txtpaymamnt').value = inAngka;
                          document.getElementById('txtpaymamnt').focus();
                        }
                      ">
                  </div>

                  <div class="form-group">
                    <label class="form-label">Action</label>
                    <button type="button" class="btn-modern btn-save" id="btnSaleAction" style="height: 38px;"
                      onclick="handleSaleAction();">Bayar!</button>
                  </div>
                </div>

                <div class="form-grid-2 pati">
                  <div class="card-exam">
                    <div class="card-title">Data Pasien</div>
                    <div class="form-grid">
                      <div class="form-group">
                        <label class="form-label" for="txtregidate">Tgl Daftar</label>
                        <input type="text" name="txtregidate" id="txtregidate" class="form-control" readonly>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="txtregicode">No. RM</label>
                        <input type="text" name="txtregicode" id="txtregicode" class="form-control" readonly>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="txtpatiname">Nama</label>
                        <input type="text" name="txtpatiname" id="txtpatiname" class="form-control" readonly>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="txtpatigend">Gender</label>
                        <input type="text" name="txtpatigend" id="txtpatigend" class="form-control" readonly>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="txtpatibirt">Tanggal Lahir</label>
                        <input type="text" name="txtpatibirt" id="txtpatibirt" class="form-control" readonly>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="txtregipaymdisp">Pembayaran</label>
                        <input type="text" name="txtregipaymdisp" id="txtregipaymdisp" class="form-control" readonly>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="txtregidoctdisp">Dokter</label>
                        <input type="text" name="txtregidoctdisp" id="txtregidoctdisp" class="form-control" readonly>
                      </div>
                      <div class="form-group">
                        <label class="form-label" for="txtregipolidisp">Poli</label>
                        <input type="text" name="txtregipolidisp" id="txtregipolidisp" class="form-control" readonly>
                      </div>
                    </div>
                  </div>

                  <div class="card-exam" style="display: flex; flex-direction: column;">
                    <div class="card-title">Invoice</div>
                    <div id="tblviewinvc" style="flex: 1; overflow: auto;">
                    </div>
                  </div>
                </div>

                <div>
                  <button type="button" class="btn-modern btn-refresh" style="width: 100px; height: 38px;"
                    onclick="window.location.href='TRXASALE00.php'"><i
                      class="bi bi-arrow-left-short fs-5"></i>Kembali</button>
                </div>

              </div>
            </div>
          </form>


        </div><!-- div content -->
        <div class="footerdate">
          <span class="labelTime Time"><b>Date :</b> <?php $tgl = date('d-m-Y');
          echo $tgl; ?></span>
        </div>
        <div class="footertime">
          <span class="labelTime Time" id="timestamp"></span>
        </div>


      </div>
    </div>
    <script src="js/TRXASALE01.js"></script>
    <script src="js/ui.js"></script>
  </body>

  </html>
  <?php
} else {
  header("Location: " . "signin.php");
}
?>