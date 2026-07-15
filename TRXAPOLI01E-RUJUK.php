<?php
session_start();
include "conf/config.php";
include "inc/sanie.php";

if (!isset($_SESSION['username'])) {
    echo "ERROR|Unauthorized";
    exit();
}

if (!isset($_POST['q']) || empty($_POST['q'])) {
    echo "ERROR|Parameter kosong";
    exit();
}

$rawdata = xss_clean($_POST['q']);
list($regicode, $rujuk_type, $rujuk_note) = explode("|", $rawdata);

$userid = $_SESSION['username'];
$dateinput = date("Y-m-d");
$timeinput = date("H:i:s");

// Ambil data admisi asal
$orig_stmt = $db->prepare("SELECT TRXA_PATI_CODE, TRXA_REGI_PAYM, TRXA_REGI_FROM FROM trxaregi WHERE TRXA_REGI_CODE = :regicode");
$orig_stmt->execute([':regicode' => $regicode]);
$orig = $orig_stmt->fetch(PDO::FETCH_ASSOC);

if (!$orig) {
    echo "ERROR|Pendaftaran asal tidak ditemukan!";
    exit();
}

$paticode = $orig['TRXA_PATI_CODE'];
$regipaym = $orig['TRXA_REGI_PAYM'];
$regifrom = $orig['TRXA_REGI_FROM'];

