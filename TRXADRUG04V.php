<link rel="stylesheet" href="assets/css/modern-table.css">
<?php
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE);

include "conf/config.php";

$fulldate = $_POST['q'];
list($startdate, $enddate) = explode("|", $fulldate);
?>

<div class="table-wrapper">
  <table class="modern-table">
    <thead>
      <tr>
        <th>No</th>
        <th>Kode</th>
        <th>Nama Obat</th>
        <th>Qty Keluar</th>
      </tr>
    </thead>
    <tbody>

      <?php

      $no = 0;
      $totalqty = 0;

      $query1 = "SELECT
          p.TRXA_STOCK_CODE,
          m.INVE_PART_NAME,
          SUM(p.TRXA_STOCK_QUTY) AS TOTAL_PAKAI
      FROM trxaprsc p
      LEFT JOIN invemast m
          ON p.TRXA_STOCK_CODE = m.INVE_MAST_CODE
      WHERE p.TRXA_VIEW_STAT = 'Y'
      AND p.TRXA_PRSC_STAT = 'P'
      AND p.TRXA_ENTR_DATE BETWEEN '$startdate' AND '$enddate'
      GROUP BY
          p.TRXA_STOCK_CODE,
          m.INVE_PART_NAME
      ORDER BY TOTAL_PAKAI DESC
      ";

      $q1 = $db->query($query1) or die("Gagal Ambil Data Pemakaian Obat");

      while ($k1 = $q1->fetch(PDO::FETCH_ASSOC)) {
        $no++;

        $qty = (int) $k1['TOTAL_PAKAI'];
        $totalqty += $qty;

        echo '<tr>';
        echo '<td>' . $no . '</td>';
        echo '<td>' . htmlspecialchars($k1['TRXA_STOCK_CODE']) . '</td>';
        echo '<td style="text-align:left;">' . htmlspecialchars($k1['INVE_PART_NAME']) . '</td>';
        echo '<td>' . number_format($qty, 0, '', '.') . '</td>';
        echo '</tr>';
      }
      ?>

      <tr style="background:#e2e8f0;font-weight:700;">
        <td colspan="3" style="text-align:right;">TOTAL PEMAKAIAN</td>
        <td><?php echo number_format($totalqty, 0, '', '.'); ?></td>
      </tr>

      <tr style="background:#f8fafc;">
        <td colspan="4" style="text-align:left;">Total Jenis Obat : <b><?php echo $no; ?></b></td>
      </tr>

    </tbody>
  </table>
</div>
