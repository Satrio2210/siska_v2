<?php
include "conf/config.php";
?>
<style>
  .table-container {
    overflow: auto;
    border-radius: 16px;
    max-height: 420px;
    border: 1px solid #e2e8f0;
    margin-top: 10px;
  }

  #tblregi-farm-receipt {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
  }

  #tblregi-farm-receipt th,
  #tblregi-farm-receipt td {
    padding: 10px 20px;
  }

  #tblregi-farm-receipt thead {
    background: #0D9488;
    color: white;
    position: sticky;
    top: 0;
    z-index: 10;
  }

  #tblregi-farm-receipt th {
    font-size: 13px;
    font-weight: 700;
    text-align: center;
    vertical-align: middle;
    border: none;
    letter-spacing: 0.3px;
  }

  #tblregi-farm-receipt td {
    font-size: 12px;
    /* color: #374151; */
    text-align: center;
    vertical-align: middle;
    border-bottom: 1px solid #e5e7eb;
    overflow-wrap: break-word;
    word-break: break-word;
  }

  #tblregi-farm-receipt th:nth-child(1),
  #tblregi-farm-receipt td:nth-child(1) {
    width: 10%;
  }

  #tblregi-farm-receipt th:nth-child(2),
  #tblregi-farm-receipt td:nth-child(2) {
    width: 20%;
  }

  #tblregi-farm-receipt th:nth-child(3),
  #tblregi-farm-receipt td:nth-child(3) {
    width: 20%;
  }

  #tblregi-farm-receipt th:nth-child(4),
  #tblregi-farm-receipt td:nth-child(4) {
    width: 20%;
  }

  #tblregi-farm-receipt th:nth-child(5),
  #tblregi-farm-receipt td:nth-child(5) {
    width: 10%;
  }

  #tblregi-farm-receipt th:nth-child(6),
  #tblregi-farm-receipt td:nth-child(6) {
    width: 20%;
  }
</style>

<div class="table-container">
  <table id="tblregi-farm-receipt">
    <thead>
      <tr>
        <th>Antri</th>
        <th>Nama Pasien</th>
        <th>Poli</th>
        <th>Status</th>
        <th>Payment</th>
        <th>Aksi</th>
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
          $badgepay = '<span class="badge-pay badge-bpjs">BPJS</span>';
        } else if ($regipaym == 'Umum') {
          $badgepay = '<span class="badge-pay badge-umum">Umum</span>';
        } else if ($regipaym == 'Asuransi') {
          $badgepay = '<span class="badge-pay badge-asuransi">Asuransi</span>';
        } else {
          $badgepay = '<span class="badge-pay badge-perusahaan">Perusahaan</span>';
        }

        $cntresep = $k['CNT_RESEP'];
        if ($cntresep > 0) {
          $statusfarmasi = '<span class="badge-status badge-success">Sudah Dilayani</span>';
        } else {
          $statusfarmasi = '<span class="badge-status badge-warning">Belum Dilayani</span>';
        }

        $jsArgs = "'" . $outprsccode . "','" . $outpaticode . "','" . addslashes($outmainname) . "','" . $outmaingend . "','" . $outmainage . "','" . $outregipaym . "','" . $outpaymcode . "','" . $outregipoli . "','" . addslashes($outexamprsc) . "','" . addslashes($outexamdiag) . "'";

        echo '<tr>';
        echo '<td>' . $k['TRXA_REGI_LIST'] . '</td>';
        echo '<td>' . htmlspecialchars($k['MAIN_NAME']) . '</td>';
        echo '<td>' . $regipoli . '</td>';
        echo '<td>' . $badgepay . '</td>';
        echo '<td>' . $statusfarmasi . '</td>';
        echo '<td>';
        echo '<div  class="form-grid">';
        echo '<button type="button" class="btn-modern btn-save" onclick="isiregi(' . $jsArgs . ');">Periksa</button>';
        echo '<button type="button" class="btn-modern btn-refresh" href="TRXADRUG01.php?regicode=' . urlencode($outprsccode) . '">Penyerahan Obat</button>';
        echo '</div>';
        echo '</td>';
        echo '</tr>';
      }
      ?>
    </tbody>
  </table>
</div>