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
      $kata = isset($_POST['q']) ? trim($_POST['q']) : '';
      $page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
      $perpage = 5;
      $offset = ($page - 1) * $perpage;

      $where = "r.TRXA_REGI_STAT IN ('W', 'C', 'P')
          AND r.TRXA_VIEW_STAT = 'Y'
          AND r.TRXA_ENTR_DATE > DATE_SUB(CURDATE(), INTERVAL 2 DAY)";

      $params = [];
      if ($kata !== '') {
        // Search: gunakan prepared statement dengan parameter binding
        $where .= " AND p.PATI_MAIN_NAME LIKE :kata";
        $params[':kata'] = '%' . $kata . '%';
      }

      // Hitung total untuk pagination
      $countSql = "SELECT COUNT(*)
        FROM trxaregi r
        LEFT JOIN patimast p ON p.PATI_MAST_CODE = r.TRXA_PATI_CODE
        WHERE $where";
      $stmtCount = $db->prepare($countSql);
      $stmtCount->execute($params);
      $total = (int) $stmtCount->fetchColumn();
      $totalPages = max(1, (int) ceil($total / $perpage));
      if ($page > $totalPages) {
        $page = $totalPages;
        $offset = ($page - 1) * $perpage;
      }

      $order = ($kata !== '')
        ? "ORDER BY r.TRXA_REGI_LIST"
        : "ORDER BY r.TRXA_ENTR_DATE DESC, r.TRXA_ENTR_TIME DESC";

      $sql = "SELECT
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
        WHERE $where
        $order
        LIMIT $offset, $perpage";

      $stmt = $db->prepare($sql);
      foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
      }
      $stmt->execute();

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
        echo '<td class="w-10p">';
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

<?php if ($total > 0) { ?>
  <div class="table-pagination" id="tblscreenPagination">
    <?php
    $prev = $page - 1;
    $next = $page + 1;
    $prevClass = $page <= 1 ? 'disabled' : '';
    $nextClass = $page >= $totalPages ? 'disabled' : '';
    ?>
    <a href="#" class="<?php echo $prevClass; ?>" onclick="return tblScreenGo(event, <?php echo $prev; ?>);">&laquo;</a>

    <?php
    $start = max(1, $page - 2);
    $end = min($totalPages, $page + 2);
    if ($start > 1) {
      echo '<a href="#" onclick="return tblScreenGo(event, 1);">1</a>';
      if ($start > 2) {
        echo '<span>&hellip;</span>';
      }
    }
    for ($i = $start; $i <= $end; $i++) {
      if ($i === $page) {
        echo '<span class="active">' . $i . '</span>';
      } else {
        echo '<a href="#" onclick="return tblScreenGo(event, ' . $i . ');">' . $i . '</a>';
      }
    }
    if ($end < $totalPages) {
      if ($end < $totalPages - 1) {
        echo '<span>&hellip;</span>';
      }
      echo '<a href="#" onclick="return tblScreenGo(event, ' . $totalPages . ');">' . $totalPages . '</a>';
    }
    ?>

    <a href="#" class="<?php echo $nextClass; ?>" onclick="return tblScreenGo(event, <?php echo $next; ?>);">&raquo;</a>

    <span class="trx06-info"><?php echo $total; ?> pasien &middot; hlm
      <?php echo $page; ?>/<?php echo $totalPages; ?></span>
  </div>
<?php } ?>