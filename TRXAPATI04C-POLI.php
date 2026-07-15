<?php
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE);
include "conf/config.php";
?>


<link rel="stylesheet" href="assets/css/modern-table.css">
  <table id="screen" class="modern-table">
  <thead>
  <tr>
  <th style="width: 100px;">ROOM CODE</th>
  <th style="width: 200px;">ROOM NAME</th>
  </tr>
  </thead>
  <tbody>
<?php
  $kata = $_POST['q'];
  //$kata = 'bag';
  if (strlen($kata) == 1)
  {
  $xquery = "SELECT TBLA_POLI_CODE, TBLA_POLI_NAME 
              FROM tblapoli 
              WHERE TBLA_POLI_STAT = 'Y'
              ORDER by TBLA_POLI_CODE";    
  }
  else
  {
  $xquery = "SELECT TBLA_POLI_CODE, TBLA_POLI_NAME 
              FROM tblapoli 
              WHERE TBLA_POLI_NAME LIKE '$kata%'  
              AND TBLA_POLI_STAT = 'Y'
              ORDER by TBLA_POLI_CODE";    
  }

$q = $db->query($xquery) or die("Gagal ambil data !!");
while ($k = $q->fetch(PDO::FETCH_ASSOC))
{
  $policode = $k['TBLA_POLI_CODE'];
  $poliname = $k['TBLA_POLI_NAME'];

echo '<tr>';
echo '<td style="width: 100px;" onClick="isipolicode(\''.$policode.'\',\''.$poliname.'\');" 
      style="cursor:pointer">'.$k['TBLA_POLI_CODE'].'</td>';
echo '<td style="width: 200px;" onClick="isipolicode(\''.$policode.'\',\''.$poliname.'\');" 
      style="cursor:pointer">'.$k['TBLA_POLI_NAME'].'</td>';
echo '</tr>';
}
?>
  </tbody>
  </table>





