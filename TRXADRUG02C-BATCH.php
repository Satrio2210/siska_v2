<?php
include "conf/config.php";
include "inc/sanie.php";
?>


<link rel="stylesheet" href="assets/css/modern-table.css">
  <table id="screen" class="modern-table">
  <thead>
  <tr>
  <th style="width: 200px;">BATCH CODE</th>
  </tr>
  </thead>
  <tbody>
<?php
  $rawdata = $_POST['q'];
  list($kata,$stockcode) = explode("|", $rawdata);

  if (strlen($kata) == 1)
  {

  $xquery = "SELECT INVE_STOCK_BTCH AS STOCK_BTCH, INVE_STOCK_PRIC, (INVE_STOCK_PRIC * '$profit') AS PRICE_RITEL, INVE_STOCK_QUTY  
              FROM investock 
              WHERE INVE_WARE_CODE = '$gudang_farmasi' 
              AND INVE_STOCK_QUTY > 0  
              AND INVE_VIEW_STAT IN ('R','Y')
              AND INVE_STOCK_CODE = '$stockcode'
              ORDER by INVE_STOCK_CODE, INVE_ENTR_DATE DESC LIMIT 1";    

  }
  else
  {
  $xquery = "SELECT INVE_STOCK_BTCH AS STOCK_BTCH, INVE_STOCK_PRIC, (INVE_STOCK_PRIC * '$profit') AS PRICE_RITEL, INVE_STOCK_QUTY  
              FROM investock 
              WHERE INVE_STOCK_BTCH LIKE '$kata%'
              AND INVE_WARE_CODE = '$gudang_farmasi' 
              AND INVE_STOCK_QUTY > 0  
              AND INVE_VIEW_STAT IN ('R','Y')
              AND INVE_STOCK_CODE = '$stockcode'
              ORDER by INVE_STOCK_CODE, INVE_ENTR_DATE DESC LIMIT 1";    

  }

$q = $db->query($xquery) or die("Gagal ambil batch code !!");
while ($k = $q->fetch(PDO::FETCH_ASSOC))
{
  $outbatch = $k['STOCK_BTCH'];
  $outquty = $k['INVE_STOCK_QUTY'];

  $xharga = round($k['PRICE_RITEL']);
  $int = (int)$xharga;

  $price_ritel = pembulatan($int);

  $view_price_ritel = number_format($price_ritel,0,',','.');



echo '<tr>';
echo '<td style="width: 200px;" onClick="isibatch(\''.$outbatch.'\',\''.$view_price_ritel.'\',\''.$outquty.'\');" 
      style="cursor:pointer">'.$k['STOCK_BTCH'].'</td>';

echo '</tr>';
}
?>
  </tbody>
  </table>








