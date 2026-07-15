<?php
error_reporting(E_ALL & ~E_NOTICE);
session_start();
include "conf/config.php";

if (isset($_POST['q'])) {
    $rawdata = $_POST['q'];
    list($tretcode, $medicode) = explode("|",$rawdata);

    $userid = $_SESSION['username'];
    $dateinput = date("Y-m-d");
    $timeinput = date("G:i:s");

    $update = "UPDATE trxatret SET TRXA_VIEW_STAT='N',
                TRXA_UPDT_DATE='$dateinput',
                TRXA_UPDT_TIME='$timeinput',
                TRXA_UPDT_USER='$userid'    
            WHERE TRXA_TRET_CODE='$tretcode' AND TRXA_MEDI_CODE='$medicode' AND TRXA_VIEW_STAT='Y'";
    $query_update = $db->prepare($update);

    $db->beginTransaction();
    $query_update->execute();
    $db->commit();
}
?>
