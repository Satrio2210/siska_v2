<?php
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE);
include "conf/config.php";
?>


<link rel="stylesheet" href="assets/css/modern-table.css">
  <table id="screen" class="modern-table">
  <thead>
  <tr>
  <th style="width: 100px;">CODE</th>
  <th style="width: 200px;">NAME</th>
  </tr>
  </thead>
  <tbody>
<?php
  $kata = $_POST['q'];
  if (strlen($kata) == 1)
  {
  $xquery = "SELECT PASS_USER_IDEN, PASS_USER_NAME 
              FROM passiden 
              WHERE PASS_USER_TYPE = 'Y'
              ORDER by PASS_USER_IDEN";    
  }
  else
  {
  $xquery = "SELECT PASS_USER_IDEN, PASS_USER_NAME 
              FROM passiden 
              WHERE PASS_USER_NAME LIKE '$kata%'   
              AND PASS_USER_TYPE = 'Y'
              OR PASS_USER_NAME LIKE '%$kata%'   
              AND PASS_USER_TYPE = 'Y'
              ORDER by PASS_USER_IDEN";        
  }

$q = $db->query($xquery) or die("Gagal ambil data !!");
while ($k = $q->fetch(PDO::FETCH_ASSOC))
{
  $outusercode = $k['PASS_USER_IDEN'];
  $outusername = $k['PASS_USER_NAME'];

echo '<tr>';
echo '<td style="width: 100px;" onClick="isiusercode(\''.$outusercode.'\',\''.$outusername.'\');" 
      style="cursor:pointer">'.$k['PASS_USER_IDEN'].'</td>';
echo '<td style="width: 200px;" onClick="isiusercode(\''.$outusercode.'\',\''.$outusername.'\');" 
      style="cursor:pointer">'.$k['PASS_USER_NAME'].'</td>';
echo '</tr>';
}
?>
  </tbody>
  </table>






