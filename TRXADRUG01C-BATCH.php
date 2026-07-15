<?php
include "conf/config.php";
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
  $xquery = "SELECT DISTINCT(INVE_STOCK_BTCH) AS STOCK_BTCH 
              FROM investock 
              WHERE INVE_STOCK_CODE = '$stockcode'";    
  }
  else
  {
  $xquery = "SELECT DISTINCT(INVE_STOCK_BTCH) AS STOCK_BTCH 
              FROM investock 
              WHERE INVE_STOCK_BTCH LIKE '$kata%'  
              AND INVE_STOCK_CODE = '$stockcode'";        
  }

$q = $db->query($xquery) or die("Gagal ambil batch code !!");
while ($k = $q->fetch(PDO::FETCH_ASSOC))
{
  $outbatch = $k['STOCK_BTCH'];

echo '<tr>';
echo '<td style="width: 200px;" onClick="isibatch(\''.$outbatch.'\');" 
      style="cursor:pointer">'.$k['STOCK_BTCH'].'</td>';

echo '</tr>';
}
?>
  </tbody>
  </table>