try {
    if ($rujuk_type === 'RS') {
        // Opsi 1: Rujuk Rumah Sakit (Eksternal). Hanya update status rujukan eksternal di database.
        $db->beginTransaction();
        $stmt = $db->prepare("UPDATE trxaregi SET 
            TRXA_REGI_RUJUK_TYPE = 'RS', 
            TRXA_REGI_RUJUK_NOTE = :note,
            TRXA_UPDT_DATE = :date,
            TRXA_UPDT_TIME = :time,
            TRXA_UPDT_USER = :user
            WHERE TRXA_REGI_CODE = :regicode");
        $stmt->execute([
            ':note' => $rujuk_note,
            ':date' => $dateinput,
            ':time' => $timeinput,
            ':user' => $userid,
            ':regicode' => $regicode
        ]);
        $db->commit();
        echo "SUCCESS|Rujukan RS berhasil disimpan!";
        exit();

    } else if ($rujuk_type === 'LB') {
        // Opsi 2: Rujuk Laboratorium (Internal). 
        // Pasien otomatis terdaftar ke Admisi Laboratorium dengan dokter tujuan: dr. Herawati Ningsih (HERAW).
        
        $db->beginTransaction();
        
        // Cek apakah hari ini pasien sudah terdaftar di Laboratorium
        $chk = $db->prepare("SELECT COUNT(*) FROM trxaregi WHERE TRXA_PATI_CODE = :paticode AND TRXA_REGI_DATE = :date AND TRXA_REGI_POLI = 'LB' AND TRXA_VIEW_STAT = 'Y'");
        $chk->execute([':paticode' => $paticode, ':date' => $dateinput]);
        $already = $chk->fetchColumn();
        
        if ($already > 0) {
            // Pasien sudah terdaftar hari ini, cukup update flagging
            $stmt = $db->prepare("UPDATE trxaregi SET 
                TRXA_REGI_RUJUK_TYPE = 'LB', 
                TRXA_REGI_RUJUK_NOTE = :note,
                TRXA_UPDT_DATE = :date,
                TRXA_UPDT_TIME = :time,
                TRXA_UPDT_USER = :user
                WHERE TRXA_REGI_CODE = :regicode");
            $stmt->execute([
                ':note' => $rujuk_note,
                ':date' => $dateinput,
                ':time' => $timeinput,
                ':user' => $userid,
                ':regicode' => $regicode
            ]);
            $db->commit();
            echo "SUCCESS|Pasien sudah terdaftar di Laboratorium hari ini. Flagging diupdate.";
            exit();
        }

        // Generate nomor antrian baru untuk Laboratorium (room = LB)
        $sqlNo = "SELECT MAX(CAST(TRXA_REGI_LIST AS UNSIGNED)) AS last_no FROM trxaregi WHERE TRXA_REGI_DATE = :tgl AND TRXA_REGI_POLI = 'LB'";
        $stmtNo = $db->prepare($sqlNo);
        $stmtNo->execute([':tgl' => $dateinput]);
        $rowNo = $stmtNo->fetch(PDO::FETCH_ASSOC);
        $lastNo = !empty($rowNo['last_no']) ? (int)$rowNo['last_no'] : 0;
        $nextNo = $lastNo + 1;
        $regilist = str_pad($nextNo, 3, '0', STR_PAD_LEFT);

        // Generate kode Urut pendaftaran baru (TRXA_REGI_CODE)
        $sqllast = "SELECT TRXA_REGI_CODE FROM trxaregi ORDER BY TRXA_ENTR_DATE DESC, TRXA_ENTR_TIME DESC LIMIT 1";
        $q = $db->query($sqllast);
        $r = $q->fetch(PDO::FETCH_ASSOC);
        $sequcode = !empty($r['TRXA_REGI_CODE']) ? $r['TRXA_REGI_CODE'] : '';
        $xcode = substr($sequcode, -5);
        $int_val = (int)$xcode;
        $int_val++;
        
        $new_regicode = date("dmY") . "-" . sprintf("%05d", $int_val);

        // Insert pendaftaran baru ke Laboratorium (dokter = HERAW, room = LB, status = W)
        $input = "INSERT INTO trxaregi (
            TRXA_REGI_CODE, TRXA_PATI_CODE, TRXA_REGI_DATE,
            TRXA_REGI_LIST, TRXA_REGI_FROM, TRXA_REGI_PAYM,
            TRXA_REGI_DOCT, TRXA_REGI_POLI, TRXA_REGI_FEE,
            TRXA_REGI_STAT, TRXA_VIEW_STAT,
            TRXA_ENTR_DATE, TRXA_ENTR_TIME, TRXA_ENTR_USER,  
            TRXA_UPDT_DATE, TRXA_UPDT_TIME, TRXA_UPDT_USER) 
            VALUES (
            :new_code, :paticode, :regi_date,
            :regilist, :regifrom, :regipaym,
            'HERAW', 'LB', '0',
            'W', 'Y',
            :entr_date, :entr_time, :entr_user,  
            :updt_date, :updt_time, :updt_user)";
            
        $stmt_ins = $db->prepare($input);
        $stmt_ins->execute([
            ':new_code' => $new_regicode,
            ':paticode' => $paticode,
            ':regi_date' => $dateinput,
            ':regilist' => $regilist,
            ':regifrom' => $regifrom,
            ':regipaym' => $regipaym,
            ':entr_date' => $dateinput,
            ':entr_time' => $timeinput,
            ':entr_user' => $userid,
            ':updt_date' => $dateinput,
            ':updt_time' => $timeinput,
            ':updt_user' => $userid
        ]);

        // Update flagging rujukan internal di pendaftaran asal
        $stmt_upd = $db->prepare("UPDATE trxaregi SET 
            TRXA_REGI_RUJUK_TYPE = 'LB', 
            TRXA_REGI_RUJUK_NOTE = :note,
            TRXA_UPDT_DATE = :date,
            TRXA_UPDT_TIME = :time,
            TRXA_UPDT_USER = :user
            WHERE TRXA_REGI_CODE = :regicode");
        $stmt_upd->execute([
            ':note' => $rujuk_note,
            ':date' => $dateinput,
            ':time' => $timeinput,
            ':user' => $userid,
            ':regicode' => $regicode
        ]);
        
        $db->commit();
        echo "SUCCESS|Rujukan LB berhasil didaftarkan!";
        exit();
    } else {
        echo "ERROR|Tipe rujukan tidak dikenal!";
        exit();
    }
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo "ERROR|Gagal memproses rujukan: " . $e->getMessage();
    exit();
}
?>
