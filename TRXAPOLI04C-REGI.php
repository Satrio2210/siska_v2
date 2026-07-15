<?php
include "conf/config.php";
?>


<link rel="stylesheet" href="assets/css/modern-table.css">
  <table id="screen" class="modern-table">
  <thead>
  <tr>
  <th style="width: 400px;">PASIEN</th>
  </tr>
  </thead>
  <tbody>
<?php
  $rawdata = $_POST['q'];
  list($kata, $dokter) = explode("|",$rawdata);

  if (strlen($kata) == 1)
  {
  $xquery = "SELECT TRXA_REGI_CODE, TRXA_PATI_CODE, 
                (SELECT PATI_MAIN_NAME FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_NAME,
                (SELECT PATI_MAIN_BIRT FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_AGE,
                (SELECT PATI_MAIN_GEND FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_GEND,
                TRXA_REGI_PAYM, TRXA_REGI_POLI
                FROM trxaregi
                WHERE TRXA_REGI_DOCT = '$dokter' AND TRXA_REGI_STAT = 'C'";    
  }
  else
  {
  $xquery = "SELECT TRXA_REGI_CODE, TRXA_PATI_CODE, 
                (SELECT PATI_MAIN_NAME FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_NAME,
                (SELECT PATI_MAIN_BIRT FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_AGE,
                (SELECT PATI_MAIN_GEND FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_GEND,
                TRXA_REGI_PAYM, TRXA_REGI_POLI
                FROM trxaregi
                WHERE (SELECT PATI_MAIN_NAME FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) LIKE '$kata%' 
                AND TRXA_REGI_DOCT = '$dokter' AND TRXA_REGI_STAT = 'C'";        
  }

$q = $db->query($xquery) or die("Gagal ambil regis !!");
while ($k = $q->fetch(PDO::FETCH_ASSOC))
{
  $outprsccode = $k['TRXA_REGI_CODE'];
  $outpaticode = $k['TRXA_PATI_CODE'];
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

  $outpaymcode = $k['TRXA_REGI_PAYM'];

  if ($outpaymcode == 'U') { $outregipaym = 'Umum'; }
  else if ($outpaymcode == 'B') { $outregipaym = 'BPJS'; }
  else if ($outpaymcode == 'A') { $outregipaym = 'Asuransi'; }
  else if ($outpaymcode == 'P') { $outregipaym = 'Perusahaan'; }
  else { $outregipaym = 'Kosong';}

  $outregipoli = $k['TRXA_REGI_POLI'];
                                                    //isiregi(outcsblcode,outpaticode,outmainname,outmaingend,outmainage,outregipaym,outpaymcode)
echo '<tr>';

echo '<td style="width: 100px;" onClick="isiregi(\''.$outprsccode.'\',\''.$outpaticode.'\',\''.$outmainname.'\',\''.$outmaingend.'\',\''.$outmainage.'\',\''.$outregipaym.'\',\''.$outpaymcode.'\',\''.$outregipoli.'\');" 
      style="cursor:pointer">'.$k['TRXA_REGI_CODE'].'</td>';

echo '<td style="width: 100px;" onClick="isiregi(\''.$outprsccode.'\',\''.$outpaticode.'\',\''.$outmainname.'\',\''.$outmaingend.'\',\''.$outmainage.'\',\''.$outregipaym.'\',\''.$outpaymcode.'\',\''.$outregipoli.'\');" 
      style="cursor:pointer">'.$k['TRXA_PATI_CODE'].'</td>';

echo '<td style="width: 150px;" onClick="isiregi(\''.$outprsccode.'\',\''.$outpaticode.'\',\''.$outmainname.'\',\''.$outmaingend.'\',\''.$outmainage.'\',\''.$outregipaym.'\',\''.$outpaymcode.'\');" 
      style="cursor:pointer">'.$k['MAIN_NAME'].'</td>';

echo '</tr>';
}
?>
  </tbody>
  </table>








