<?php
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE);
include "conf/config.php";
include "inc/sanie.php";
?>


<link rel="stylesheet" href="assets/css/modern-table.css">
<div class="card-modern">

  <div class="card-body">
    <div class="table-wrapper">
      <table id="screen" class="modern-table">
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

          // $q = $db->query($xquery) or die("Gagal Maning!!");
          
          $q = $db->query($xquery);

          if (!$q) {
            print_r($db->errorInfo());
            exit;
          }

          while ($k = $q->fetch(PDO::FETCH_ASSOC)) {
            $cnt_a = $k['CNT_A'];
            $cnt_i = $k['CNT_I'];

            if ($cnt_i == 0) {
              $status = '<span class="badge-warning">BELUM SIAP</span>';
            } else if ($cnt_a == 0) {
              $status = '<span class="badge-success">SUDAH SIAP</span>';
            } else {
              $status = '<span class="badge-info">DIPROSES</span>';
            }

            echo '<tr>';

            echo '<td>' . $k['TRXA_REGI_LIST'] . '</td>';

            echo '<td style="text-align:left">' .
              $k['PATI_MAIN_TITL'] . ' ' .
              $k['PATI_MAIN_NAME'] .
              '</td>';

            echo '<td style="text-align:left">' .
              $k['PASS_USER_NAME'] .
              '</td>';

            echo '<td>' .
              $k['PEMBAYARAN'] .
              '</td>';

            echo '<td>' .
              $k['JML_OBAT'] . ' Item' .
              '</td>';

            echo '<td>' .
              $status .
              '</td>';

            echo '
      <td>
        <button
            type="button"
            class="btn-detail"
            onclick="viewresep(\'' . $k['TRXA_PRSC_CODE'] . '\')">
            Detail
        </button>

        <button
            type="button"
            class="btn-cetak"
            onclick="window.open(\'TRXADRUG01-ETIKET.php?prsccode=' . $k['TRXA_PRSC_CODE'] . '\')">
            E-Tiket
        </button>
      </td>';

            echo '</tr>';
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>





