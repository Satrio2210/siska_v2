<?php
/**
 * Tampilan cetak HTML hasil lab dinamis (alternatif FPDF).
 * URL: print-labo05.php?laboregi=XXXX
 */
include 'conf/config.php';
include 'inc/sanie.php';
include 'inc/lab_filter_rujukan.php';

$laboregi = isset($_GET['laboregi']) ? xss_clean($_GET['laboregi']) : '';
if ($laboregi === '') {
    header('Location: TRXALABO05.php');
    exit;
}

$qh = $db->prepare("SELECT TRXA_REGI_CODE, TRXA_PATI_CODE, TRXA_REGI_DATE,
    (SELECT PATI_MAIN_NAME FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_NAME,
    (SELECT PATI_MAIN_GEND FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_GEND,
    (SELECT PATI_MAIN_BIRT FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_BIRT
    FROM trxaregi WHERE TRXA_REGI_CODE=:regi AND TRXA_VIEW_STAT='Y' LIMIT 1");
$qh->execute(array(':regi' => $laboregi));
$header = $qh->fetch(PDO::FETCH_ASSOC);
if (!$header) {
    echo 'Data registrasi tidak ditemukan';
    exit;
}

$gender = ($header['MAIN_GEND'] == 'M') ? 'Laki-laki' : (($header['MAIN_GEND'] == 'F') ? 'Perempuan' : '-');
$pati_umur = null;
$pati_gend = strtoupper(trim((string)$header['MAIN_GEND']));
if (!empty($header['MAIN_BIRT']) && $header['MAIN_BIRT'] !== '0000-00-00') {
    try {
        $lahir = new DateTime($header['MAIN_BIRT']);
        $today = new DateTime('today');
        $pati_umur = (int)$today->diff($lahir)->y;
    } catch (Exception $e) {
        $pati_umur = null;
    }
}
$qd = $db->prepare("SELECT h.ITEM_NAME, h.ITEM_HASIL, h.ITEM_SATUAN, h.ITEM_RUJUKAN, h.ITEM_NOTE,
    COALESCE(m.TBLF_MEDI_NAME, h.TRXA_MEDI_CODE, 'LAB') AS MEDI_NAME
    FROM trxalabo_detail_hasil h
    LEFT JOIN tblfmedi m ON m.TBLF_MEDI_CODE = h.TRXA_MEDI_CODE
    WHERE h.TRXA_LABO_REGI=:regi AND h.HASIL_VIEW_STAT='Y'
    ORDER BY h.TRXA_MEDI_CODE, h.ITEM_URUT, h.HASIL_ID");
$qd->execute(array(':regi' => $laboregi));
$rows = $qd->fetchAll(PDO::FETCH_ASSOC);
if ($rows) {
    foreach ($rows as &$r) {
        $r['ITEM_RUJUKAN'] = filter_lab_rujukan($r['ITEM_RUJUKAN'], $pati_umur, $pati_gend);
        $r['ITEM_HASIL'] = format_lab_hasil_flag($r['ITEM_HASIL'], $r['ITEM_RUJUKAN']);
    }
    unset($r);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Print Hasil Lab - <?php echo htmlspecialchars($laboregi); ?></title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; margin: 24px; color: #222; }
    h1 { text-align: center; font-size: 16px; margin-bottom: 16px; }
    .meta td { padding: 2px 8px 2px 0; vertical-align: top; }
    table.hasil { width: 100%; border-collapse: collapse; margin-top: 16px; }
    table.hasil th, table.hasil td { border: 1px solid #333; padding: 6px 8px; }
    table.hasil th { background: #e8e8e8; }
    .group { background: #f0f0f0; font-weight: bold; text-align: center; }
    .rujukan { white-space: pre-line; font-size: 11px; }
    @media print {
      .no-print { display: none; }
      body { margin: 10mm; }
    }
  </style>
</head>
<body>
  <div class="no-print" style="margin-bottom:12px;">
    <button onclick="window.print()">Cetak</button>
    <button onclick="window.close()">Tutup</button>
  </div>
  <h1>HASIL PEMERIKSAAN LABORATORIUM</h1>
  <table class="meta">
    <tr><td>Nomor</td><td>: <?php echo htmlspecialchars($header['TRXA_REGI_CODE'] . ' / ' . $header['TRXA_PATI_CODE']); ?></td></tr>
    <tr><td>Nama / JK</td><td>: <?php echo htmlspecialchars($header['MAIN_NAME'] . ' / ' . $gender); ?></td></tr>
    <tr><td>Tgl Daftar</td><td>: <?php echo htmlspecialchars($header['TRXA_REGI_DATE']); ?></td></tr>
  </table>
  <table class="hasil">
    <thead>
      <tr>
        <th>Item Pemeriksaan</th>
        <th>Hasil</th>
        <th>Satuan</th>
        <th>Nilai Normal</th>
        <th>Keterangan</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $lastGroup = null;
    if (!$rows) {
        echo '<tr><td colspan="5" style="text-align:center;">Belum ada hasil</td></tr>';
    } else {
        foreach ($rows as $r) {
            if ($lastGroup !== $r['MEDI_NAME']) {
                $lastGroup = $r['MEDI_NAME'];
                echo '<tr class="group"><td colspan="5">' . htmlspecialchars($lastGroup) . '</td></tr>';
            }
            echo '<tr>';
            echo '<td>' . htmlspecialchars($r['ITEM_NAME']) . '</td>';
            echo '<td>' . htmlspecialchars($r['ITEM_HASIL']) . '</td>';
            echo '<td>' . htmlspecialchars($r['ITEM_SATUAN']) . '</td>';
            echo '<td class="rujukan">' . htmlspecialchars($r['ITEM_RUJUKAN']) . '</td>';
            echo '<td>' . htmlspecialchars($r['ITEM_NOTE']) . '</td>';
            echo '</tr>';
        }
    }
    ?>
    </tbody>
  </table>
  <script>window.onload = function () { /* optional auto print: window.print(); */ };</script>
</body>
</html>
