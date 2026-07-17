<?php
session_start();
include "conf/config.php";
include "inc/sanie.php";
include "inc/lab_filter_rujukan.php";

header('Content-Type: application/json; charset=utf-8');

/**
 * Ambil umur (tahun) & gender pasien dari nomor registrasi lab.
 * Return: array('umur' => int|null, 'gender' => 'M'|'F'|'', 'pati_code' => '')
 */
function get_pasien_umur_gender($db, $laboregi)
{
    $result = array('umur' => null, 'gender' => '', 'pati_code' => '');
    if ($laboregi === '') {
        return $result;
    }

    $q = $db->prepare("SELECT r.TRXA_PATI_CODE,
                              p.PATI_MAIN_GEND AS GEND,
                              p.PATI_MAIN_BIRT AS BIRT
                       FROM trxaregi r
                       INNER JOIN patimast p ON p.PATI_MAST_CODE = r.TRXA_PATI_CODE
                       WHERE r.TRXA_REGI_CODE = :regi
                         AND r.TRXA_VIEW_STAT = 'Y'
                       LIMIT 1");
    $q->execute(array(':regi' => $laboregi));
    $row = $q->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return $result;
    }

    $result['pati_code'] = $row['TRXA_PATI_CODE'];
    $result['gender'] = strtoupper(trim((string)$row['GEND']));

    if (!empty($row['BIRT']) && $row['BIRT'] !== '0000-00-00') {
        try {
            $lahir = new DateTime($row['BIRT']);
            $today = new DateTime('today');
            $result['umur'] = (int)$today->diff($lahir)->y;
        } catch (Exception $e) {
            $result['umur'] = null;
        }
    }

    return $result;
}

// ---------- main ----------
$kode = isset($_POST['kode_pemeriksaan']) ? xss_clean($_POST['kode_pemeriksaan']) : '';
$tempcode = isset($_POST['temp_code']) ? xss_clean($_POST['temp_code']) : '';
$laboregi = isset($_POST['laboregi']) ? xss_clean($_POST['laboregi']) : '';
if ($laboregi === '' && isset($_POST['regicode'])) {
    $laboregi = xss_clean($_POST['regicode']);
}

if ($kode === '' && $tempcode === '') {
    echo json_encode(array('status' => 'error', 'message' => 'kode_pemeriksaan kosong', 'template' => null, 'items' => array()));
    exit;
}

$pasien = get_pasien_umur_gender($db, $laboregi);
$umur = $pasien['umur'];
$gender = $pasien['gender'];

$template = null;

if ($tempcode !== '') {
    $q = $db->prepare("SELECT TEMP_CODE, TEMP_TIPE, TEMP_NAME, TEMP_MEDI_CODE
                       FROM mst_lab_template
                       WHERE TEMP_CODE = :temp AND TEMP_VIEW_STAT = 'Y' LIMIT 1");
    $q->execute(array(':temp' => $tempcode));
    $template = $q->fetch(PDO::FETCH_ASSOC);
}

if (!$template && $kode !== '') {
    $q = $db->prepare("SELECT TEMP_CODE, TEMP_TIPE, TEMP_NAME, TEMP_MEDI_CODE
                       FROM mst_lab_template
                       WHERE TEMP_MEDI_CODE = :kode AND TEMP_VIEW_STAT = 'Y' LIMIT 1");
    $q->execute(array(':kode' => $kode));
    $template = $q->fetch(PDO::FETCH_ASSOC);
}

if (!$template && $kode !== '') {
    $q = $db->prepare("SELECT t.TEMP_CODE, t.TEMP_TIPE, t.TEMP_NAME, t.TEMP_MEDI_CODE
                       FROM mst_lab_template t
                       INNER JOIN tblfmedi m ON UPPER(m.TBLF_MEDI_NAME) = UPPER(t.TEMP_NAME)
                       WHERE m.TBLF_MEDI_CODE = :kode
                         AND t.TEMP_VIEW_STAT = 'Y'
                         AND m.TBLF_VIEW_STAT = 'Y'
                       LIMIT 1");
    $q->execute(array(':kode' => $kode));
    $template = $q->fetch(PDO::FETCH_ASSOC);
}

if (!$template) {
    echo json_encode(array(
        'status' => 'ok',
        'message' => 'Template tidak ditemukan, form kosong siap diisi manual',
        'template' => null,
        'items' => array(),
        'pasien' => array('umur' => $umur, 'gender' => $gender)
    ));
    exit;
}

$qd = $db->prepare("SELECT DTL_ID AS dtl_id,
                           ITEM_NAME AS item_name,
                           ITEM_SATUAN AS item_satuan,
                           ITEM_RUJUKAN AS item_rujukan,
                           ITEM_USIA AS item_usia,
                           ITEM_URUT AS item_urut,
                           ITEM_IS_HEADER AS item_is_header
                    FROM mst_lab_template_detail
                    WHERE TEMP_CODE = :temp
                      AND ITEM_VIEW_STAT = 'Y'
                    ORDER BY ITEM_URUT ASC, DTL_ID ASC");
$qd->execute(array(':temp' => $template['TEMP_CODE']));
$items = $qd->fetchAll(PDO::FETCH_ASSOC);

if ($items) {
    foreach ($items as &$it) {
        $raw = isset($it['item_rujukan']) ? $it['item_rujukan'] : '';
        $it['item_rujukan_raw'] = $raw;
        $it['item_rujukan'] = filter_lab_rujukan($raw, $umur, $gender);
    }
    unset($it);
}

echo json_encode(array(
    'status' => 'ok',
    'template' => array(
        'temp_code' => $template['TEMP_CODE'],
        'temp_tipe' => $template['TEMP_TIPE'],
        'temp_name' => $template['TEMP_NAME'],
        'temp_medi_code' => $template['TEMP_MEDI_CODE']
    ),
    'pasien' => array(
        'umur' => $umur,
        'gender' => $gender,
        'pati_code' => $pasien['pati_code']
    ),
    'items' => $items ? $items : array()
));
