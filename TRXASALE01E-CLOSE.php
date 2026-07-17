<?php
/**
 * BPJS Close: tidak ada tambahan layanan/tindakan berbayar.
 * Hanya update status registrasi menjadi sudah bayar (P).
 */
error_reporting(E_ALL & ~E_NOTICE);
session_start();
include "conf/config.php";
include "inc/sanie.php";

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['username'])) {
    echo json_encode(array('status' => 'error', 'message' => 'Session habis'));
    exit;
}

$rawinput = isset($_POST['q']) ? xss_clean($_POST['q']) : '';
list($regicode, $paticode) = array_pad(explode("|", $rawinput), 2, '');

if ($regicode === '') {
    echo json_encode(array('status' => 'error', 'message' => 'Kode registrasi kosong'));
    exit;
}

$userid = $_SESSION['username'];
$dateinput = date("Y-m-d");
$timeinput = date("H:i:s");

// Pastikan pasien BPJS
$qreg = $db->prepare("SELECT TRXA_REGI_PAYM, TRXA_REGI_STAT FROM trxaregi
                      WHERE TRXA_REGI_CODE = :regi AND TRXA_VIEW_STAT = 'Y' LIMIT 1");
$qreg->execute(array(':regi' => $regicode));
$row = $qreg->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo json_encode(array('status' => 'error', 'message' => 'Registrasi tidak ditemukan'));
    exit;
}

if ($row['TRXA_REGI_PAYM'] !== 'B') {
    echo json_encode(array('status' => 'error', 'message' => 'Close hanya untuk pasien BPJS'));
    exit;
}

// Pastikan tidak ada layanan/tindakan berbayar (>0)
$qextra = $db->prepare("SELECT COALESCE(SUM(a.TRXA_MEDI_RATE * a.TRXA_TRET_QUTY), 0)
                        FROM trxatret a
                        LEFT JOIN tblfmedi b ON a.TRXA_MEDI_CODE = b.TBLF_MEDI_CODE
                        WHERE a.TRXA_TRET_CODE = :regi
                          AND b.TBLF_MEDI_TYPE IN ('J','O','N')
                          AND a.TRXA_TRET_STAT = 'I'
                          AND a.TRXA_VIEW_STAT = 'Y'");
$qextra->execute(array(':regi' => $regicode));
$extra = (float)$qextra->fetchColumn();

if ($extra > 0) {
    echo json_encode(array('status' => 'error', 'message' => 'Masih ada layanan/tindakan berbayar. Gunakan Bayar.'));
    exit;
}

try {
    $db->beginTransaction();

    $upd = $db->prepare("UPDATE trxaregi SET TRXA_REGI_STAT = 'P',
                            TRXA_UPDT_DATE = :d,
                            TRXA_UPDT_TIME = :t,
                            TRXA_UPDT_USER = :u
                         WHERE TRXA_REGI_CODE = :regi
                           AND TRXA_VIEW_STAT = 'Y'");
    $upd->execute(array(
        ':d' => $dateinput,
        ':t' => $timeinput,
        ':u' => $userid,
        ':regi' => $regicode
    ));

    // Tutup item tret/bhp/resep status I -> P (tanpa potong stock ulang jika sudah)
    $db->prepare("UPDATE trxatret SET TRXA_TRET_STAT='P', TRXA_UPDT_DATE=:d, TRXA_UPDT_TIME=:t, TRXA_UPDT_USER=:u
                  WHERE TRXA_TRET_CODE=:regi AND TRXA_VIEW_STAT='Y'")
       ->execute(array(':d' => $dateinput, ':t' => $timeinput, ':u' => $userid, ':regi' => $regicode));

    $db->prepare("UPDATE trxacsbl SET TRXA_CSBL_STAT='P', TRXA_UPDT_DATE=:d, TRXA_UPDT_TIME=:t, TRXA_UPDT_USER=:u
                  WHERE TRXA_CSBL_CODE=:regi AND TRXA_VIEW_STAT='Y'")
       ->execute(array(':d' => $dateinput, ':t' => $timeinput, ':u' => $userid, ':regi' => $regicode));

    $db->prepare("UPDATE trxaprsc SET TRXA_PRSC_STAT='P', TRXA_UPDT_DATE=:d, TRXA_UPDT_TIME=:t, TRXA_UPDT_USER=:u
                  WHERE TRXA_PRSC_CODE=:regi AND TRXA_VIEW_STAT='Y'")
       ->execute(array(':d' => $dateinput, ':t' => $timeinput, ':u' => $userid, ':regi' => $regicode));

    $db->commit();
    echo json_encode(array('status' => 'ok', 'message' => 'Pasien BPJS ditutup (sudah bayar)'));
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}
