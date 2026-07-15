<?php
include "conf/config.php";
include "inc/sanie.php";
?>


<link rel="stylesheet" href="assets/css/modern-table.css">
  <table id="screen" class="modern-table">
  <thead>
  <tr>
  <th style="width: 400px;">PEMERIKSAAN LABORATORIUM</th>
  </tr>
  </thead>
  <tbody>
<?php

  $rawdata = xss_clean($_POST['q']);
  list($kata,$gender,$tindakan) = explode("|", $rawdata);

  if (strlen($kata) == 1)
  {
  $xquery = "SELECT LABO_MAST_CODE, LABO_SUBS_CODE,
                    (SELECT TBLL_EXAM_NAME FROM tbllexam WHERE TBLL_EXAM_CODE = LABO_SUBS_CODE) AS SUBS_NAME, 
                    LABO_SIZE_NAME, LABO_UNIT_NAME, LABO_VALU_MIN, LABO_VALU_MAX, LABO_VALU_STRG  
              FROM labomast 
              WHERE LABO_PATI_GEND = '$gender' 
              AND LABO_SIZE_CODE IN (SELECT TRXA_MEDI_CODE FROM trxatret WHERE TRXA_TRET_CODE='$tindakan') 
              AND LABO_VIEW_STAT = 'Y'
              ORDER by LABO_SIZE_NAME";    
  }
  else
  {
  $xquery = "SELECT LABO_MAST_CODE, LABO_SUBS_CODE,
                    (SELECT TBLL_EXAM_NAME FROM tbllexam WHERE TBLL_EXAM_CODE = LABO_SUBS_CODE) AS SUBS_NAME,  
                    LABO_SIZE_NAME, LABO_UNIT_NAME, LABO_VALU_MIN, LABO_VALU_MAX, LABO_VALU_STRG  
              FROM labomast 
              WHERE LABO_PATI_GEND = '$gender' 
              AND LABO_SIZE_CODE IN (SELECT TRXA_MEDI_CODE FROM trxatret WHERE TRXA_TRET_CODE='$tindakan') 
              AND LABO_VIEW_STAT = 'Y'
              AND LABO_SIZE_NAME LIKE '$kata%'
              OR LABO_PATI_GEND = '$gender' 
              AND LABO_SIZE_CODE IN (SELECT TRXA_MEDI_CODE FROM trxatret WHERE TRXA_TRET_CODE='$tindakan') 
              AND LABO_VIEW_STAT = 'Y'
              AND LABO_SIZE_NAME LIKE '%$kata%'
              ORDER by LABO_SIZE_NAME";        
  }

$q = $db->query($xquery) or die("Gagal ambil Rujukan Lab !!");
while ($k = $q->fetch(PDO::FETCH_ASSOC))
{
  $outmastcode = $k['LABO_MAST_CODE'];
  $outsizename = $k['LABO_SIZE_NAME'];
  $outunitname = $k['LABO_UNIT_NAME'];
  $outvalumin = $k['LABO_VALU_MIN'];
  $outvalumax = $k['LABO_VALU_MAX'];
  $outvalustrg = $k['LABO_VALU_STRG'];


echo '<tr>';


echo '<td style="width: 100px;" onClick="isirujukan(\''.$outmastcode.'\',\''.$outsizename.'\',\''.$outunitname.'\',\''.$outvalumin.'\',\''.$outvalumax.'\',\''.$outvalustrg.'\');" 
      style="cursor:pointer">'.$k['SUBS_NAME'].'</td>';

echo '<td style="width: 300px;" onClick="isirujukan(\''.$outmastcode.'\',\''.$outsizename.'\',\''.$outunitname.'\',\''.$outvalumin.'\',\''.$outvalumax.'\',\''.$outvalustrg.'\');" 
      style="cursor:pointer">'.$k['LABO_SIZE_NAME'].'</td>';

echo '</tr>';
}
?>
  </tbody>
  </table>








