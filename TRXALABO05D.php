<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();
include "conf/config.php";
include "inc/sanie.php";

$rawdata = isset($_POST['q']) ? $_POST['q'] : '';
list($regicode, $hasilid) = array_pad(explode("|", $rawdata), 2, '');

$userid = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$dateinput = date("Y-m-d");
$timeinput = date("H:i:s");

if ($regicode !== '' && $hasilid !== '') {
    $update = "UPDATE trxalabo_detail_hasil
               SET HASIL_VIEW_STAT='N',
                   HASIL_UPDT_DATE=:d,
                   HASIL_UPDT_TIME=:t,
                   HASIL_UPDT_USER=:u
               WHERE TRXA_LABO_REGI=:regi AND HASIL_ID=:id";
    $query_update = $db->prepare($update);
    $db->beginTransaction();
    $query_update->execute(array(
        ':d' => $dateinput,
        ':t' => $timeinput,
        ':u' => $userid,
        ':regi' => $regicode,
        ':id' => $hasilid
    ));
    $db->commit();
}
?>
