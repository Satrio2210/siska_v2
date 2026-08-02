<link rel="stylesheet" href="assets/css/modern-table.css">
<?php
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE);
include "conf/config.php";

$fulldate = $_POST['q'];
//$kode = 'ACC';
list($startdate, $enddate) = explode("|",$fulldate);
?>
  <table class="pure-table pure-table-horizontal">
  <thead>
  <tr>
  <th style="width: 50px; text-align: center;">No.</th>  
  <th style="width: 300px; text-align: center;">Nama Obat</th>
  <th style="width: 100px; text-align: center;">Jumlah</th>
  <th style="width: 200px; text-align: center;">Total HNA</th> 
  </tr>
  </thead>
  <tbody>

<?php
$no = 0;
$query1 = "SELECT
              p.TRXA_STOCK_CODE AS STOCK_CODE,
              m.INVE_PART_NAME AS STOCK_NAME,
              m.INVE_SALE_UNIT AS UNIT_CODE,
              u.TBLI_UNIT_NAME AS UNIT_NAME,
              m.INVE_MAIN_SPEC AS SPEC_CODE,
              s.TBLI_SPEC_NAME AS SPEC_NAME,
              SUM(p.TRXA_STOCK_QUTY) AS STOCK_QUTY,
              SUM(p.TRXA_STOCK_QUTY * p.TRXA_STOCK_PRIC) AS TOTAL_SALE
          FROM trxaprsc p
          LEFT JOIN invemast m ON p.TRXA_STOCK_CODE = m.INVE_MAST_CODE
          LEFT JOIN tbliunit u ON m.INVE_SALE_UNIT = u.TBLI_UNIT_CODE
          LEFT JOIN tblispec s ON m.INVE_MAIN_SPEC = s.TBLI_SPEC_CODE
          WHERE p.TRXA_PRSC_STAT = 'P' AND p.TRXA_VIEW_STAT = 'Y'
            AND p.TRXA_ENTR_DATE BETWEEN '$startdate' AND '$enddate'
          GROUP BY
              p.TRXA_STOCK_CODE,
              m.INVE_PART_NAME,
              m.INVE_SALE_UNIT,
              u.TBLI_UNIT_NAME,
              m.INVE_MAIN_SPEC,
              s.TBLI_SPEC_NAME
          ORDER BY STOCK_QUTY DESC";

$q1 = $db->query($query1) or die("Gagal Ambil Pemakaian Obat!!");
while ($k1 = $q1->fetch(PDO::FETCH_ASSOC))
{ 
    $no++;

    $stockcode = $k1['STOCK_CODE'];
    $stockname = $k1['STOCK_NAME'];
    $nameunit = $k1['UNIT_NAME'];
    $namespec = $k1['SPEC_NAME'];
    $quantity = $k1['STOCK_QUTY'];
    $price = $k1['TOTAL_SALE'];

    echo '<tr>';
    echo '<td style="width: 50px">'.$no.'</td>';
    echo '<td style="width: 300px">'.$stockname.' '.$namespec.'</td>';
    echo '<td style="width: 100px; text-align: right;">'.$quantity.' '.$nameunit.'</td>';

    $view_price = number_format($price, 0, '', '.');
    echo '<td style="width: 200px; text-align: right;">Rp. '.$view_price.'</td>';

    echo '</tr>';
}  

// Total Pemakaian
$query_total = "SELECT COALESCE(SUM(p.TRXA_STOCK_QUTY * p.TRXA_STOCK_PRIC), 0) AS TOTAL_PRICE
          FROM trxaprsc p
          WHERE p.TRXA_PRSC_STAT = 'P' AND p.TRXA_VIEW_STAT = 'Y'
            AND p.TRXA_ENTR_DATE BETWEEN '$startdate' AND '$enddate'";

$q_total = $db->query($query_total) or die("Gagal ambil Total Pemakaian");
$r_total = $q_total->fetch(PDO::FETCH_ASSOC);
$total = (float) $r_total['TOTAL_PRICE'];

$view_total = number_format($total, 0, '', '.');
?>
  <tr class='pure-table-odd'>
  <td colspan="3" style="text-align: right;">Total Pemakaian</td>  
  <td style="text-align: right;">Rp. <?php echo $view_total; ?></td>
  </tr>


  </tbody>
  </table>
<div style="padding: 30px 0 30px 0;">
  <center>
  &copy; 2021, SISKA Development Legal   
  </center>
</div>




