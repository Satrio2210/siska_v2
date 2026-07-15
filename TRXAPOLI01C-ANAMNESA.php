<?php
include "conf/config.php";
?>


<link rel="stylesheet" href="assets/css/modern-table.css">
  <table id="screen" class="modern-table">
  <thead>
  <tr>
  <th style="width: 500px;">ANAMNESA TEXT</th>
  </tr>
  </thead>
  <tbody>
<?php
  $kata = $_POST['q'];
  if (strlen($kata) == 1)
  {
  $xquery = "SELECT DISTINCT(TRXA_EXAM_ANAM) AS EXAM_ANAM 
              FROM trxaexam 
              WHERE TRXA_VIEW_STAT = 'Y'
              ORDER by TRXA_EXAM_ANAM LIMIT 20";    
  }
  else
  {
  $xquery = "SELECT DISTINCT(TRXA_EXAM_ANAM) AS EXAM_ANAM 
              FROM trxaexam 
              WHERE TRXA_EXAM_ANAM LIKE '$kata%'  
              AND TRXA_VIEW_STAT = 'Y'
              ORDER by TRXA_EXAM_ANAM";        
  }

$q = $db->query($xquery) or die("Gagal ambil teks anamnesa !!");
while ($k = $q->fetch(PDO::FETCH_ASSOC))
{
  $outexamanam = $k['EXAM_ANAM'];

echo '<tr>';
echo '<td style="width: 500px;" onClick="isiexamanam(\''.$outexamanam.'\');" 
      style="cursor:pointer">'.$k['EXAM_ANAM'].'</td>';

echo '</tr>';
}
?>
  </tbody>
  </table>








