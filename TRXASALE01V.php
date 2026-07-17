<?php
include "conf/config.php";
include "inc/sanie.php";
?>
<link rel="stylesheet" href="assets/css/modern-table.css">
<div class="table-wrapper">
  <table id="screen" class="modern-table">
    <thead>
      <tr>
        <th>Tgl. Daftar</th>
        <th>No. Antrian</th>
        <th>Poli</th>
        <th>Nama Pasien</th>
        <th>Pembayaran</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $kata = $_POST['q'];
      //$kata = '';
      $panjangkata = strlen($kata);

      $xquery = "SELECT 
            t.TRXA_REGI_CODE, 
            t.TRXA_PATI_CODE, 
            CONCAT(p.PATI_MAIN_TITL, ' ', p.PATI_MAIN_NAME) AS PATI_NAME,
            p.PATI_MAIN_GEND AS MAIN_GEND, 
            t.TRXA_REGI_LIST, 
            t.TRXA_REGI_DATE, 
            t.TRXA_REGI_PAYM, 
            t.TRXA_REGI_DOCT, 
            u.PASS_USER_NAME AS REGI_DOCT,
            t.TRXA_REGI_POLI, 
            pl.TBLA_POLI_NAME AS REGI_POLI,
            t.TRXA_ENTR_DATE,
            t.TRXA_REGI_STAT,
            (SELECT COUNT(*) FROM trxasale WHERE TRXA_REGI_CODE = t.TRXA_REGI_CODE AND TRXA_VIEW_STAT = 'Y') AS SUDAH_BAYAR,
            (SELECT TRXA_SALE_CODE FROM trxasale WHERE TRXA_REGI_CODE = t.TRXA_REGI_CODE AND TRXA_VIEW_STAT = 'Y' ORDER BY TRXA_SALE_CODE DESC LIMIT 1) AS SALES_CODE
        FROM trxaregi t
        LEFT JOIN patimast p ON p.PATI_MAST_CODE = t.TRXA_PATI_CODE
        LEFT JOIN passiden u ON u.PASS_USER_IDEN = t.TRXA_REGI_DOCT
        LEFT JOIN tblapoli pl ON pl.TBLA_POLI_CODE = t.TRXA_REGI_POLI
        WHERE t.TRXA_REGI_STAT IN ('C', 'P')
          AND t.TRXA_REGI_PAYM IN ('U', 'B') 
          AND DATE(t.TRXA_ENTR_DATE) >= DATE_SUB(CURDATE(), INTERVAL 5 DAY)
    ";

      if ($panjangkata > 0) {
        $xquery .= " AND p.PATI_MAIN_NAME LIKE '%$kata%' ";
      }

      $xquery .= " ORDER BY t.TRXA_ENTR_DATE DESC, t.TRXA_ENTR_TIME DESC";

      $prefixMap = [
        'PU' => 'A', // Poli Umum
        'PG' => 'B', // Poli Gigi
        'KB' => 'C', // Poli KIA
        'LB' => 'D', // Laboratorium
      ];

      $namaPoliMap = [
        'PU' => 'Poli Umum',
        'PG' => 'Poli Gigi',
        'KB' => 'Poli KIA',
        'LB' => 'Laboratorium',
      ];

      $q = $db->query($xquery) or die("Gagal Maning!!");
      while ($k = $q->fetch(PDO::FETCH_ASSOC)) {

        echo '<tr>';
        $regicode = $k['TRXA_REGI_CODE'];
        $paticode = $k['TRXA_PATI_CODE'];
        $salescode = $k['SALES_CODE'];

        $regidate = date("d-m-Y", strtotime($k['TRXA_ENTR_DATE']));
        echo '<td>' . $regidate . '</td>';

        // hitung nomor antrian full (A001, B005, dst)
        $kodePoli = $k['TRXA_REGI_POLI'];       // misal: PU / PG / PK / LB
        $prefix = isset($prefixMap[$kodePoli]) ? $prefixMap[$kodePoli] : '';
        $noantri_full = $prefix . $k['TRXA_REGI_LIST'];

        echo '<td>' . $noantri_full . '</td>';

        // nama poli buat suara
        $namapoli = isset($namaPoliMap[$kodePoli]) ? $namaPoliMap[$kodePoli] : 'Poli';
        echo '<td>' . $namapoli . '</td>';

        $pati_name = htmlspecialchars($k['PATI_NAME'], ENT_QUOTES, 'UTF-8');
        echo '<td style="text-align: left;">' . $pati_name . '</td>';

        $regipaym = $k['TRXA_REGI_PAYM'];
        if ($regipaym == 'U') {
          echo '<td><span class="pay-umum">Umum</span></td>';
        } else if ($regipaym == 'B') {
          echo '<td><span class="pay-bpjs">BPJS</span></td>';
        } else if ($regipaym == 'A') {
          echo '<td><span class="pay-bpjs">Asuransi</span></td>';
        } else if ($regipaym == 'P') {
          echo '<td><span class="pay-umum">Perusahaan</span></td>';
        } else if ($regipaym == 'H') {
          echo '<td><span class="pay-bpjs">Halodoc</span></td>';
        } else {
          echo '<td>' . $regipaym . '</td>';
        }

        // Sudah bayar = ada row di trxasale ATAU status registrasi = P (BPJS CLOSE tanpa trxasale)
        $sudah_bayar = ((int) $k['SUDAH_BAYAR'] > 0 || $k['TRXA_REGI_STAT'] === 'P');
        if ($sudah_bayar) {
          echo '<td><span class="status-badge status-lunas">Sudah Bayar</span></td>';
        } else {
          echo '<td><span class="status-badge status-belum">Belum Bayar</span></td>';
        }

        echo '<td><div class="action-group">';

        // Panggil (selalu aktif)
        $btn_panggil = '<a class="button-panggil pure-button"
          data-noantri="' . $noantri_full . '"
          data-nama="' . htmlspecialchars($pati_name, ENT_QUOTES, 'UTF-8') . '"
          data-poli="' . $namapoli . '"
          data-channel="SALE">Panggil</a>';

        if ($sudah_bayar) {
          // LOGIKA JIKA SUDAH BAYAR: Periksa (Disabled), Panggil, Print (Aktif jika ada sales)
          echo '<a type="button" class="button-view pure-button button-disabled">Periksa</a>';
          echo $btn_panggil;
          if (!empty($salescode)) {
            echo '<a type="button" class="button-view pure-button" onclick="location.href=\'TRXASALE02P.php?regicode=' . $regicode . '&salecode=' . $salescode . '\';">Print</a>';
          } else {
            // BPJS CLOSE: tidak ada kwitansi nominal, print disabled
            echo '<a type="button" class="button-view pure-button button-disabled">Print</a>';
          }
        } else {
          // LOGIKA JIKA BELUM BAYAR: Periksa (Aktif), Panggil, Print (Disabled)
          echo '<a type="button" class="button-view pure-button" onclick="location.href=\'TRXASALE01.php?regicode=' . $regicode . '&paticode=' . $paticode . '\';">Periksa</a>';
          echo $btn_panggil;
          echo '<a type="button" class="button-view pure-button button-disabled">Print</a>';
        }
        echo '</div></td>';
        echo '</tr>';
      }

      ?>
    </tbody>
  </table>
</div>