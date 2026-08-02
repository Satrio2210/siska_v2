<?php
include "conf/config.php";
include "inc/sanie.php";
?>

<link rel="stylesheet" href="assets/css/modern-table.css">
<div class="table-wrapper">
  <table class="modern-table">
    <thead>
      <tr>
        <th>Tgl Daftar</th>
        <th>No. Antrian</th>
        <th>Keterangan</th>
        <th>Nama Pasien</th>
        <th>Pembayaran</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $prefixMap = [
        'PU' => 'A',
        'PG' => 'B',
        'KB' => 'C',
        'LB' => 'D',
      ];

      $namaPoliMap = [
        'PU' => 'Poli Umum',
        'PG' => 'Poli Gigi',
        'KB' => 'Poli KIA',
        'LB' => 'Laboratorium',
      ];

      $paymMap = [
        'U' => 'Umum',
        'B' => 'BPJS',
        'A' => 'Asuransi',
        'P' => 'Perusahaan',
        'H' => 'Halodoc',
      ];

      $genderMap = [
        'M' => 'Laki Laki',
        'F' => 'Perempuan',
      ];

      try {
        $st = $db->prepare("SELECT
                r.TRXA_REGI_CODE,
                r.TRXA_PATI_CODE,
                r.TRXA_REGI_PAYM,
                r.TRXA_REGI_POLI,
                r.TRXA_REGI_LIST,
                r.TRXA_REGI_STAT,
                r.TRXA_REGI_DOCT,
                r.TRXA_ENTR_DATE,
                r.TRXA_ENTR_TIME,
                r.TRXA_REGI_RUJUK_TYPE,
                r.TRXA_REGI_RUJUK_NOTE,
                CONCAT(p.PATI_MAIN_TITL, ' ', p.PATI_MAIN_NAME) AS MAIN_NAME,
                DATE_FORMAT(p.PATI_MAIN_BIRT, '%d/%m/%Y') AS BIRT_DATE,
                p.PATI_MAIN_BIRT AS MAIN_BIRT_RAW,
                p.PATI_MAIN_GEND AS MAIN_GEND,
                (SELECT COUNT(*) FROM trxatret WHERE TRXA_TRET_CODE = r.TRXA_REGI_CODE AND TRXA_VIEW_STAT = 'Y') AS TOTA_TRET
            FROM trxaregi r
            LEFT JOIN patimast p ON p.PATI_MAST_CODE = r.TRXA_PATI_CODE
            WHERE r.TRXA_REGI_POLI = :poli_lab
              AND r.TRXA_VIEW_STAT = 'Y'
              AND r.TRXA_REGI_STAT IN ('W','C','P','X')
              AND r.TRXA_ENTR_DATE > DATE_SUB(CURDATE(), INTERVAL 5 DAY)
            ORDER BY r.TRXA_ENTR_DATE DESC, r.TRXA_ENTR_TIME DESC
            LIMIT 200
        ");
        $st->execute([':poli_lab' => $code_lab_room]);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
      } catch (PDOException $e) {
        echo '<tr><td colspan="8" class="lab00-empty">Tidak dapat memuat daftar pasien lab.</td></tr>';
        $rows = [];
      }

      if (count($rows) === 0) {
        echo '<tr><td colspan="8" class="lab00-empty">Tidak ada pasien laboratorium pada 5 hari terakhir.</td></tr>';
      }

      foreach ($rows as $k) {
        $outtretcode = $k['TRXA_REGI_CODE'];
        $outpaticode = $k['TRXA_PATI_CODE'];
        $outmainname = !empty($k['MAIN_NAME']) ? trim($k['MAIN_NAME']) : '-';
        $outbirtdate = !empty($k['BIRT_DATE']) ? $k['BIRT_DATE'] : '-';
        $mainage = $k['MAIN_BIRT_RAW'];

        $outmainage = '-';
        if (!empty($mainage) && $mainage !== '0000-00-00') {
          try {
            $tanggal = new DateTime($mainage);
            $today = new DateTime('today');
            $y = $today->diff($tanggal)->y;
            $m = $today->diff($tanggal)->m;
            $d = $today->diff($tanggal)->d;
            $outmainage = $y . ' tahun ' . $m . ' bulan ' . $d . ' hari';
          } catch (Exception $e) {
            $outmainage = '-';
          }
        }

        $gender = $k['MAIN_GEND'];
        $outmaingend = isset($genderMap[$gender]) ? $genderMap[$gender] : 'No Gender';

        $outtotaltret = $k['TOTA_TRET'];

        $kodePoli = $k['TRXA_REGI_POLI'];
        $regipoli = isset($namaPoliMap[$kodePoli]) ? $namaPoliMap[$kodePoli] : 'Kosong';
        $outregipoli = $k['TRXA_REGI_POLI'];

        $outpaymcode = $k['TRXA_REGI_PAYM'];
        $outregipaym = isset($paymMap[$outpaymcode]) ? $paymMap[$outpaymcode] : 'Kosong';

        $prefix = isset($prefixMap[$kodePoli]) ? $prefixMap[$kodePoli] : '';
        $noantri_full = $prefix . $k['TRXA_REGI_LIST'];

        $rujukType = $k['TRXA_REGI_RUJUK_TYPE'];
        $rujukNote = $k['TRXA_REGI_RUJUK_NOTE'];

        echo '<tr>';
        echo '<td>' . htmlspecialchars($k['TRXA_ENTR_DATE'], ENT_QUOTES, 'UTF-8') . '<br>' . htmlspecialchars($k['TRXA_ENTR_TIME'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($noantri_full, ENT_QUOTES, 'UTF-8') . '</td>';

        if ($rujukType === 'LB' && $rujukNote !== '') {
          $keterangan = 'Rujuk Internal<br>' . htmlspecialchars($rujukNote, ENT_QUOTES, 'UTF-8');
        } else if ($rujukType === 'LB') {
          $keterangan = 'Rujuk Internal';
        } else {
          $keterangan = 'Datang Sendiri';
        }
        echo '<td>' . $keterangan . '</td>';
        echo '<td>' . htmlspecialchars($outmainname, ENT_QUOTES, 'UTF-8') . '</td>';
        // echo '<td>' . htmlspecialchars($regipoli, ENT_QUOTES, 'UTF-8') . '</td>';
        if ($outregipaym === 'BPJS') {
          $statuspay = 'BPJS';
          $badgeClass = 'pay-bpjs';
        } else {
          $statuspay = 'Umum';
          $badgeClass = 'pay-umum';
        }
        echo '<td><span class="status-badge ' . $badgeClass . '">' . htmlspecialchars($statuspay, ENT_QUOTES, 'UTF-8') . '</span></td>';

        $registat = (string) $k['TRXA_REGI_STAT'];
        if ($registat === 'W') {
          $statusText = 'Antri';
          $badgeClass = 'status-wait';
        } elseif ($registat === 'C') {
          $statusText = 'Periksa';
          $badgeClass = 'status-process';
        } elseif ($registat === 'P') {
          $statusText = 'Bayar';
          $badgeClass = 'status-lunas';
        } elseif ($registat === 'X') {
          $statusText = 'Selesai';
          $badgeClass = 'status-done';
        } else {
          $statusText = $registat;
          $badgeClass = 'status-wait';
        }
        echo '<td><span class="status-badge ' . $badgeClass . '">' . htmlspecialchars($statusText, ENT_QUOTES, 'UTF-8') . '</span></td>';

        $regiParam = htmlspecialchars($outtretcode, ENT_QUOTES, 'UTF-8');
        $patiParam = htmlspecialchars($outpaticode, ENT_QUOTES, 'UTF-8');
        $patiParamJs = $patiParam;
        $periUrl = 'TRXALABO01.php?regicode=' . rawurlencode($outtretcode) . '&paticode=' . rawurlencode($outpaticode);
        $hasiUrl = 'TRXALABO05.php?regicode=' . rawurlencode($outtretcode) . '&paticode=' . rawurlencode($outpaticode);
        echo '<td>';
        echo '<div class="action-group">';
        echo '<a class="button-view" href="' . $periUrl . '">Periksa</a>';
        echo '<a class="button-panggil" href="' . $hasiUrl . '">Hasil</a>';
        echo '</div>';
        echo '</td>';
        echo '</tr>';
      }
      ?>
    </tbody>
  </table>
</div>