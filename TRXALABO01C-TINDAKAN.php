<?php
include "conf/config.php";
?>

<table id="screen">
  <thead>
    <tr>
      <th style="width: 500px;">LAYANAN</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $rawdata = $_POST['q'];
    list($kata, $regipoli, $regipaym) = explode("|", $rawdata);

    if (strlen($kata) == 1) {

      $maxquery = "SELECT TBLF_MEDI_CODE, TBLF_MEDI_NAME, TBLF_MEDI_RATE 
              FROM tblfmedi 
              WHERE TBLF_MEDI_ROOM = '$regipoli'
              AND TBLF_MEDI_PAYM = '$regipaym'
              AND TBLF_MEDI_ACTI = 'A'  
              AND TBLF_VIEW_STAT = 'Y' ORDER BY TBLF_MEDI_CODE";

    } else {

      $maxquery = "SELECT TBLF_MEDI_CODE, TBLF_MEDI_NAME, TBLF_MEDI_RATE 
              FROM tblfmedi 
              WHERE TBLF_MEDI_NAME LIKE '$kata%'
              AND TBLF_MEDI_ROOM = '$regipoli'
              AND TBLF_MEDI_PAYM = '$regipaym'
              AND TBLF_MEDI_ACTI = 'A'  
              AND TBLF_VIEW_STAT = 'Y' ORDER BY TBLF_MEDI_CODE";

    }

    $minquery = "SELECT TBLF_MEDI_CODE, TBLF_MEDI_NAME, TBLF_MEDI_RATE 
              FROM tblfmedi 
              WHERE TBLF_MEDI_NAME LIKE '$kata%'
              AND TBLF_MEDI_ACTI = 'A'  
              AND TBLF_VIEW_STAT = 'Y' ORDER BY TBLF_MEDI_CODE";

    //var_dump($xquery);
//exit();
    
    $q = $db->query($maxquery) or die("Gagal ambil list tindakan !!");
    while ($k = $q->fetch(PDO::FETCH_ASSOC)) {
      $outmedicode = $k['TBLF_MEDI_CODE'];
      $outmediname = $k['TBLF_MEDI_NAME'];
      $outmedirate = $k['TBLF_MEDI_RATE'];

      echo '<tr>';
      echo '<td style="width: 500px;" onClick="isitindakan(\'' . $outmedicode . '\',\'' . $outmediname . '\',\'' . $outmedirate . '\');" 
      style="cursor:pointer">' . $k['TBLF_MEDI_NAME'] . '</td>';

      echo '</tr>';
    }
    ?>
  </tbody>
</table>