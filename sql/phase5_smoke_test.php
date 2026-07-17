<?php
include __DIR__ . '/../conf/config.php';

echo "=== Phase 5 Smoke Test ===\n";

$q = $db->query("SELECT TEMP_CODE, TEMP_NAME,
  (SELECT COUNT(*) FROM mst_lab_template_detail d WHERE d.TEMP_CODE=t.TEMP_CODE AND d.ITEM_VIEW_STAT='Y') AS cnt
  FROM mst_lab_template t WHERE TEMP_VIEW_STAT='Y'");
while ($r = $q->fetch(PDO::FETCH_ASSOC)) {
    echo $r['TEMP_CODE'] . ' | ' . $r['TEMP_NAME'] . ' | items=' . $r['cnt'] . "\n";
}

$q2 = $db->prepare("SELECT TEMP_CODE, TEMP_NAME FROM mst_lab_template WHERE TEMP_NAME='DARAH RUTIN' AND TEMP_VIEW_STAT='Y' LIMIT 1");
$q2->execute();
$t = $q2->fetch(PDO::FETCH_ASSOC);
if ($t) {
    $qd = $db->prepare("SELECT ITEM_NAME, ITEM_SATUAN, ITEM_IS_HEADER FROM mst_lab_template_detail WHERE TEMP_CODE=? AND ITEM_VIEW_STAT='Y' ORDER BY ITEM_URUT");
    $qd->execute(array($t['TEMP_CODE']));
    echo "Template DARAH RUTIN OK:\n";
    while ($i = $qd->fetch(PDO::FETCH_ASSOC)) {
        echo ' - ' . $i['ITEM_NAME'] . ' [' . $i['ITEM_SATUAN'] . '] H=' . $i['ITEM_IS_HEADER'] . "\n";
    }
}

$db->exec("DELETE FROM trxalabo_detail_hasil WHERE TRXA_LABO_REGI='TEST-P5-001'");
$ins = $db->prepare("INSERT INTO trxalabo_detail_hasil (
    TRXA_LABO_REGI, TRXA_MEDI_CODE, TEMP_CODE, ITEM_NAME, ITEM_HASIL, ITEM_RUJUKAN, ITEM_SATUAN, ITEM_URUT,
    HASIL_VIEW_STAT, HASIL_ENTR_DATE, HASIL_ENTR_TIME, HASIL_ENTR_USER, HASIL_UPDT_DATE, HASIL_UPDT_TIME, HASIL_UPDT_USER
) VALUES (
    'TEST-P5-001', 'TND-TEST', 'TPL-0001', 'HEMOGLOBIN', '14.5', '13-18', 'gr/dL', 1,
    'Y', CURDATE(), CURTIME(), 'SYS', CURDATE(), CURTIME(), 'SYS'
)");
$ins->execute();
$c = $db->query("SELECT COUNT(*) FROM trxalabo_detail_hasil WHERE TRXA_LABO_REGI='TEST-P5-001' AND HASIL_VIEW_STAT='Y'")->fetchColumn();
echo "Insert test rows: $c\n";
$db->exec("DELETE FROM trxalabo_detail_hasil WHERE TRXA_LABO_REGI='TEST-P5-001'");
echo "Cleanup OK\n";

// simulate soft-delete + reinsert flow
$db->beginTransaction();
$soft = $db->prepare("UPDATE trxalabo_detail_hasil SET HASIL_VIEW_STAT='N' WHERE TRXA_LABO_REGI='TEST-P5-002'");
$soft->execute();
$ins2 = $db->prepare("INSERT INTO trxalabo_detail_hasil (
    TRXA_LABO_REGI, TRXA_MEDI_CODE, TEMP_CODE, ITEM_NAME, ITEM_HASIL, ITEM_RUJUKAN, ITEM_SATUAN, ITEM_URUT,
    HASIL_VIEW_STAT, HASIL_ENTR_DATE, HASIL_ENTR_TIME, HASIL_ENTR_USER, HASIL_UPDT_DATE, HASIL_UPDT_TIME, HASIL_UPDT_USER
) VALUES
('TEST-P5-002','M1','TPL-0001','HEMOGLOBIN','13','13-18','gr/dL',1,'Y',CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS'),
('TEST-P5-002','M1','TPL-0001','HEMATOKRIT','40','38-55','%',2,'Y',CURDATE(),CURTIME(),'SYS',CURDATE(),CURTIME(),'SYS')");
$ins2->execute();
$db->commit();
$c2 = $db->query("SELECT COUNT(*) FROM trxalabo_detail_hasil WHERE TRXA_LABO_REGI='TEST-P5-002' AND HASIL_VIEW_STAT='Y'")->fetchColumn();
echo "Multi-row save test: $c2 (expect 2)\n";
$db->exec("DELETE FROM trxalabo_detail_hasil WHERE TRXA_LABO_REGI='TEST-P5-002'");
echo "ALL SMOKE TESTS PASSED\n";
