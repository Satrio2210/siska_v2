<?php
include "conf/config.php";
?>
<link rel="stylesheet" href="assets/css/modern-table.css">
<div class="table-wrapper">
  <table class="modern-table">
    <thead>
      <tr>
        <th>Tgl Daftar</th>
        <th>No. Antrian</th>
        <th>Poli</th>
        <th>Nama Pasien</th>
        <th>Pembayaran</th>
        <th>Status</th>
        <th>action</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $kata = $_POST['q'];

      $xquery = " SELECT
        r.TRXA_REGI_CODE, 
        r.TRXA_PATI_CODE,
        CONCAT(p.PATI_MAIN_TITL, ' ', p.PATI_MAIN_NAME) AS MAIN_NAME,
        IFNULL(pr.CNT_RESEP, 0) AS CNT_RESEP,
        p.PATI_MAIN_BIRT AS MAIN_AGE,
        p.PATI_MAIN_GEND AS MAIN_GEND,
        r.TRXA_REGI_LIST, 
        r.TRXA_REGI_PAYM, 
        r.TRXA_REGI_POLI,
        r.TRXA_ENTR_DATE,
        r.TRXA_ENTR_TIME,
        e.TRXA_EXAM_PRSC AS EXAM_PRSC,
        d.DIAGNOSA
        FROM trxaregi r
        JOIN patimast p ON p.PATI_MAST_CODE = r.TRXA_PATI_CODE
        LEFT JOIN trxaexam e ON e.TRXA_EXAM_CODE = r.TRXA_REGI_CODE
        LEFT JOIN (
        SELECT TRXA_PRSC_CODE, COUNT(*) AS CNT_RESEP 
        FROM trxaprsc 
        WHERE TRXA_VIEW_STAT = 'Y' 
        GROUP BY TRXA_PRSC_CODE
        ) pr ON pr.TRXA_PRSC_CODE = r.TRXA_REGI_CODE
        LEFT JOIN (
        SELECT TRXA_EXAM_CODE, GROUP_CONCAT(TRXA_DIAG_NAME SEPARATOR ', ') AS DIAGNOSA 
        FROM trxadiag 
        GROUP BY TRXA_EXAM_CODE
        ) d ON d.TRXA_EXAM_CODE = r.TRXA_REGI_CODE
        WHERE r.TRXA_REGI_POLI <> '$code_lab_room' 
        AND r.TRXA_REGI_STAT = 'C'
        AND r.TRXA_ENTR_DATE > DATE_SUB(CURDATE(), INTERVAL 2 DAY)
        ";
      if (strlen($kata) != 1) {
        $xquery .= " AND p.PATI_MAIN_NAME LIKE '$kata%' ";
      }
      $xquery .= " ORDER BY r.TRXA_ENTR_DATE DESC, r.TRXA_ENTR_TIME DESC";

      $q = $db->query($xquery) or die("Gagal ambil regis !!");
      while ($k = $q->fetch(PDO::FETCH_ASSOC)) {
        $outprsccode = $k['TRXA_REGI_CODE'];
        $outpaticode = $k['TRXA_PATI_CODE'];
        $outmainname = $k['MAIN_NAME'];
        $mainage = $k['MAIN_AGE'];
        $outexamdiag = $k['DIAGNOSA'];
        $tgldaftard = $k['TRXA_ENTR_DATE'];
        $tgldaftart = $k['TRXA_ENTR_TIME'];

        $tanggal = new DateTime($mainage);
        $today = new DateTime('today');
        $y = $today->diff($tanggal)->y;
        $m = $today->diff($tanggal)->m;
        $d = $today->diff($tanggal)->d;
        $outmainage = '' . $y . ' tahun ' . $m . ' bulan ' . $d . ' hari';

        $gender = $k['MAIN_GEND'];
        if ($gender == 'M') {
          $outmaingend = 'Laki Laki';
        } else if ($gender == 'F') {
          $outmaingend = 'Perempuan';
        } else {
          $outmaingend = 'No Gender';
        }

        $outpaymcode = $k['TRXA_REGI_PAYM'];
        if ($outpaymcode == 'U') {
          $outregipaym = 'Umum';
        } else if ($outpaymcode == 'B') {
          $outregipaym = 'BPJS';
        } else if ($outpaymcode == 'A') {
          $outregipaym = 'Asuransi';
        } else if ($outpaymcode == 'P') {
          $outregipaym = 'Perusahaan';
        } else {
          $outregipaym = 'Kosong';
        }

        $outregipoli = $k['TRXA_REGI_POLI'];
        $inexamprsc = $k['EXAM_PRSC'];
        $outexamprsc = preg_replace("/[\r\n]*/", "", $inexamprsc);

        $regipoli = $k['TRXA_REGI_POLI'];
        if ($regipoli == 'PU') {
          $regipoli = 'Poli Umum';
        } else if ($regipoli == 'KB') {
          $regipoli = 'Poli KIA';
        } else if ($regipoli == 'PG') {
          $regipoli = 'Poli Gigi';
        } else if ($regipoli == 'LB') {
          $regipoli = 'Laboratorium';
        } else {
          $regipoli = 'Kosong';
        }

        $regipaym = $outregipaym;
        if ($regipaym == 'BPJS') {
          $badgepay = '<span class="status-badge pay-bpjs">BPJS</span>';
        } else if ($regipaym == 'Umum') {
          $badgepay = '<span class="status-badge pay-umum">Umum</span>';
        } else if ($regipaym == 'Asuransi') {
          $badgepay = '<span class="status-badge pay-umum">Asuransi</span>';
        } else {
          $badgepay = '<span class="status-badge pay-umum">Perusahaan</span>';
        }

        $cntresep = $k['CNT_RESEP'];
        if ($cntresep > 0) {
          $statusfarmasi = '<span class="status-badge status-done">Sudah Dilayani</span>';
        } else {
          $statusfarmasi = '<span class="status-badge status-wait">Belum Dilayani</span>';
        }

        $jsArgs = "'" . $outprsccode . "','" . $outpaticode . "','" . addslashes($outmainname) . "','" . $outmaingend . "','" . $outmainage . "','" . $outregipaym . "','" . $outpaymcode . "','" . $outregipoli . "','" . addslashes($outexamprsc) . "','" . addslashes($outexamdiag) . "'";

        echo '<tr>';
        echo '<td>' . $tgldaftard . '<br>' . $tgldaftart . '</td>';
        echo '<td>' . $k['TRXA_REGI_LIST'] . '</td>';
        echo '<td>' . $regipoli . '</td>';
        echo '<td>' . htmlspecialchars($k['MAIN_NAME']) . '</td>';
        echo '<td>' . $badgepay . '</td>';
        echo '<td>' . $statusfarmasi . '</td>';
        echo '<td>';
        echo '<div  class="action-group">';
        echo '<button type="button" class="button-view" onclick="isiregi(' . $jsArgs . ');">Periksa</button>';
        echo '<button type="button" class="button-print" href="TRXADRUG01.php?regicode=' . urlencode($outprsccode) . '">Siapkan</button>';
        echo '</div>';
        echo '</td>';
        echo '</tr>';
      }
      ?>
    </tbody>
  </table>
</div>