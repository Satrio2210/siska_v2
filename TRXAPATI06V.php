<?php
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE);
include "conf/config.php";
?>


<link rel="stylesheet" href="assets/css/modern-table.css">
  <table id="screen" class="modern-table">
  <thead>
  <tr>
  <th style="width: 100px">Kode</th>
  <th style="width: 200px">Nama Tindakan</th>
  <th style="width: 200px">Tarif</th>
  <th style="width: 100px">Tipe</th>
  <th style="width: 100px">Bayar</th>
  <th style="width: 100px">Poli</th>

  </tr>
  </thead>
  <tbody>
<?php
$kata = $_POST['q'];
//$kode = 'ACC';
$panjangkata = strlen($kata);
if ($panjangkata == 1 )
{ 
$xquery = "SELECT TBLF_MEDI_CODE AS MEDI_CODE, TBLF_MEDI_NAME, TBLF_MEDI_RATE, 
                  TBLF_MEDI_ROOM, (SELECT TBLA_POLI_NAME FROM tblapoli WHERE TBLA_POLI_CODE = TBLF_MEDI_ROOM) AS MEDI_ROOM,
                  CASE
                      WHEN TBLF_MEDI_TYPE = 'J' THEN 'Jasa'
                      WHEN TBLF_MEDI_TYPE = 'O' THEN 'Operasi'
                      WHEN TBLF_MEDI_TYPE = 'N' THEN 'Non Operasi'
                      ELSE 'Jenis baru'
                  END AS MEDI_TYPE,
                  CASE
                      WHEN TBLF_MEDI_PAYM = 'U' THEN 'Umum'
                      WHEN TBLF_MEDI_PAYM = 'B' THEN 'BPJS'
                      WHEN TBLF_MEDI_PAYM = 'A' THEN 'Asuransi'
                      WHEN TBLF_MEDI_PAYM = 'P' THEN 'Perusahaan'
                      ELSE 'Pembayaran baru'
                  END AS MEDI_PAYM
                FROM tblfmedi WHERE TBLF_MEDI_ACTI = 'A'
                AND TBLF_VIEW_STAT = 'Y'
                ORDER BY TBLF_MEDI_CODE LIMIT 10"; 
}
else
{ 
$xquery = "SELECT TBLF_MEDI_CODE AS MEDI_CODE, TBLF_MEDI_NAME, TBLF_MEDI_RATE, 
                  TBLF_MEDI_ROOM, (SELECT TBLA_POLI_NAME FROM tblapoli WHERE TBLA_POLI_CODE = TBLF_MEDI_ROOM) AS MEDI_ROOM,
                  CASE
                      WHEN TBLF_MEDI_TYPE = 'J' THEN 'Jasa'
                      WHEN TBLF_MEDI_TYPE = 'O' THEN 'Operasi'
                      WHEN TBLF_MEDI_TYPE = 'N' THEN 'Non Operasi'
                      ELSE 'Jenis baru'
                  END AS MEDI_TYPE,
                  CASE
                      WHEN TBLF_MEDI_PAYM = 'U' THEN 'Umum'
                      WHEN TBLF_MEDI_PAYM = 'B' THEN 'BPJS'
                      WHEN TBLF_MEDI_PAYM = 'A' THEN 'Asuransi'
                      WHEN TBLF_MEDI_PAYM = 'P' THEN 'Perusahaan'
                      ELSE 'Pembayaran baru'
                  END AS MEDI_PAYM
                FROM tblfmedi WHERE 
                TBLF_MEDI_NAME LIKE '$kata%' 
                AND TBLF_MEDI_ACTI = 'A'
                AND TBLF_VIEW_STAT = 'Y'
                OR TBLF_MEDI_NAME LIKE '%$kata%' 
                AND TBLF_MEDI_ACTI = 'A'
                AND TBLF_VIEW_STAT = 'Y'
                ORDER BY TBLF_MEDI_CODE"; 
               
 }

$q = $db->query($xquery) or die("Gagal Maning!!");
while ($row = $q->fetch(PDO::FETCH_ASSOC))
{ 

    echo '<tr>';
    echo '<td style="width: 100px">'.$row['MEDI_CODE'].'</td>';
    echo '<td style="width: 200px">'.$row['TBLF_MEDI_NAME'].'</td>';

    $view_medi_rate = number_format($row['TBLF_MEDI_RATE'],0,',','.');
    echo '<td style="width: 200px">Rp. '.$view_medi_rate.'</td>';

    echo '<td style="width: 100px">'.$row['MEDI_TYPE'].'</td>';
    echo '<td style="width: 100px">'.$row['MEDI_PAYM'].'</td>';
    echo '<td style="width: 100px">'.$row['MEDI_ROOM'].'</td>';


    echo '</tr>';

}
?>

  </tbody>
  </table>








