<?php
include "conf/config.php";
include "inc/sanie.php";
?>


<link rel="stylesheet" href="assets/css/modern-table.css">
  <table id="screen" class="modern-table">
  <thead>
  <tr>

  <th style="width: 300px">Nama Item</th>
  <th style="width: 100px">Satuan</th>
  <th style="width: 100px">Biaya</th>
  <th style="width: 100px">Qty</th>
  <th style="width: 100px">Total.</th>
  <th style="width: 200px">Action</th>

  </tr>
  </thead>
  <tbody>
<?php
$csblcode = $_POST['q'];

$xquery = "SELECT TRXA_CSBL_CODE, TRXA_STOCK_CODE, 
          (SELECT INVE_PART_NAME FROM invemast WHERE INVE_MAST_CODE = TRXA_STOCK_CODE) AS STOCK_NAME,
          (SELECT INVE_SALE_UNIT FROM invemast WHERE INVE_MAST_CODE = TRXA_STOCK_CODE) AS UNIT_CODE,
          (SELECT TBLI_UNIT_NAME FROM tbliunit WHERE TBLI_UNIT_CODE= UNIT_CODE) AS UNIT_NAME,
           TRXA_STOCK_PRIC, TRXA_STOCK_QUTY, (TRXA_STOCK_PRIC * TRXA_STOCK_QUTY) AS TOTAL
          FROM trxacsbl 
          WHERE TRXA_CSBL_STAT='N' AND TRXA_CSBL_CODE='$csblcode' AND TRXA_VIEW_STAT='Y'
          ORDER BY TRXA_STOCK_CODE";
$q = $db->query($xquery) or die("Gagal Maning!!");

while ($k = $q->fetch(PDO::FETCH_ASSOC))
{ 

echo '<tr>';
$stockcode = $k['TRXA_STOCK_CODE'];
echo '<td style="width: 300px">'.$k['STOCK_NAME'].'</td>';

echo '<td style="width: 100px">'.$k['UNIT_NAME'].'</td>';

$viewprice = number_format($k['TRXA_STOCK_PRIC'], 0, '', '.');
echo '<td style="width: 100px; text-align: right;">'.$viewprice.'</td>';

echo '<td style="width: 100px">'.$k['TRXA_STOCK_QUTY'].'</td>';

$viewtotal = number_format($k['TOTAL'], 0, '', '.');
echo '<td style="width: 100px; text-align: right;">'.$viewtotal.'</td>';

echo '<td style="width: 200px">';
echo '<a class="button-delete pure-button" 
              onclick="if (confirm (\'Are You Sure To Delete ?\'))
              { hapuscode(\''.$csblcode.'\',\''.$stockcode.'\');}
              else
              { document.getElementById(\'txtstockcode\').focus();}
              ">Delete</a>';

echo '</td>';

echo '</tr>';
}
?>
  </tbody>
  </table>








