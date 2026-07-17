<link rel="stylesheet" href="assets/css/modern-table.css">
<?php
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE);
include "conf/config.php";
?>

<style>
  .table-container {
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    margin-top: 10px;
    overflow: hidden;
  }

  #tblsaleprt {
    width: 100%;
    border-collapse: collapse;
  }

  #tblsaleprt tbody {
    display: block;
    max-height: 250px;
    overflow-y: auto;
  }

  #tblsaleprt thead,
  #tblsaleprt tbody tr {
    display: table;
    width: 100%;
    table-layout: fixed;
  }

  #tblsaleprt thead {
    background: #10b981;
    color: white;
    /* position: sticky;
    top: 0;
    z-index: 10; */
    /* height: 30px; */
  }

  #tblsaleprt th {
    padding: 10px 6px;
    font-size: 13px;
    font-weight: 700;
    text-align: center;
    vertical-align: middle;
    border: none;
    letter-spacing: 0.3px;
  }

  #tblsaleprt td {
    padding: 8px 6px;
    font-size: 12px;
    /* color: #374151; */
    text-align: center;
    vertical-align: middle;
    border-bottom: 1px solid #e5e7eb;
    overflow-wrap: break-word;
    word-break: break-word;
  }

  #tblsaleprt th:nth-child(1),
  #tblsaleprt td:nth-child(1) {
    width: 30%;
  }

  #tblsaleprt th:nth-child(2),
  #tblsaleprt td:nth-child(2) {
    width: 70%;
  }

  #tblsaleprt th:nth-child(3),
  #tblsaleprt td:nth-child(3) {
    width: 70%;
  }

  #tblsaleprt th:nth-child(4),
  #tblsaleprt td:nth-child(4) {
    width: 70%;
  }

  #tblsaleprt th:nth-child(5),
  #tblsaleprt td:nth-child(5) {
    width: 70%;
  }

  #tblsaleprt th:nth-child(6),
  #tblsaleprt td:nth-child(6) {
    width: 70%;
  }

  #tblsaleprt tbody tr:hover {
    background: #d0e3f7;
  }
</style>

<div class="table-container">
  <table id="tblsaleprt">
    <thead>
      <tr>
        <th>Tgl Bayar</th>
        <th>Klinik</th>
        <th>Medis</th>
        <th>Nama</th>
        <th>Pembayaran</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody>
      <?php
      $kata = $_POST['q'];
      //$kode = 'ACC';
      
      $panjangkata = strlen($kata);
      if ($panjangkata == 1) {
        $xquery = "SELECT TRXA_SALE_CODE, TRXA_REGI_CODE AS REGI_CODE, TRXA_PATI_CODE, 
          TRXA_REGI_DOCT, (SELECT PASS_USER_NAME FROM passiden WHERE PASS_USER_IDEN = TRXA_REGI_DOCT) AS REGI_DOCT,
          (SELECT CONCAT(PATI_MAIN_TITL,' ',PATI_MAIN_NAME) FROM patimast WHERE PATI_MAST_CODE = TRXA_PATI_CODE) AS PATI_NAME,
          (SELECT PATI_MAIN_GEND FROM patimast WHERE PATI_MAST_CODE = TRXA_PATI_CODE) AS MAIN_GEND,
          TRXA_REGI_POLI, (SELECT TBLA_POLI_NAME FROM tblapoli WHERE TBLA_POLI_CODE=TRXA_REGI_POLI) AS REGI_POLI,
          TRXA_PAYM_MODE, TRXA_ENTR_DATE,
          (SELECT TRXA_REGI_PAYM FROM trxaregi WHERE TRXA_REGI_CODE = REGI_CODE) AS REGI_PAYM
          FROM trxasale WHERE TRXA_VIEW_STAT = 'Y' 
          AND DATE(TRXA_ENTR_DATE) >= DATE_SUB(CURDATE(), INTERVAL 9 DAY)
          ORDER BY TRXA_ENTR_DATE DESC, 
          TRXA_ENTR_TIME DESC LIMIT 10";
      } else {
        $xquery = "SELECT TRXA_SALE_CODE, TRXA_REGI_CODE AS REGI_CODE, TRXA_PATI_CODE, 
          TRXA_REGI_DOCT, (SELECT PASS_USER_NAME FROM passiden WHERE PASS_USER_IDEN = TRXA_REGI_DOCT) AS REGI_DOCT,
          (SELECT CONCAT(PATI_MAIN_TITL,' ',PATI_MAIN_NAME) FROM patimast WHERE PATI_MAST_CODE = TRXA_PATI_CODE) AS PATI_NAME,
          (SELECT PATI_MAIN_GEND FROM patimast WHERE PATI_MAST_CODE = TRXA_PATI_CODE) AS MAIN_GEND,
          TRXA_REGI_POLI, (SELECT TBLA_POLI_NAME FROM tblapoli WHERE TBLA_POLI_CODE=TRXA_REGI_POLI) AS REGI_POLI,
          TRXA_PAYM_MODE, TRXA_ENTR_DATE,
          (SELECT TRXA_REGI_PAYM FROM trxaregi WHERE TRXA_REGI_CODE = REGI_CODE) AS REGI_PAYM
          FROM trxasale 
          WHERE TRXA_VIEW_STAT = 'Y' 
          AND DATE(TRXA_ENTR_DATE) >= DATE_SUB(CURDATE(), INTERVAL 9 DAY)
          AND (
              (SELECT PATI_MAIN_NAME FROM patimast WHERE PATI_MAST_CODE = TRXA_PATI_CODE) LIKE '%$kata%' 
              OR TRXA_SALE_CODE LIKE '%$kata%'
          )
          ORDER BY TRXA_ENTR_DATE DESC, TRXA_ENTR_TIME DESC";
      }

      $q = $db->query($xquery) or die("Gagal Maning!!");
      while ($k = $q->fetch(PDO::FETCH_ASSOC)) {

        echo '<tr>';
        $salecode = $k['TRXA_SALE_CODE'];
        $regicode = $k['REGI_CODE'];
        $paticode = $k['TRXA_PATI_CODE'];
        $paiddate = date("d-m-Y", strtotime($k['TRXA_ENTR_DATE']));

        echo '<td>' . $paiddate . '</td>';
        echo '<td>' . $k['REGI_POLI'] . '</td>';
        echo '<td>' . $k['REGI_DOCT'] . '</td>';
        echo '<td>' . $k['PATI_NAME'] . '</td>';
        // echo '<td>' . $regicode . '</td>';
        // echo '<td>' . $salecode . '</td>';
        // echo '<td>' . $paticode . '</td>';
        // $maingend = $k['MAIN_GEND'];
        // if ($maingend == 'M') {
        //   echo '<td> Laki-laki </td>';
        // } else if ($maingend == 'F') {
        //   echo '<td> Perempuan </td>';
        // } else {
        //   echo '<td> No gender </td>';
        // }
      
        $regipaym = $k['REGI_PAYM'];
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
        }

        echo '<td>';
        ?>
        <button type="button" class="btn-modern btn-refresh"
          onClick="javascript: location.href ='TRXASALE02P.php?regicode=<?php echo $regicode; ?>&salecode=<?php echo $salecode; ?>'">Print</button>

        <?php
        echo '</td>';

        echo '</tr>';
      }
      ?>

    </tbody>
  </table>
</div>