<?php
include "conf/config.php";
include "inc/sanie.php";
?>

<style>
  #screen {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    background: #fff;
  }

  /* WRAPPER */
  .table-wrapper {
    width: 100%;
    border-radius: 18px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    background: white;
  }

  /* HEADER */
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

  /* BODY */
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

  /* CELL */
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

  /* COLUMN WIDTH */
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

  /* STATUS BADGE */
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

  /* ACTION */
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

  /* SCROLLBAR */
  #screen tbody::-webkit-scrollbar {
    width: 8px;
  }

  #screen tbody::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 999px;
  }

  /* RESPONSIVE */
  @media(max-width:768px) {

    .table-wrapper {
      overflow-x: auto;
    }

    #screen {
      min-width: 1020px;
    }

  }
</style>
<link rel="stylesheet" href="assets/css/modern-table.css">

<div class="table-wrapper">
  <table id="screen" class="modern-table">
    <thead>
      <tr>
        <th>Tgl Daftar</th>
        <th>Antri</th>
        <th>Nama Pasien</th>
        <th>Poli</th>
        <th>Keterangan</th>
        <th>Payment</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $dokter = $_POST['q'];

      $xquery = "SELECT TRXA_REGI_CODE, TRXA_PATI_CODE,
                (SELECT CONCAT(PATI_MAIN_TITL,' ',PATI_MAIN_NAME) FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_NAME,
                (SELECT DATE_FORMAT(PATI_MAIN_BIRT,'%d/%m/%Y') FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS BIRT_DATE,
                (SELECT PATI_MAIN_BIRT FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_AGE,
                (SELECT PATI_MAIN_GEND FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_GEND,
                (SELECT COUNT(*) FROM trxatret WHERE TRXA_TRET_CODE=TRXA_REGI_CODE AND TRXA_VIEW_STAT='Y') AS TOTA_TRET,
                TRXA_REGI_PAYM, TRXA_REGI_POLI, TRXA_REGI_LIST, TRXA_ENTR_DATE,
                TRXA_REGI_RUJUK_TYPE, TRXA_REGI_RUJUK_NOTE
                FROM trxaregi
                WHERE TRXA_REGI_DOCT = '$dokter' AND TRXA_REGI_STAT IN('W','C')
                AND TRXA_ENTR_DATE > DATE_SUB(CURDATE(), INTERVAL 5 DAY)
                ORDER BY TRXA_ENTR_DATE DESC, TRXA_ENTR_TIME DESC";

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

      $q = $db->query($xquery) or die("Gagal ambil regis !!");
      while ($k = $q->fetch(PDO::FETCH_ASSOC)) {
        $outtretcode = $k['TRXA_REGI_CODE'];
        $outpaticode = $k['TRXA_PATI_CODE'];
        $outmainname = $k['MAIN_NAME'];
        $outbirtdate = $k['BIRT_DATE'];
        $mainage = $k['MAIN_AGE'];

        // tanggal lahir
        $tanggal = new DateTime($mainage);
        // tanggal hari ini
        $today = new DateTime('today');
        $y = $today->diff($tanggal)->y;
        $m = $today->diff($tanggal)->m;
        $d = $today->diff($tanggal)->d;
        $outmainage = '' . $y . ' tahun ' . $m . ' bulan ' . $d . ' hari';

        $gender = $k['MAIN_GEND'];
        $outmaingend = isset($genderMap[$gender]) ? $genderMap[$gender] : 'No Gender';

        $outtotaltret = $k['TOTA_TRET'];

        $kodePoli = $k['TRXA_REGI_POLI'];
        $regipoli = isset($namaPoliMap[$kodePoli]) ? $namaPoliMap[$kodePoli] : 'Kosong';
        $outregipoli = $k['TRXA_REGI_POLI'];

        $outpaymcode = $k['TRXA_REGI_PAYM'];
        $outregipaym = isset($paymMap[$outpaymcode]) ? $paymMap[$outpaymcode] : 'Kosong';

        // nomor antrian full (A001, B005, dst)
        $prefix = isset($prefixMap[$kodePoli]) ? $prefixMap[$kodePoli] : '';
        $noantri_full = $prefix . $k['TRXA_REGI_LIST'];

        echo '<tr>';
        echo '<td>' . $k['TRXA_ENTR_DATE'] . '</td>';
        echo '<td>' . $noantri_full . '</td>';
        echo '<td>' . htmlspecialchars($outmainname, ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . $regipoli . '</td>';

        $rujukType = isset($k['TRXA_REGI_RUJUK_TYPE']) ? trim($k['TRXA_REGI_RUJUK_TYPE']) : '';
        $rujukNote = isset($k['TRXA_REGI_RUJUK_NOTE']) ? trim($k['TRXA_REGI_RUJUK_NOTE']) : '';
        if ($rujukType === 'LB' && $rujukNote !== '') {
          $keterangan = 'Rujuk Internal - ' . $rujukNote . '';
        } elseif ($rujukType === 'LB') {
          $keterangan = 'Rujuk Internal';
        } else {
          $keterangan = 'Datang Sendiri';
        }
        echo '<td style="text-align:left;">' . htmlspecialchars($keterangan, ENT_QUOTES, 'UTF-8') . '</td>';

        echo '<td>' . $outregipaym . '</td>';

        if ($outtotaltret > 0) {
          echo '<td><span class="status-badge status-sudah">Sudah Dilayani</span></td>';
        } else {
          echo '<td><span class="status-badge status-belum">Belum Dilayani</span></td>';
        }

        echo '<td><div class="action-group">';
        echo '<a class="button-periksa" href="TRXALABO01.php?regicode=' . urlencode($outtretcode)
          . '&paticode=' . urlencode($outpaticode) . '">Periksa</a>';
        echo '<a class="button-hasil" href="TRXALABO05.php?regicode=' . urlencode($outtretcode)
          . '&paticode=' . urlencode($outpaticode) . '">Hasil</a>';
        echo '</div></td>';

        echo '</tr>';
      }
      ?>
    </tbody>
  </table>
</div>