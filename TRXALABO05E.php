<?php
session_start();
include "conf/config.php";
include "inc/sanie.php";

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['username'])) {
    echo json_encode(array('status' => 'error', 'message' => 'Session habis, silakan login ulang'));
    exit;
}

$userid = $_SESSION['username'];
$dateinput = date("Y-m-d");
$timeinput = date("H:i:s");

$laboregi = isset($_POST['laboregi']) ? xss_clean($_POST['laboregi']) : '';
$labodoct = isset($_POST['labodoct']) ? xss_clean($_POST['labodoct']) : $userid;
$medicode = isset($_POST['medicode']) ? xss_clean($_POST['medicode']) : '';
$tempcode = isset($_POST['tempcode']) ? xss_clean($_POST['tempcode']) : '';
$labonote = isset($_POST['labonote']) ? xss_clean($_POST['labonote']) : '';

$item_name = isset($_POST['item_name']) ? $_POST['item_name'] : array();
$item_hasil = isset($_POST['item_hasil']) ? $_POST['item_hasil'] : array();
$item_rujukan = isset($_POST['item_rujukan']) ? $_POST['item_rujukan'] : array();
$item_satuan = isset($_POST['item_satuan']) ? $_POST['item_satuan'] : array();
$item_dtl_id = isset($_POST['item_dtl_id']) ? $_POST['item_dtl_id'] : array();
$item_is_header = isset($_POST['item_is_header']) ? $_POST['item_is_header'] : array();

if ($laboregi === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Nomor daftar kosong'));
    exit;
}

if (!is_array($item_name) || count($item_name) === 0) {
    echo json_encode(array('status' => 'error', 'message' => 'Tidak ada item hasil untuk disimpan'));
    exit;
}

$rows = array();
$count = count($item_name);
for ($i = 0; $i < $count; $i++) {
    $name = isset($item_name[$i]) ? trim(xss_clean($item_name[$i])) : '';
    $hasil = isset($item_hasil[$i]) ? trim(xss_clean($item_hasil[$i])) : '';
    $rujukan = isset($item_rujukan[$i]) ? trim(xss_clean($item_rujukan[$i])) : '';
    $satuan = isset($item_satuan[$i]) ? trim(xss_clean($item_satuan[$i])) : '';
    $dtlid = isset($item_dtl_id[$i]) ? trim(xss_clean($item_dtl_id[$i])) : '';
    $isheader = isset($item_is_header[$i]) ? trim(xss_clean($item_is_header[$i])) : 'N';

    if ($name === '') {
        continue;
    }
    if ($isheader === 'Y') {
        continue;
    }
    if ($hasil === '') {
        continue;
    }

    $rows[] = array(
        'name' => $name,
        'hasil' => $hasil,
        'rujukan' => $rujukan,
        'satuan' => $satuan,
        'dtl_id' => ($dtlid !== '' ? (int)$dtlid : null),
        'urut' => count($rows) + 1
    );
}

if (count($rows) === 0) {
    echo json_encode(array('status' => 'error', 'message' => 'Isi minimal 1 hasil item'));
    exit;
}

try {
    $db->beginTransaction();

    $soft = $db->prepare("UPDATE trxalabo_detail_hasil
                          SET HASIL_VIEW_STAT = 'N',
                              HASIL_UPDT_DATE = :d,
                              HASIL_UPDT_TIME = :t,
                              HASIL_UPDT_USER = :u
                          WHERE TRXA_LABO_REGI = :regi
                            AND (TRXA_MEDI_CODE = :medi OR (:medi = '' AND (TRXA_MEDI_CODE IS NULL OR TRXA_MEDI_CODE = '')))
                            AND HASIL_VIEW_STAT = 'Y'");
    $soft->execute(array(
        ':d' => $dateinput,
        ':t' => $timeinput,
        ':u' => $userid,
        ':regi' => $laboregi,
        ':medi' => $medicode
    ));

    $ins = $db->prepare("INSERT INTO trxalabo_detail_hasil (
            TRXA_LABO_REGI, TRXA_MEDI_CODE, TEMP_CODE, DTL_ID,
            ITEM_NAME, ITEM_HASIL, ITEM_RUJUKAN, ITEM_SATUAN, ITEM_URUT, ITEM_NOTE,
            HASIL_VIEW_STAT, HASIL_ENTR_DATE, HASIL_ENTR_TIME, HASIL_ENTR_USER,
            HASIL_UPDT_DATE, HASIL_UPDT_TIME, HASIL_UPDT_USER
        ) VALUES (
            :regi, :medi, :temp, :dtl,
            :name, :hasil, :rujukan, :satuan, :urut, :note,
            'Y', :ed, :et, :eu, :ud, :ut, :uu
        )");

    foreach ($rows as $r) {
        $ins->execute(array(
            ':regi' => $laboregi,
            ':medi' => ($medicode !== '' ? $medicode : null),
            ':temp' => ($tempcode !== '' ? $tempcode : null),
            ':dtl' => $r['dtl_id'],
            ':name' => $r['name'],
            ':hasil' => $r['hasil'],
            ':rujukan' => $r['rujukan'],
            ':satuan' => $r['satuan'],
            ':urut' => $r['urut'],
            ':note' => $labonote,
            ':ed' => $dateinput,
            ':et' => $timeinput,
            ':eu' => $userid,
            ':ud' => $dateinput,
            ':ut' => $timeinput,
            ':uu' => $userid
        ));
    }

    $db->commit();
    echo json_encode(array(
        'status' => 'ok',
        'message' => 'Hasil lab berhasil disimpan (' . count($rows) . ' item)',
        'saved' => count($rows)
    ));
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(array('status' => 'error', 'message' => 'Gagal simpan: ' . $e->getMessage()));
}
