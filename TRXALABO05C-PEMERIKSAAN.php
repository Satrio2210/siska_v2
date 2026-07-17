<?php
session_start();
include "conf/config.php";
include "inc/sanie.php";

header('Content-Type: application/json; charset=utf-8');

$regicode = isset($_POST['regicode']) ? xss_clean($_POST['regicode']) : '';
if ($regicode === '') {
    echo json_encode(array('status' => 'error', 'message' => 'regicode kosong', 'data' => array()));
    exit;
}

$query = "SELECT t.TRXA_MEDI_CODE AS medi_code,
                 COALESCE(m.TBLF_MEDI_NAME, t.TRXA_MEDI_CODE) AS medi_name,
                 COALESCE(tpl.TEMP_CODE, '') AS temp_code,
                 COALESCE(tpl.TEMP_NAME, '') AS temp_name
          FROM trxatret t
          LEFT JOIN tblfmedi m ON m.TBLF_MEDI_CODE = t.TRXA_MEDI_CODE AND m.TBLF_VIEW_STAT = 'Y'
          LEFT JOIN mst_lab_template tpl
                 ON (tpl.TEMP_MEDI_CODE = t.TRXA_MEDI_CODE
                     OR UPPER(tpl.TEMP_NAME) = UPPER(m.TBLF_MEDI_NAME))
                AND tpl.TEMP_VIEW_STAT = 'Y'
          WHERE t.TRXA_TRET_CODE = :regicode
            AND t.TRXA_VIEW_STAT = 'Y'
            AND (t.TRXA_MEDI_ROOM = :labroom OR t.TRXA_MEDI_ROOM IS NULL OR t.TRXA_MEDI_ROOM = '')
          ORDER BY t.TRXA_ENTR_DATE, t.TRXA_ENTR_TIME, t.TRXA_MEDI_CODE";

$stmt = $db->prepare($query);
$stmt->execute(array(':regicode' => $regicode, ':labroom' => $code_lab_room));
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$rows) {
    $query2 = "SELECT t.TRXA_MEDI_CODE AS medi_code,
                      COALESCE(m.TBLF_MEDI_NAME, t.TRXA_MEDI_CODE) AS medi_name,
                      COALESCE(tpl.TEMP_CODE, '') AS temp_code,
                      COALESCE(tpl.TEMP_NAME, '') AS temp_name
               FROM trxatret t
               LEFT JOIN tblfmedi m ON m.TBLF_MEDI_CODE = t.TRXA_MEDI_CODE AND m.TBLF_VIEW_STAT = 'Y'
               LEFT JOIN mst_lab_template tpl
                      ON (tpl.TEMP_MEDI_CODE = t.TRXA_MEDI_CODE
                          OR UPPER(tpl.TEMP_NAME) = UPPER(m.TBLF_MEDI_NAME))
                     AND tpl.TEMP_VIEW_STAT = 'Y'
               WHERE t.TRXA_TRET_CODE = :regicode
                 AND t.TRXA_VIEW_STAT = 'Y'
               ORDER BY t.TRXA_ENTR_DATE, t.TRXA_ENTR_TIME, t.TRXA_MEDI_CODE";
    $stmt2 = $db->prepare($query2);
    $stmt2->execute(array(':regicode' => $regicode));
    $rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode(array('status' => 'ok', 'data' => $rows ? $rows : array()));
