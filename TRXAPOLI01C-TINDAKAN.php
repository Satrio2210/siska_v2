<?php
include "conf/config.php";
?>

<link rel="stylesheet" href="assets/css/modern-table.css">
<table id="screen_suggestion">
  <tbody>
<?php
  $rawdata = $_POST['q'];
  list($kata, $regipoli, $regipaym) = explode("|",$rawdata);

  if (strlen($kata) == 0) {
      $maxquery = "SELECT TBLF_MEDI_CODE, TBLF_MEDI_NAME, TBLF_MEDI_RATE 
                  FROM tblfmedi 
                  WHERE TBLF_MEDI_ROOM = '$regipoli'
                  AND TBLF_MEDI_PAYM IN ('B', 'U')
                  AND TBLF_MEDI_ACTI = 'A'  
                  AND TBLF_VIEW_STAT = 'Y' ORDER BY TBLF_MEDI_CODE";
  } else {
      $maxquery = "SELECT TBLF_MEDI_CODE, TBLF_MEDI_NAME, TBLF_MEDI_RATE 
                  FROM tblfmedi 
                  WHERE TBLF_MEDI_NAME LIKE '$kata%'
                  AND TBLF_MEDI_ROOM = '$regipoli'
                  AND TBLF_MEDI_PAYM IN ('B', 'U')
                  AND TBLF_MEDI_ACTI = 'A'  
                  AND TBLF_VIEW_STAT = 'Y' ORDER BY TBLF_MEDI_CODE";
  }

  $q = $db->query($maxquery) or die("Gagal ambil list tindakan !!");
  $count = 1;
  while ($k = $q->fetch(PDO::FETCH_ASSOC)) {
      $count++;
      $outmedicode = $k['TBLF_MEDI_CODE'];
      $outmediname = $k['TBLF_MEDI_NAME'];
      $outmedirate = $k['TBLF_MEDI_RATE'];
      $medirate = number_format($k['TBLF_MEDI_RATE'], 0, '', '.');

      echo '<tr onClick="isitindakan(\''.$outmedicode.'\',\''.addslashes($outmediname).'\',\''.$outmedirate.'\');">';
      echo '<td style="text-align: left; font-weight: 500;">'.$k['TBLF_MEDI_NAME'].'</td>';
      echo '<td style="text-align: right; width: 120px; font-weight: 600; color: #10b981;">Rp.'.$medirate.'</td>';
      echo '</tr>';
  }
  if ($count == 0) {
      echo '<tr><td colspan="2" style="text-align: center; color: #9ca3af; padding: 10px;">Tidak ditemukan tindakan/layanan</td></tr>';
  }
?>
  </tbody>
</table>



