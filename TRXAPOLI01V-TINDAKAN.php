<?php
session_start();
include "conf/config.php";
include "inc/sanie.php";

$tretcode = xss_clean($_POST['q']);

if (!empty($tretcode)) {
    // 1. Cek apakah sudah ada tindakan (aktif maupun terhapus) untuk pendaftaran ini
    $check_query = "SELECT COUNT(*) FROM trxatret WHERE TRXA_TRET_CODE = :tretcode";
    $chk_stmt = $db->prepare($check_query);
    $chk_stmt->execute([':tretcode' => $tretcode]);
    $existing_count = $chk_stmt->fetchColumn();

    if ($existing_count == 0) {
        $reg_query = "SELECT TRXA_REGI_PAYM, TRXA_REGI_POLI, TRXA_REGI_DOCT FROM trxaregi WHERE TRXA_REGI_CODE = :tretcode";
        $reg_stmt = $db->prepare($reg_query);
        $reg_stmt->execute([':tretcode' => $tretcode]);
        $reg_data = $reg_stmt->fetch(PDO::FETCH_ASSOC);

        if ($reg_data) {
            $paym_method = $reg_data['TRXA_REGI_PAYM'];
            $room_code = $reg_data['TRXA_REGI_POLI'];
            $doct_code = $reg_data['TRXA_REGI_DOCT'];
            
            // Default treatment logic
            if ($paym_method === 'B') {
                $default_tret = 'TND-0275';
            } else {
                $current_hour = (int)date("G"); // Format 24 Jam (0-23)
                if ($current_hour < 21) {
                    $default_tret = 'TND-0001';
                } else {
                    $default_tret = 'TND-0497';
                }
            }

            // Ambil tarif tindakan default dari master data tblfmedi
            $medi_query = "SELECT TBLF_MEDI_RATE FROM tblfmedi WHERE TBLF_MEDI_CODE = :code";
            $medi_stmt = $db->prepare($medi_query);
            $medi_stmt->execute([':code' => $default_tret]);
            $medi_rate = $medi_stmt->fetchColumn();
            if ($medi_rate === false) {
                $medi_rate = 0.00;
            }

            // Insert tindakan otomatis
            $userid = isset($_SESSION['username']) ? $_SESSION['username'] : 'DOCT';
            $dateinput = date("Y-m-d");
            $timeinput = date("H:i:s");

            $insert_sql = "INSERT INTO trxatret (
                TRXA_TRET_CODE, TRXA_TRET_DOCT, TRXA_MEDI_CODE,
                TRXA_MEDI_RATE, TRXA_TRET_QUTY, TRXA_MEDI_ROOM,
                TRXA_TRET_STAT, TRXA_VIEW_STAT, 
                TRXA_ENTR_DATE, TRXA_ENTR_TIME, TRXA_ENTR_USER,
                TRXA_UPDT_DATE, TRXA_UPDT_TIME, TRXA_UPDT_USER
            ) VALUES (
                :tretcode, :doct, :medi, :rate, 1, :room,
                'I', 'Y',
                :entr_date, :entr_time, :user,
                :updt_date, :updt_time, :user
            )";
            $ins_stmt = $db->prepare($insert_sql);
            $ins_stmt->execute([
                ':tretcode'  => $tretcode,
                ':doct'      => $doct_code,
                ':medi'      => $default_tret,
                ':rate'      => $medi_rate,
                ':room'      => $room_code,
                ':entr_date' => $dateinput,
                ':entr_time' => $timeinput,
                ':updt_date' => $dateinput,
                ':updt_time' => $timeinput,
                ':user'      => $userid
            ]);
        }
    }
}
?>

<link rel="stylesheet" href="assets/css/modern-table.css">
<div style="overflow-x: auto; width: 100%;">
  <table class="treatment-table">
    <thead>
      <tr>
        <th style="width: 60%; text-align: left;">Layanan/Tindakan</th>
        <th style="width: 25%; text-align: center;">Harga</th>
        <th style="width: 15%; text-align: center;">Action</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $xquery = "SELECT TRXA_TRET_CODE, TRXA_MEDI_CODE, 
               (SELECT TBLF_MEDI_NAME FROM tblfmedi WHERE TBLF_MEDI_CODE = TRXA_MEDI_CODE) AS MEDI_NAME,
               TRXA_MEDI_RATE, TRXA_TRET_QUTY, (TRXA_MEDI_RATE * TRXA_TRET_QUTY) AS TOTAL
              FROM trxatret 
              WHERE TRXA_TRET_STAT='I' AND TRXA_TRET_CODE=:tretcode AND TRXA_VIEW_STAT='Y'
              ORDER BY TRXA_MEDI_CODE";
              
    $query_stmt = $db->prepare($xquery);
    $query_stmt->execute([':tretcode' => $tretcode]);
    $rows = $query_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($rows) > 0) {
        foreach ($rows as $k) {
            $medicode = $k['TRXA_MEDI_CODE'];
            echo '<tr>';
            echo '<td style="text-align: left;">' . htmlspecialchars($k['MEDI_NAME']) . '</td>';
            echo '<td style="text-align: Center;">Rp ' . number_format($k['TRXA_MEDI_RATE'], 0, ',', '.') . '</td>';
            echo '<td style="text-align: center;">';
            echo '<button type="button" class="btn-delete-action" onclick="executeHapusTindakan(\'' . htmlspecialchars($tretcode) . '\', \'' . htmlspecialchars($medicode) . '\')">Delete</button>';
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="3" style="text-align: center; color: #9ca3af; padding: 20px;">Belum ada tindakan</td></tr>';
    }
    ?>
    </tbody>
  </table>
</div>



