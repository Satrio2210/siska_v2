<?php
include "conf/config.php";
?>


<link rel="stylesheet" href="assets/css/modern-table.css">
  <table id="screen" class="modern-table">
  <thead>
  <tr>
  <th style="width: 47px;">Antri</th>
  <th style="width: 100px;">R.M.</th>
  <th style="width: 100px;">Tanggal</th>
  <th style="width: 200px;">Nama</th>
  <th style="width: 100px;">Status</th>
  </tr>
  </thead>
  <tbody>
<?php
  $rawdata = $_POST['q'];
  list($kata, $dokter) = explode("|",$rawdata);

  if (strlen($kata) == 1)
  {
  $xquery = "SELECT TRXA_REGI_CODE, TRXA_PATI_CODE, DATE_FORMAT(TRXA_REGI_DATE,'%d/%m/%Y') AS REGI_DATE,
                (SELECT PATI_MAIN_NAME FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_NAME,
                (SELECT PATI_MAIN_BIRT FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_AGE,
                (SELECT PATI_MAIN_GEND FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_GEND,
                (SELECT COUNT(*) FROM trxatret WHERE TRXA_TRET_CODE=TRXA_REGI_CODE AND TRXA_VIEW_STAT='Y') AS TOTA_TRET,
                TRXA_REGI_LIST, TRXA_REGI_PAYM, TRXA_REGI_POLI
                FROM trxaregi
                WHERE TRXA_REGI_DOCT = '$dokter' AND TRXA_REGI_STAT IN('W','C')
                AND TRXA_ENTR_DATE > DATE_SUB(CURDATE(), INTERVAL 3 DAY)
                ORDER BY TRXA_ENTR_DATE DESC, TRXA_ENTR_TIME DESC";    
  }
  else
  {
  $xquery = "SELECT TRXA_REGI_CODE, TRXA_PATI_CODE, DATE_FORMAT(TRXA_REGI_DATE,'%d/%m/%Y') AS REGI_DATE,
                (SELECT PATI_MAIN_NAME FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_NAME,
                (SELECT PATI_MAIN_BIRT FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_AGE,
                (SELECT PATI_MAIN_GEND FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_GEND,
                (SELECT COUNT(*) FROM trxatret WHERE TRXA_TRET_CODE=TRXA_REGI_CODE AND TRXA_VIEW_STAT='Y') AS TOTA_TRET,
                TRXA_REGI_LIST, TRXA_REGI_PAYM, TRXA_REGI_POLI
                FROM trxaregi
                WHERE (SELECT PATI_MAIN_NAME FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) LIKE '$kata%' 
                AND TRXA_REGI_DOCT = '$dokter' AND TRXA_REGI_STAT IN('W','C')
                OR (SELECT PATI_MAIN_NAME FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) LIKE '%$kata%' 
                AND TRXA_REGI_DOCT = '$dokter' AND TRXA_REGI_STAT IN('W','C')
                AND TRXA_ENTR_DATE > DATE_SUB(CURDATE(), INTERVAL 3 DAY)
                ORDER BY TRXA_ENTR_DATE DESC, TRXA_ENTR_TIME DESC";        
  }

$q = $db->query($xquery) or die("Gagal ambil regis !!");
while ($k = $q->fetch(PDO::FETCH_ASSOC))
{
  $outtretcode = $k['TRXA_REGI_CODE'];
  $outpaticode = $k['TRXA_PATI_CODE'];
  $outregidate = $k['REGI_DATE'];
  $outmainname = $k['MAIN_NAME'];
  $mainage = $k['MAIN_AGE'];
  // tanggal lahir
  $tanggal = new DateTime($mainage);

  // tanggal hari ini
  $today = new DateTime('today');

  $y = $today->diff($tanggal)->y;
  $m = $today->diff($tanggal)->m;
  $d = $today->diff($tanggal)->d;
  $outmainage = '' . $y . ' tahun ' . $m . ' bulan ' . $d . ' hari';

  $gender = $k['MAIN_GEND'];
  
  if ($gender == 'M') { $outmaingend = 'Laki Laki';}
  else if ($gender = 'F') { $outmaingend = 'Perempuan';}
  else { $outmaingend = 'No Gender'; }

  $outtotaltret = $k['TOTA_TRET'];

  $outpaymcode = $k['TRXA_REGI_PAYM'];

  if ($outpaymcode == 'U') { $outregipaym = 'Umum'; }
  else if ($outpaymcode == 'B') { $outregipaym = 'BPJS'; }
  else if ($outpaymcode == 'A') { $outregipaym = 'Asuransi'; }
  else if ($outpaymcode == 'P') { $outregipaym = 'Perusahaan'; }
  else { $outregipaym = 'Kosong';}

  $outregipoli = $k['TRXA_REGI_POLI'];
                                                    //isiregi(outtretcode,outpaticode,outmainname,outmaingend,outmainage,outregipaym,outpaymcode)
if ($outtotaltret == 0)
{
  echo '<td style="width: 50px;" onClick="isiregi(\''.$outtretcode.'\',\''.$outpaticode.'\',\''.$outmainname.'\',\''.$outmaingend.'\',\''.$outmainage.'\',\''.$outregipaym.'\',\''.$outpaymcode.'\',\''.$outregipoli.'\');" 
  style="cursor:pointer">'.$k['TRXA_REGI_LIST'].'</td>';

echo '<td style="width: 100px;" onClick="isiregi(\''.$outtretcode.'\',\''.$outpaticode.'\',\''.$outmainname.'\',\''.$outmaingend.'\',\''.$outmainage.'\',\''.$outregipaym.'\',\''.$outpaymcode.'\',\''.$outregipoli.'\');" 
  style="cursor:pointer">'.$k['TRXA_PATI_CODE'].'</td>';

echo '<td style="width: 100px;" onClick="isiregi(\''.$outtretcode.'\',\''.$outpaticode.'\',\''.$outmainname.'\',\''.$outmaingend.'\',\''.$outmainage.'\',\''.$outregipaym.'\',\''.$outpaymcode.'\',\''.$outregipoli.'\');" 
  style="cursor:pointer">'.$k['REGI_DATE'].'</td>';

echo '<td style="width: 200px;" onClick="isiregi(\''.$outtretcode.'\',\''.$outpaticode.'\',\''.$outmainname.'\',\''.$outmaingend.'\',\''.$outmainage.'\',\''.$outregipaym.'\',\''.$outpaymcode.'\',\''.$outregipoli.'\');" 
  style="cursor:pointer">'.$k['MAIN_NAME'].'</td>';

echo '<td style="width: 100px;" onClick="isiregi(\''.$outtretcode.'\',\''.$outpaticode.'\',\''.$outmainname.'\',\''.$outmaingend.'\',\''.$outmainage.'\',\''.$outregipaym.'\',\''.$outpaymcode.'\',\''.$outregipoli.'\');" 
  style="cursor:pointer">'.'Belum di Layani'.'</td>'; 

echo '<tr>';
} 
else
{
echo '<tr style="background-color: #98F7FD;">';
}

echo '<td style="width: 50px;" onClick="isiregi(\''.$outtretcode.'\',\''.$outpaticode.'\',\''.$outmainname.'\',\''.$outmaingend.'\',\''.$outmainage.'\',\''.$outregipaym.'\',\''.$outpaymcode.'\',\''.$outregipoli.'\');" 
      style="cursor:pointer">'.$k['TRXA_REGI_LIST'].'</td>';

echo '<td style="width: 100px;" onClick="isiregi(\''.$outtretcode.'\',\''.$outpaticode.'\',\''.$outmainname.'\',\''.$outmaingend.'\',\''.$outmainage.'\',\''.$outregipaym.'\',\''.$outpaymcode.'\',\''.$outregipoli.'\');" 
      style="cursor:pointer">'.$k['TRXA_PATI_CODE'].'</td>';

echo '<td style="width: 100px;" onClick="isiregi(\''.$outtretcode.'\',\''.$outpaticode.'\',\''.$outmainname.'\',\''.$outmaingend.'\',\''.$outmainage.'\',\''.$outregipaym.'\',\''.$outpaymcode.'\',\''.$outregipoli.'\');" 
      style="cursor:pointer">'.$k['REGI_DATE'].'</td>';

echo '<td style="width: 200px;" onClick="isiregi(\''.$outtretcode.'\',\''.$outpaticode.'\',\''.$outmainname.'\',\''.$outmaingend.'\',\''.$outmainage.'\',\''.$outregipaym.'\',\''.$outpaymcode.'\',\''.$outregipoli.'\');" 
      style="cursor:pointer">'.$k['MAIN_NAME'].'</td>';

echo '<td style="width: 100px;" onClick="isiregi(\''.$outtretcode.'\',\''.$outpaticode.'\',\''.$outmainname.'\',\''.$outmaingend.'\',\''.$outmainage.'\',\''.$outregipaym.'\',\''.$outpaymcode.'\',\''.$outregipoli.'\');" 
      style="cursor:pointer">'.'Sudah di Layani'.'</td>';      

echo '</tr>';
}
?>
  </tbody>
  </table>








