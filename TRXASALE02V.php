<link rel="stylesheet" href="assets/css/modern-table.css">
<?php
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE);
include "conf/config.php";
?>

<div class="table-wrapper">
  <table class="modern-table">
    <thead>
      <tr>
        <th>Tgl Bayar</th>
        <th>Poli</th>
        <th>Nama pasien</th>
        <th>Pembayaran</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody>
      <?php
      // 1. Tangkap input, hapus spasi berlebih
      $kata = trim($_POST['q'] ?? '');
      $panjangkata = strlen($kata);

      // 2. Query utama pakai JOIN agar ringan
      $sql = "SELECT 
                s.TRXA_SALE_CODE, 
                s.TRXA_REGI_CODE AS REGI_CODE, 
                s.TRXA_PATI_CODE, 
                s.TRXA_REGI_DOCT, 
                d.PASS_USER_NAME AS REGI_DOCT,
                CONCAT(p.PATI_MAIN_TITL, ' ', p.PATI_MAIN_NAME) AS PATI_NAME,
                p.PATI_MAIN_GEND AS MAIN_GEND,
                s.TRXA_REGI_POLI, 
                pl.TBLA_POLI_NAME AS REGI_POLI,
                s.TRXA_PAYM_MODE, 
                s.TRXA_ENTR_DATE, 
                s.TRXA_ENTR_TIME,
                r.TRXA_REGI_PAYM AS REGI_PAYM,
                r.TRXA_REGI_STAT -- Ditambahkan agar $statuspay di bawah bisa berfungsi
              FROM trxasale s
              LEFT JOIN passiden d ON d.PASS_USER_IDEN = s.TRXA_REGI_DOCT
              LEFT JOIN patimast p ON p.PATI_MAST_CODE = s.TRXA_PATI_CODE
              LEFT JOIN tblapoli pl ON pl.TBLA_POLI_CODE = s.TRXA_REGI_POLI
              LEFT JOIN trxaregi r ON r.TRXA_REGI_CODE = s.TRXA_REGI_CODE
              WHERE s.TRXA_VIEW_STAT = 'Y' 
                AND s.TRXA_ENTR_DATE >= DATE_SUB(CURDATE(), INTERVAL 9 DAY)";

      $params = [];

      // 3. Logika filter pencarian
      // Jika tidak ada pencarian (atau panjang kata = 1 sesuai logic asli)
      if (empty($kata) || $panjangkata == 1) {
        $sql .= " ORDER BY s.TRXA_ENTR_DATE DESC, s.TRXA_ENTR_TIME DESC LIMIT 10";
      } else {
        $sql .= " AND (p.PATI_MAIN_NAME LIKE :kata OR s.TRXA_SALE_CODE LIKE :kata)
                  ORDER BY s.TRXA_ENTR_DATE DESC, s.TRXA_ENTR_TIME DESC";
        $params[':kata'] = "%{$kata}%";
      }

      try {
        // 4. Eksekusi query dengan PDO prepare (Aman dari SQL Injection)
        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        // 5. Looping data
        while ($k = $stmt->fetch(PDO::FETCH_ASSOC)) {
          echo '<tr>';

          $salecode = $k['TRXA_SALE_CODE'];
          $regicode = $k['REGI_CODE'];
          $paticode = $k['TRXA_PATI_CODE'];
          $paiddate = $k['TRXA_ENTR_DATE'];
          $paidtime = $k['TRXA_ENTR_TIME'];

          // Kolom Tgl, Poli, Nama
          echo '<td>' . htmlspecialchars($paiddate) . '<br>' . htmlspecialchars($paidtime) . '</td>';
          echo '<td>' . htmlspecialchars($k['REGI_POLI'] ?? '-') . '</td>';
          echo '<td>' . htmlspecialchars($k['PATI_NAME'] ?? 'Unknown') . '</td>';

          // Kolom Pembayaran
          $regipaym = $k['REGI_PAYM'] ?? '';
          if ($regipaym == 'U') {
            echo '<td> Umum </td>';
          } else if ($regipaym == 'B') {
            echo '<td> BPJS </td>';
          } else if ($regipaym == 'A') {
            echo '<td> Asuransi </td>';
          } else if ($regipaym == 'P') {
            echo '<td> Perusahaan </td>';
          } else if ($regipaym == 'H') {
            echo '<td> Halodoc </td>';
          } else {
            echo '<td> - </td>';
          }

          // Kolom Status (Gua kasih ?? operator biar bebas dari notice)
          $regiStat = $k['TRXA_REGI_STAT'] ?? '';
          $statuspay = ($regiStat === 'P');

          if ($statuspay) {
            echo '<td><span class="status-badge status-lunas">Sudah Bayar</span></td>';
          } else {
            echo '<td><span class="status-badge status-belum">Belum Bayar</span></td>';
          }

          // Kolom Action
          ?>
          <td>
            <div class="action-group">
              <button type="button" class="button-print" style="width: 70px;"
                onClick="javascript: location.href ='TRXASALE02P.php?regicode=<?php echo urlencode($regicode); ?>&salecode=<?php echo urlencode($salecode); ?>'">Print</button>
            </div>
          </td>
          <?php
          echo '</tr>';
        }
      } catch (PDOException $e) {
        // Tampilkan pesan jika ada error dari database
        echo '<tr><td colspan="6" style="text-align:center; color:red;">Gagal memuat data: ' . $e->getMessage() . '</td></tr>';
      }
      ?>
    </tbody>
  </table>
</div>