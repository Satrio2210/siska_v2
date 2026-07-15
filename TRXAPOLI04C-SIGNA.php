<?php
include "conf/config.php";
?>


<link rel="stylesheet" href="assets/css/modern-table.css">
  <table id="screen" class="modern-table">
  <thead>
  <tr>
  <th style="width: 500px;">SIGNA</th>
  </tr>
  </thead>
  <tbody>
<?php
  $kata = $_POST['q'];
  //list($kata, $regipoli) = explode("|",$rawdata);

  if (strlen($kata) == 1)
  {

  $xquery = "SELECT TBLP_SGNA_CODE, TBLP_SGNA_NAME 
              FROM tblpsgna 
              WHERE TBLP_SGNA_STAT ='Y'
              ORDER by TBLP_SGNA_CODE";    

  }
  else
  {
  $xquery = "SELECT TBLP_SGNA_CODE, TBLP_SGNA_NAME 
              FROM tblpsgna 
              WHERE TBLP_SGNA_NAME LIKE '$kata%'
              AND TBLP_SGNA_STAT ='Y'
              ORDER by TBLP_SGNA_CODE";        
  }

$q = $db->query($xquery) or die("Gagal ambil Signa!!");
while ($k = $q->fetch(PDO::FETCH_ASSOC))
{
  $outsgnacode = $k['TBLP_SGNA_CODE'];
  $outsgnaname = $k['TBLP_SGNA_NAME'];

echo '<tr>';
echo '<td style="width: 500px;" onClick="isisigna(\''.$outsgnacode.'\',\''.$outsgnaname.'\');" 
      style="cursor:pointer">'.$k['TBLP_SGNA_NAME'].'</td>';

echo '</tr>';
}
?>
  </tbody>
  </table>








