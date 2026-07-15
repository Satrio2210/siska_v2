<?php
include "conf/config.php";

$paticode = $_POST['paticode'];
$regidate = $_POST['regidate'];

$sql = "SELECT 1
        FROM trxaregi
        WHERE TRXA_PATI_CODE = ?
        AND TRXA_REGI_DATE = ?
        AND TRXA_REGI_PAYM = 'B'
        LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->execute([$paticode, $regidate]);

echo $stmt->fetch() ? "EXIST" : "OK";