<?php
include "conf/config.php";

$rawdata = $_POST['q'];
list($regicode, $paticode) = array_pad(explode("|", $rawdata), 2, '');

$xquery = "SELECT TRXA_REGI_CODE, TRXA_PATI_CODE,
          (SELECT CONCAT(PATI_MAIN_TITL,' ',PATI_MAIN_NAME) FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_NAME,
          (SELECT DATE_FORMAT(PATI_MAIN_BIRT,'%d/%m/%Y') FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS BIRT_DATE,
          (SELECT PATI_MAIN_BIRT FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_AGE,
          (SELECT PATI_MAIN_GEND FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_GEND,
          TRXA_REGI_PAYM, TRXA_REGI_POLI
          FROM trxaregi
          WHERE TRXA_REGI_CODE = '$regicode' AND TRXA_PATI_CODE = '$paticode'
          LIMIT 1";

$q = $db->query($xquery) or die("Gagal ambil detail regi !!");
$k = $q->fetch(PDO::FETCH_ASSOC);

if (!$k) {
  echo "ERROR|Data pendaftaran tidak ditemukan";
  exit;
}

$outtretcode = $k['TRXA_REGI_CODE'];
$outpaticode = $k['TRXA_PATI_CODE'];
$outmainname = $k['MAIN_NAME'];
$outbirtdate = $k['BIRT_DATE'];
$mainage = $k['MAIN_AGE'];

// umur
$tanggal = new DateTime($mainage);
$today = new DateTime('today');
$y = $today->diff($tanggal)->y;
$m = $today->diff($tanggal)->m;
$d = $today->diff($tanggal)->d;
$outmainage = '' . $y . ' tahun ' . $m . ' bulan ' . $d . ' hari';

// gender
$gender = $k['MAIN_GEND'];
if ($gender == 'M') {
  $outmaingend = 'Laki Laki';
} else if ($gender == 'F') {
  $outmaingend = 'Perempuan';
} else {
  $outmaingend = 'No Gender';
}

// pembayaran
$outpaymcode = $k['TRXA_REGI_PAYM'];
if ($outpaymcode == 'U') {
  $outregipaym = 'Umum';
} else if ($outpaymcode == 'B') {
  $outregipaym = 'BPJS';
} else if ($outpaymcode == 'A') {
  $outregipaym = 'Asuransi';
} else if ($outpaymcode == 'P') {
  $outregipaym = 'Perusahaan';
} else if ($outpaymcode == 'H') {
  $outregipaym = 'Halodoc';
} else {
  $outregipaym = 'Kosong';
}

$outregipoli = $k['TRXA_REGI_POLI'];

// outtretcode|outpaticode|outmainname|outmaingend|outbirtdate|outmainage|outregipaym|outpaymcode|outregipoli
echo $outtretcode . '|' . $outpaticode . '|' . $outmainname . '|' . $outmaingend . '|'
   . $outbirtdate . '|' . $outmainage . '|' . $outregipaym . '|' . $outpaymcode . '|' . $outregipoli;
