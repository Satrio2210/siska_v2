<?php
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE);
include "conf/config.php";
?>


<link rel="stylesheet" href="assets/css/modern-table.css">
  <table id="screen" class="modern-table">
  <thead>
  <tr>
  <th style="width: 100px">SIGNA CODE</th>
  <th style="width: 300px">SIGNA NAME</th>
  <th style="width: 200px">Action</th>

  </tr>
  </thead>
  <tbody>
<?php
$kata = $_POST['q'];
//$kode = 'ACC';
$panjangkata = strlen($kata);
if ($panjangkata == 0 )
{ 

$xquery = "SELECT TBLP_SGNA_CODE, TBLP_SGNA_NAME
          FROM tblpsgna 
          WHERE TBLP_SGNA_STAT = 'Y' 
          ORDER by TBLP_SGNA_CODE"; 
}
else
{
$xquery = "SELECT TBLP_SGNA_CODE, TBLP_SGNA_NAME 
          FROM tblpsgna 
          WHERE TBLP_SGNA_NAME LIKE '$kata%' 
          AND TBLP_SGNA_STAT = 'Y' 
          ORDER by TBLP_SGNA_CODE"; 
}
$q = $db->query($xquery) or die("Gagal Maning!!");
while ($k = $q->fetch(PDO::FETCH_ASSOC))
{ 
echo '<tr>';
$policode = $k['TBLP_SGNA_CODE'];
echo '<td style="width: 100px">'.$k['TBLP_SGNA_CODE'].'</td>';
echo '<td style="width: 300px">'.$k['TBLP_SGNA_NAME'].'</td>';
echo '<td style="width: 200px">';
echo '<a class="button-view pure-button" onclick="viewcode(\''.$policode.'\');">Update</a>';
echo '<a class="button-delete pure-button" 
              onclick="if (confirm (\'Are You Sure To Delete ?\'))
              { hapuscode(\''.$policode.'\');}
              else
              { document.getElementById(\'txtsgnaname\').focus();}
              ">Delete</a>';

echo '</td>';
echo '</tr>';
}
?>
  </tbody>
  </table>








