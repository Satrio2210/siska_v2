<?php
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE);
include "conf/config.php";
include "inc/sanie.php";
?>

<style>
  .table-container {
    overflow: auto;
    border-radius: 16px;
    max-height: 420px;
    border: 1px solid #e2e8f0;
    margin-top: 10px;
  }

  #tbllistpt-farm-receipt {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
  }

  #tbllistpt-farm-receipt th,
  #tbllistpt-farm-receipt td {
    padding: 10px 20px;
  }

  #tbllistpt-farm-receipt thead {
    background: #10b981;
    color: white;
    position: sticky;
    top: 0;
    z-index: 10;
  }

  #tbllistpt-farm-receipt th {
    font-size: 13px;
    font-weight: 700;
    text-align: center;
    vertical-align: middle;
    border: none;
    letter-spacing: 0.3px;
  }

  #tbllistpt-farm-receipt td {
    font-size: 12px;
    /* color: #374151; */
    text-align: center;
    vertical-align: middle;
    border-bottom: 1px solid #e5e7eb;
    overflow-wrap: break-word;
    word-break: break-word;
  }

  #tbllistpt-farm-receipt th:nth-child(1),
  #tbllistpt-farm-receipt td:nth-child(1) {
    width: 5%;
  }

  #tbllistpt-farm-receipt th:nth-child(2),
  #tbllistpt-farm-receipt td:nth-child(2) {
    width: 15%;
  }

  #tbllistpt-farm-receipt th:nth-child(3),
  #tbllistpt-farm-receipt td:nth-child(3) {
    width: 15%;
  }

  #tbllistpt-farm-receipt th:nth-child(4),
  #tbllistpt-farm-receipt td:nth-child(4) {
    width: 10%;
  }

  #tbllistpt-farm-receipt th:nth-child(5),
  #tbllistpt-farm-receipt td:nth-child(5) {
    width: 10%;
  }

  #tbllistpt-farm-receipt th:nth-child(6),
  #tbllistpt-farm-receipt td:nth-child(6) {
    width: 10%;
  }

  #tbllistpt-farm-receipt th:nth-child(7),
  #tbllistpt-farm-receipt td:nth-child(7) {
    width: 20%;
  }
</style>

<div class="table-container">
  <table id="tbllistpt-farm-receipt">
    <thead>
      <tr>
        <th>No. Antrian</th>
        <th>Pasien</th>
        <th>Dokter</th>
        <th>Pembayaran</th>
        <th>Jumlah Obat</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $kata = $_POST['q'];
      $panjangkata = strlen($kata);
      $xquery = "SELECT
              p.TRXA_PRSC_CODE,
              r.TRXA_REGI_LIST,
              pm.PATI_MAIN_TITL,
              pm.PATI_MAIN_NAME,
              d.PASS_USER_NAME,
              CASE
                  WHEN r.TRXA_REGI_PAYM='U' THEN 'UMUM'
                  WHEN r.TRXA_REGI_PAYM='B' THEN 'BPJS'
                  ELSE '-'
              END AS PEMBAYARAN,
              COUNT(*) AS JML_OBAT,
              SUM(CASE WHEN p.TRXA_PRSC_STAT='A' THEN 1 ELSE 0 END) AS CNT_A,
              SUM(CASE WHEN p.TRXA_PRSC_STAT='I' THEN 1 ELSE 0 END) AS CNT_I,
              MAX(p.TRXA_ENTR_DATE) AS LAST_DATE,
              MAX(p.TRXA_ENTR_TIME) AS LAST_TIME
              FROM trxaprsc p
              INNER JOIN trxaregi r
              ON r.TRXA_REGI_CODE=p.TRXA_PRSC_CODE
              LEFT JOIN patimast pm
              ON pm.PATI_MAST_CODE=r.TRXA_PATI_CODE
              LEFT JOIN passiden d
              ON d.PASS_USER_IDEN=r.TRXA_REGI_DOCT
              WHERE
              p.TRXA_VIEW_STAT='Y'
              AND p.TRXA_PRSC_STAT IN ('A','I')
              AND p.TRXA_ENTR_DATE > DATE_SUB(CURDATE(),INTERVAL 4 DAY)
              ";

      if ($kata != '') {
        $xquery .= "
      AND (
          pm.PATI_MAIN_NAME LIKE '$kata%'
          OR r.TRXA_REGI_LIST LIKE '$kata%'
      )
      ";
      }
      $xquery .= "
              GROUP BY 
              p.TRXA_PRSC_CODE,
              r.TRXA_REGI_LIST,
              pm.PATI_MAIN_TITL,
              pm.PATI_MAIN_NAME,
              d.PASS_USER_NAME,
              r.TRXA_REGI_PAYM

              ORDER BY 
              LAST_DATE DESC,
              LAST_TIME DESC
              ";

      $q = $db->query($xquery);

      if (!$q) {
        print_r($db->errorInfo());
        exit;
      }

      while ($k = $q->fetch(PDO::FETCH_ASSOC)) {
        $cnt_a = $k['CNT_A'];
        $cnt_i = $k['CNT_I'];

        if ($cnt_i == 0) {
          $status = '<span class="badge badge-antri">BELUM SIAP</span>';
        } else if ($cnt_a == 0) {
          $status = '<span class="badge badge-selesai">SUDAH SIAP</span>';
        } else {
          $status = '<span class="badge badge-periksa">DIPROSES</span>';
        }

        echo '<tr>';
        echo '<td>' . $k['TRXA_REGI_LIST'] . '</td>';
        echo '<td>' . $k['PATI_MAIN_TITL'] . ' ' . $k['PATI_MAIN_NAME'] . '</td>';
        echo '<td>' . $k['PASS_USER_NAME'] . '</td>';
        echo '<td>' . $k['PEMBAYARAN'] . '</td>';
        echo '<td>' . $k['JML_OBAT'] . ' Item' . '</td>';
        echo '<td>' . $status . '</td>';
        echo '<td>';
        echo '<div class="form-grid"> 
        <button
            type="button"
            class="btn-modern btn-save" style="height: 38px;"
            onclick="viewresep(\'' . $k['TRXA_PRSC_CODE'] . '\')">
            Detail
        </button>

        <button
            type="button"
            class="btn-modern btn-refresh" style="height: 38px;"
            onclick="window.open(\'TRXADRUG01-ETIKET.php?prsccode=' . $k['TRXA_PRSC_CODE'] . '\')">
            E-Tiket
        </button>
        </div>';
        echo '</td>';

        echo '</tr>';
      }
      ?>
    </tbody>
  </table>
</div>