<?php
include "conf/config.php";
include "inc/sanie.php";

// === Sanitize input pencarian ===
$kata = isset($_POST['q']) ? trim($_POST['q']) : '';

// === Mapping helpers ===
$prefixMap = [
  'PU' => 'A',  // Poli Umum
  'PG' => 'B',  // Poli Gigi
  'KB' => 'C',  // Poli KIA
  'LB' => 'D',  // Laboratorium
];

$paymentLabels = [
  'U' => 'Umum',
  'B' => 'BPJS',
  'A' => 'Asuransi',
  'P' => 'Perusahaan',
  'H' => 'Halodoc',
];
?>

<link rel="stylesheet" href="assets/css/modern-table.css">
<div class="table-wrapper">
  <table class="modern-table">
    <thead>
      <tr>
        <th class="w-10p">Tgl Daftar</th>
        <th class="w-10p">No. Antrian</th>
        <th>Poli</th>
        <th>Nama Pasien</th>
        <th class="w-10p">Pembayaran</th>
        <th class="w-10p">Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $baseSql = "SELECT
          r.TRXA_REGI_CODE,
          r.TRXA_REGI_DATE,
          DATE_FORMAT(r.TRXA_REGI_DATE, '%d/%m/%Y') AS REGI_DATE,
          CONCAT_WS(' ', p.PATI_MAIN_TITL, p.PATI_MAIN_NAME) AS PATI_NAME,
          r.TRXA_REGI_LIST,
          r.TRXA_REGI_PAYM,
          r.TRXA_ENTR_TIME,
          doc.PASS_USER_NAME AS DOCT_NAME,
          r.TRXA_REGI_POLI,
          poli.TBLA_POLI_NAME AS POLI_NAME,
          r.TRXA_REGI_STAT
        FROM trxaregi r
        LEFT JOIN patimast p ON p.PATI_MAST_CODE = r.TRXA_PATI_CODE
        LEFT JOIN passiden doc ON doc.PASS_USER_IDEN = r.TRXA_REGI_DOCT
        LEFT JOIN tblapoli poli ON poli.TBLA_POLI_CODE = r.TRXA_REGI_POLI
        WHERE r.TRXA_REGI_STAT IN ('W', 'C', 'P')
          AND r.TRXA_VIEW_STAT = 'Y'
          AND r.TRXA_ENTR_DATE > DATE_SUB(CURDATE(), INTERVAL 2 DAY)";

      if ($kata !== '') {
        // Search: gunakan prepared statement dengan parameter binding
        $sql = $baseSql . " AND p.PATI_MAIN_NAME LIKE :kata ORDER BY r.TRXA_REGI_LIST";
        $stmt = $db->prepare($sql);
        $stmt->execute([':kata' => '%' . $kata . '%']);
      } else {
        // Tampilkan semua (30 hari terakhir)
        $sql = $baseSql . " ORDER BY r.TRXA_ENTR_DATE DESC, r.TRXA_ENTR_TIME DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
      }

      // === Rendering loop ===
      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // --- Ambil & escape data ---
        $regicode = $row['TRXA_REGI_CODE'];
        $kodePoli = $row['TRXA_REGI_POLI'];
        $prefix = $prefixMap[$kodePoli] ?? '';
        $noantri = $prefix . $row['TRXA_REGI_LIST'];
        $paymCode = $row['TRXA_REGI_PAYM'];
        $paymLabel = $paymentLabels[$paymCode] ?? 'Lainnya';
        $statCode = $row['TRXA_REGI_STAT'];

        // Escape output untuk mencegah XSS
        $patiName = htmlspecialchars($row['PATI_NAME'] ?? 'Pasien tidak ditemukan', ENT_QUOTES);
        $doctName = htmlspecialchars($row['DOCT_NAME'] ?? '-', ENT_QUOTES);
        $poliName = htmlspecialchars($row['POLI_NAME'] ?? '', ENT_QUOTES);
        $regiDate = htmlspecialchars($row['REGI_DATE'], ENT_QUOTES);
        $entrTime = htmlspecialchars($row['TRXA_ENTR_TIME'], ENT_QUOTES);
        $escCode = htmlspecialchars($regicode, ENT_QUOTES);

        echo '<tr>';

        $tanggalDaftar = $row['TRXA_REGI_DATE'];
        echo '<td class="w-10p">';
        if ($tanggalDaftar == $datenow) {
          echo '<span class="regi-date-label">Hari ini</span><br>';
        } else {
          $hariLalu = hitungTanggal($tanggalDaftar, $datenow);
          echo '<span class="regi-date-label">' . $hariLalu . ' hari lalu</span><br>';
        }
        echo '<span>' . $regiDate . '<br>' . $entrTime . '</span>';
        echo '</td>';

        // --- KOLOM 2: ANTRIAN ---
        echo '<td class="w-10p"><strong>' . htmlspecialchars($noantri, ENT_QUOTES) . '</strong></td>';

        // --- KOLOM 3: POLI ---
        echo '<td>' . $poliName . '<br>' . $doctName . '</td>';

        // --- KOLOM 4: PASIEN ---
        echo '<td>' . $patiName . '</td>';

        // --- KOLOM 5: TIPE PEMBAYARAN ---
        echo '<td class="w-10p">';
        if ($paymCode === 'B') {
          echo '<span class="pay-bpjs">' . $paymLabel . '</span>';
        } else {
          echo '<span class="pay-umum">' . $paymLabel . '</span>';
        }
        echo '</td>';

        // --- KOLOM 6: STATUS ---
        echo '<td class="w-10p">';
        if ($statCode === 'W') {
          echo '<span class="status-badge status-wait">Antri</span>';
        } elseif ($statCode === 'C' && $paymCode === 'U') {
          echo '<span class="status-badge status-process">Periksa</span>';
        } elseif ($statCode === 'P') {
          echo '<span class="status-badge status-done">Bayar</span>';
        } elseif ($statCode === 'C' && $paymCode === 'B') {
          echo '<span class="status-badge status-selesai">Selesai</span>';
        } else {
          echo '<span class="status-badge status-belum">No Status</span>';
        }
        echo '</td>';

        // --- KOLOM 7: ACTION ---
        echo '<td><div class="action-group">';

        // Tombol Update
        echo '<button type="button" class="button-view" onclick="'
          . "viewcode('" . $escCode . "');"
          . "setTimeout(function(){"
          . "document.getElementById('regidoct').scrollIntoView({behavior:'smooth',block:'start'});"
          . "document.getElementById('txtregidoct').focus();"
          . "},300);"
          . '">Update</button>';

        // Tombol Cetak Antrian
        $printUrl = 'print.php?nomor=' . urlencode($noantri)
          . '&pasien=' . urlencode($row['PATI_NAME'] ?? '')
          . '&layanan=' . urlencode($row['POLI_NAME'] ?? '');
        echo '<button type="button" class="button-print" href="' . htmlspecialchars($printUrl, ENT_QUOTES) . '" target="_blank">Antrian</button>';

        // Tombol Closing
        if ($statCode === 'C') {
          echo '<button type="button" class="button-delete" onclick="alert(\'Pemeriksaan Belum lengkap ?\');">Close</button>';
        } else {
          echo '<button type="button" class="button-delete" onclick="'
            . "if(confirm('Are You Sure To Delete ?')){"
            . "hapuscode('" . $escCode . "');"
            . "}else{"
            . "document.getElementById('txtsearch').focus();"
            . "}"
            . '">Close</button>';
        }

        echo '</div></td>';
        echo '</tr>';
      }
      ?>
    </tbody>
  </table>
</div>