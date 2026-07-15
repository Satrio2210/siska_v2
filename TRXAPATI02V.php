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

<style>
  /* ============================================
     TRXAPATI02V - Tabel Daftar Pasien
     Responsif, tanpa scroll horizontal
     ============================================ */

  /* === TABLE CONTAINER === */
  .table-container {
    overflow: auto;
    border-radius: 16px;
    max-height: 420px;
    border: 1px solid #e2e8f0;
  }

  /* === TABLE BASE === */
  #screen {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
  }

  /* === TABLE HEADER === */
  #screen thead {
    background: #10b981;
    color: white;
    position: sticky;
    top: 0;
    z-index: 10;
  }

  .table-pasien #screen th {
    padding: 10px 6px;
    font-size: 11px;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
    border: none;
    letter-spacing: 0.3px;
  }

  /* === TABLE CELLS === */
  .table-pasien #screen td {
    padding: 8px 6px;
    font-size: 12px;
    border-bottom: 1px solid #edf2f7;
    text-align: center;
    vertical-align: middle;
    overflow-wrap: break-word;
    word-wrap: break-word;
  }

  /* === COLUMN WIDTHS (total: 100%) === */
  /* TERDAFTAR */
  #screen th:nth-child(1),
  #screen td:nth-child(1) { width: 13%; }

  /* ANTRIAN */
  #screen th:nth-child(2),
  #screen td:nth-child(2) { width: 6%; }

  /* PASIEN */
  #screen th:nth-child(3),
  #screen td:nth-child(3) { width: 24%; text-align: center; }

  /* TIPE */
  #screen th:nth-child(4),
  #screen td:nth-child(4) { width: 7%; }

  /* DOKTER */
  #screen th:nth-child(5),
  #screen td:nth-child(5) { width: 18%; }

  /* STATUS */
  #screen th:nth-child(6),
  #screen td:nth-child(6) { width: 7%; }

  /* ACTION */
  #screen th:nth-child(7),
  #screen td:nth-child(7) { width: 25%; }

  /* === ROW HOVER === */
  #screen tr { transition: .2s; }
  #screen tbody tr:hover { background: #d0e3f7; }

  /* === COMPACT DATE DISPLAY (2 baris dalam 1 cell) === */
  .regi-date-label {
    display: block;
    font-size: 10px;
    color: #64748b;
    line-height: 1.2;
  }

  .regi-date-value {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: #1e293b;
    line-height: 1.3;
  }

  /* === BADGES === */
  .badge {
    padding: 4px 8px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
    display: inline-block;
    white-space: nowrap;
  }

  .badge-antri { background: #fef3c7; color: #92400e; }
  .badge-periksa { background: #dbeafe; color: #1d4ed8; }
  .badge-bayar { background: #dcfce7; color: #166534; }
  .badge-selesai { background: #bbf7d0; color: #166534; }

  /* === ACTION BUTTONS === */
  .btn-action {
    border: none;
    border-radius: 8px;
    padding: 5px 8px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
    transition: .2s;
    margin-right: 3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    line-height: 1.2;
  }

  .btn-action:hover { transform: translateY(-1px); }
  .btn-update { background: #10b981; color: white; }
  .btn-print { background: #0ea5e9; color: white; }
  .btn-delete { background: #ef4444; color: white; }

  /* === BPJS highlight === */
  .paym-bpjs {
    background-color: #e0f7ff;
    font-weight: 600;
  }

  /* === RESPONSIVE: layar lebih kecil === */
  @media (max-width: 1200px) {
    .table-pasien #screen th { font-size: 10px; padding: 8px 4px; }
    .table-pasien #screen td { font-size: 11px; padding: 6px 4px; }
    .btn-action { padding: 4px 5px; font-size: 10px; margin-right: 2px; }
    .regi-date-label { font-size: 9px; }
    .regi-date-value { font-size: 10px; }
    .badge { padding: 3px 6px; font-size: 9px; }
  }
</style>
<link rel="stylesheet" href="assets/css/modern-table.css">


<div class="table-container table-pasien">
  <table id="screen" class="modern-table">
    <thead>
      <tr>
        <th>TERDAFTAR</th>
        <th>ANTRIAN</th>
        <th>PASIEN</th>
        <th>TIPE</th>
        <th>DOKTER</th>
        <th>STATUS</th>
        <th>ACTION</th>
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
          AND r.TRXA_ENTR_DATE > DATE_SUB(CURDATE(), INTERVAL 30 DAY)";

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
          $regicode  = $row['TRXA_REGI_CODE'];
          $kodePoli  = $row['TRXA_REGI_POLI'];
          $prefix    = $prefixMap[$kodePoli] ?? '';
          $noantri   = $prefix . $row['TRXA_REGI_LIST'];
          $paymCode  = $row['TRXA_REGI_PAYM'];
          $paymLabel = $paymentLabels[$paymCode] ?? 'Lainnya';
          $statCode  = $row['TRXA_REGI_STAT'];

          // Escape output untuk mencegah XSS
          $patiName  = htmlspecialchars($row['PATI_NAME'] ?? 'Pasien tidak ditemukan', ENT_QUOTES);
          $doctName  = htmlspecialchars($row['DOCT_NAME'] ?? '-', ENT_QUOTES);
          $poliName  = htmlspecialchars($row['POLI_NAME'] ?? '', ENT_QUOTES);
          $regiDate  = htmlspecialchars($row['REGI_DATE'], ENT_QUOTES);
          $entrTime  = htmlspecialchars(substr($row['TRXA_ENTR_TIME'], 0, 5), ENT_QUOTES);
          $escCode   = htmlspecialchars($regicode, ENT_QUOTES);

          echo '<tr>';

          // --- KOLOM 1: TERDAFTAR (format 2 baris agar compact) ---
          $tanggalDaftar = $row['TRXA_REGI_DATE'];
          echo '<td>';
          if ($tanggalDaftar == $datenow) {
              echo '<span class="regi-date-label">Hari ini</span>';
          } else {
              $hariLalu = hitungTanggal($tanggalDaftar, $datenow);
              echo '<span class="regi-date-label">' . $hariLalu . ' hari lalu</span>';
          }
          echo '<span class="regi-date-value">' . $regiDate . ' ' . $entrTime . '</span>';
          echo '</td>';

          // --- KOLOM 2: ANTRIAN ---
          echo '<td><strong>' . htmlspecialchars($noantri, ENT_QUOTES) . '</strong></td>';

          // --- KOLOM 3: PASIEN ---
          echo '<td>' . $patiName . '</td>';

          // --- KOLOM 4: TIPE PEMBAYARAN ---
          if ($paymCode === 'B') {
              echo '<td class="paym-bpjs">' . $paymLabel . '</td>';
          } else {
              echo '<td>' . $paymLabel . '</td>';
          }

          // --- KOLOM 5: DOKTER ---
          echo '<td>' . $doctName . '</td>';

          // --- KOLOM 6: STATUS ---
          echo '<td>';
          if ($statCode === 'W') {
              echo '<span class="badge badge-antri">Antri</span>';
          } elseif ($statCode === 'C' && $paymCode === 'U') {
              echo '<span class="badge badge-periksa">Periksa</span>';
          } elseif ($statCode === 'P') {
              echo '<span class="badge badge-bayar">Bayar</span>';
          } elseif ($statCode === 'C' && $paymCode === 'B') {
              echo '<span class="badge badge-selesai">Selesai</span>';
          } else {
              echo '<span class="badge badge-selesai">No Status</span>';
          }
          echo '</td>';

          // --- KOLOM 7: ACTION ---
          echo '<td>';

          // Tombol Update
          echo '<button class="btn-action btn-update" onclick="'
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
          echo '<a class="btn-action btn-print" href="' . htmlspecialchars($printUrl, ENT_QUOTES) . '" target="_blank">Antrian</a>';

          // Tombol Closing
          if ($statCode === 'C') {
              echo '<a class="btn-action btn-delete" onclick="alert(\'Pemeriksaan Belum lengkap ?\');">Closing</a>';
          } else {
              echo '<a class="btn-action btn-delete" onclick="'
                  . "if(confirm('Are You Sure To Delete ?')){"
                  . "hapuscode('" . $escCode . "');"
                  . "}else{"
                  . "document.getElementById('txtsearch').focus();"
                  . "}"
                  . '">Closing</a>';
          }

          echo '</td>';
          echo '</tr>';
      }
      ?>
    </tbody>
  </table>
</div>








