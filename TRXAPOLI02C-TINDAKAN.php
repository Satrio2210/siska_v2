<?php
include "conf/config.php";
?>


<link rel="stylesheet" href="assets/css/modern-table.css">
  <table id="screen" class="modern-table">
  <thead>
  <tr>
  <th style="width: 400px;">TINDAKAN</th>
  <th style="width: 150px;">HARGA</th>
  </tr>
  </thead>
  <tbody>
<?php
  $rawdata = $_POST['q'];
  list($kata, $regipoli, $regipaym) = explode("|",$rawdata);

  if (strlen($kata) == 1)
  {

  $maxquery = "SELECT TBLF_MEDI_CODE, TBLF_MEDI_NAME, TBLF_MEDI_RATE 
              FROM tblfmedi 
              WHERE TBLF_MEDI_ROOM = '$regipoli'
              AND TBLF_MEDI_PAYM IN ('B', 'U')
              AND TBLF_MEDI_ACTI = 'A'  
              AND TBLF_VIEW_STAT = 'Y' ORDER BY TBLF_MEDI_CODE";        

  }
  else
  {

  $maxquery = "SELECT TBLF_MEDI_CODE, TBLF_MEDI_NAME, TBLF_MEDI_RATE 
              FROM tblfmedi 
              WHERE TBLF_MEDI_NAME LIKE '$kata%'
              AND TBLF_MEDI_ROOM = '$regipoli'
              AND TBLF_MEDI_PAYM IN ('B', 'U')
              AND TBLF_MEDI_ACTI = 'A'  
              AND TBLF_VIEW_STAT = 'Y' ORDER BY TBLF_MEDI_CODE";        
  }

  $minquery = "SELECT TBLF_MEDI_CODE, TBLF_MEDI_NAME, TBLF_MEDI_RATE 
              FROM tblfmedi 
              WHERE TBLF_MEDI_NAME LIKE '$kata%'
              AND TBLF_MEDI_ACTI = 'A'  
              AND TBLF_VIEW_STAT = 'Y' ORDER BY TBLF_MEDI_CODE";        

$q = $db->query($maxquery) or die("Gagal ambil list tindakan !!");
while ($k = $q->fetch(PDO::FETCH_ASSOC))
{
  $outmedicode = $k['TBLF_MEDI_CODE'];
  $outmediname = $k['TBLF_MEDI_NAME'];
  $outmedirate = $k['TBLF_MEDI_RATE'];
  $medirate = number_format($k['TBLF_MEDI_RATE'], 0, '', '.');

echo '<tr>';
echo '<td style="width: 400px;" onClick="isitindakan(\''.$outmedicode.'\',\''.$outmediname.'\',\''.$outmedirate.'\');" 
      style="cursor:pointer">'.$k['TBLF_MEDI_NAME'].'</td>';
echo '<td style="width: 150px;" onClick="isitindakan(\''.$outmedicode.'\',\''.$outmediname.'\',\''.$outmedirate.'\');" 
      style="cursor:pointer">Rp.'.$medirate.'</td>';

echo '</tr>';
}
?>
  </tbody>
  </table>








