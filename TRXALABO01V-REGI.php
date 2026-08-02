<?php
include "conf/config.php";
include "inc/sanie.php";
?>

<!-- <style>
  #screen {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    background: #fff;
  }

  .table-wrapper {
    width: 100%;
    border-radius: 18px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    background: white;
  }

  #screen thead {
    position: sticky;
    top: 0;
    z-index: 2;
  }

  #screen thead tr {
    display: table;
    width: 100%;
    table-layout: fixed;
  }

  #screen th {
    padding: 16px;
    font-size: 14px;
    font-weight: 700;
    text-align: center;
    border: none;
    background: #10b981;
    color: white;
  }

  #screen tbody {
    display: block;
    max-height: 520px;
    overflow-y: auto;
    overflow-x: hidden;
    width: 100%;
    scrollbar-gutter: stable;
  }

  #screen tbody tr {
    display: table;
    width: 100%;
    table-layout: fixed;
    transition: .2s ease;
    background: white;
  }

  #screen tbody tr:nth-child(even) {
    background: #f9fafb;
  }

  #screen tbody tr:hover {
    background: #f3f4f6;
  }

  #screen td {
    padding: 14px 12px;
    font-size: 12px;
    font-weight: 600;
    color: #000;
    border-bottom: 1px solid #edf2f7;
    text-align: center;
    vertical-align: middle;
    word-wrap: break-word;
  }

  #screen th:nth-child(1),
  #screen td:nth-child(1) {
    width: 150px;
  }

  #screen th:nth-child(2),
  #screen td:nth-child(2) {
    width: 90px;
  }

  #screen th:nth-child(3),
  #screen td:nth-child(3) {
    width: 200px;
  }

  #screen th:nth-child(4),
  #screen td:nth-child(4) {
    width: 150px;
  }

  #screen th:nth-child(5),
  #screen td:nth-child(5) {
    width: 180px;
    text-align: left;
  }

  #screen th:nth-child(6),
  #screen td:nth-child(6) {
    width: 120px;
  }

  #screen th:nth-child(7),
  #screen td:nth-child(7) {
    width: 150px;
  }

  #screen th:nth-child(8),
  #screen td:nth-child(8) {
    width: 160px;
  }

  .status-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
  }

  .status-sudah {
    background: #dcfce7;
    color: #15803d;
  }

  .status-belum {
    background: #fef3c7;
    color: #b45309;
  }

  .action-group {
    display: flex;
    gap: 8px;
    justify-content: center;
    flex-wrap: wrap;
  }

  .button-periksa {
    border: none;
    border-radius: 10px;
    padding: 8px 14px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: .2s ease;
    text-decoration: none;
    background: #10b981;
    color: white;
  }

  .button-periksa:hover {
    background: #059669;
  }

  .button-hasil {
    border: none;
    border-radius: 10px;
    padding: 8px 14px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: .2s ease;
    text-decoration: none;
    background: #3b82f6;
    color: white;
  }

  .button-hasil:hover {
    background: #2563eb;
  }

  #screen tbody::-webkit-scrollbar {
    width: 8px;
  }

  #screen tbody::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 999px;
  }

  .lab00-empty {
    padding: 28px 12px;
    text-align: center;
    color: #64748b;
    font-weight: 600;
  }

  @media(max-width:768px) {

    .table-wrapper {
      overflow-x: auto;
    }

    #screen {
      min-width: 1020px;
    }

  }
</style> -->

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

        echo '<tr>';
        echo '<td>' . htmlspecialchars($k['TRXA_ENTR_DATE'], ENT_QUOTES, 'UTF-8') . '<br>' . htmlspecialchars($k['TRXA_ENTR_TIME'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($noantri_full, ENT_QUOTES, 'UTF-8') . '</td>';
        $rujukType = '';
        $rujukNote = '';
        if ($rujukType === 'LB' && $rujukNote !== '') {
          $keterangan = 'Rujuk Internal - ' . $rujukNote;
        } elseif ($rujukType === 'LB') {
          $keterangan = 'Rujuk Internal';
        } else {
          $keterangan = 'Datang Sendiri';
        }
        echo '<td>' . htmlspecialchars($keterangan, ENT_QUOTES, 'UTF-8') . '</td>';
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