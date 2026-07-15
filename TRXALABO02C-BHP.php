<?php
include "conf/config.php";
?>


<link rel="stylesheet" href="assets/css/modern-table.css">
  <table id="screen" class="modern-table">
  <thead>
  <tr>
  <th style="width: 500px;">BHP</th>
  </tr>
  </thead>
  <tbody>
<?php
  $rawdata = $_POST['q'];
  list($kata, $regipoli) = explode("|",$rawdata);

  if (strlen($kata) == 1)
  {

  $xquery = "SELECT INVE_STOCK_CODE, INVE_STOCK_NAME, INVE_STOCK_PRIC, INVE_STOCK_QUTY 
              FROM investock 
              WHERE (SELECT WARE_MEDI_ROOM FROM waremast WHERE WARE_HOUS_CODE = INVE_WARE_CODE) = '$regipoli' 
              AND (SELECT INVE_PART_TYPE FROM invemast WHERE INVE_MAST_CODE=INVE_STOCK_CODE) = 'NS'
              AND (SELECT TBLI_TYPE_CATE FROM tblitype WHERE TBLI_TYPE_CODE = 
                      (SELECT INVE_MAIN_TYPE FROM invemast WHERE  INVE_MAST_CODE=INVE_STOCK_CODE)
                  ) = 'RM'
              AND INVE_STOCK_QUTY > 0
              AND INVE_VIEW_STAT IN ('R','Y')
              ORDER by INVE_STOCK_NAME";    
  }
  else
  {

  $xquery = "SELECT INVE_STOCK_CODE, INVE_STOCK_NAME, INVE_STOCK_PRIC, INVE_STOCK_QUTY 
              FROM investock 
              WHERE INVE_STOCK_NAME LIKE '$kata%'
              AND (SELECT WARE_MEDI_ROOM FROM waremast WHERE WARE_HOUS_CODE = INVE_WARE_CODE) = '$regipoli'
              AND (SELECT INVE_PART_TYPE FROM invemast WHERE INVE_MAST_CODE=INVE_STOCK_CODE) = 'NS'  
              AND (SELECT TBLI_TYPE_CATE FROM tblitype WHERE TBLI_TYPE_CODE = 
                      (SELECT INVE_MAIN_TYPE FROM invemast WHERE  INVE_MAST_CODE=INVE_STOCK_CODE)
                  ) = 'RM'
              AND INVE_STOCK_QUTY > 0

              AND INVE_VIEW_STAT IN ('R','Y')
              ORDER by INVE_STOCK_NAME";        

  }

$q = $db->query($xquery) or die("Gagal ambil item Tindakan !!");
while ($k = $q->fetch(PDO::FETCH_ASSOC))
{
  $outstockcode = $k['INVE_STOCK_CODE'];
  $outstockname = $k['INVE_STOCK_NAME'];
  $outstockpric = $k['INVE_STOCK_PRIC'];
  $outstockquty = $k['INVE_STOCK_QUTY'];

echo '<tr>';
echo '<td style="width: 500px;" onClick="isibhp(\''.$outstockcode.'\',\''.$outstockname.'\',\''.$outstockpric.'\',\''.$outstockquty.'\');" 
      style="cursor:pointer">'.$k['INVE_STOCK_NAME'].'</td>';

echo '</tr>';
}
?>
  </tbody>
  </table>








