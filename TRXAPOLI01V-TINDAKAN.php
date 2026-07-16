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
                $current_hour = (int) date("G"); // Format 24 Jam (0-23)
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
                ':tretcode' => $tretcode,
                ':doct' => $doct_code,
                ':medi' => $default_tret,
                ':rate' => $medi_rate,
                ':room' => $room_code,
                ':entr_date' => $dateinput,
                ':entr_time' => $timeinput,
                ':updt_date' => $dateinput,
                ':updt_time' => $timeinput,
                ':user' => $userid
            ]);
        }
    }
}
?>

<style>
    .table-container {
        overflow: auto;
        border-radius: 16px;
        max-height: 420px;
        border: 1px solid #e2e8f0;
        margin-top: 10px;
    }

    #screen {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    #screen thead {
        background: #10b981;
        color: white;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    #screen th {
        padding: 10px 6px;
        font-size: 13px;
        font-weight: 700;
        text-align: center;
        vertical-align: middle;
        border: none;
        letter-spacing: 0.3px;
    }

    #screen td {
        padding: 8px 6px;
        font-size: 12px;
        /* color: #374151; */
        text-align: center;
        vertical-align: middle;
        border-bottom: 1px solid #e5e7eb;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    #screen th:nth-child(1),
    #screen td:nth-child(1) {
        width: 50%;
    }

    #screen th:nth-child(2),
    #screen td:nth-child(2) {
        width: 25%;
    }

    #screen th:nth-child(3),
    #screen td:nth-child(3) {
        width: 25%;
    }
</style>

<div class="table-container">
    <table id="screen">
        <thead>
            <tr>
                <th>Layanan/Tindakan</th>
                <th>Harga</th>
                <th>Action</th>
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
                    echo '<td>' . htmlspecialchars($k['MEDI_NAME']) . '</td>';
                    echo '<td>Rp ' . number_format($k['TRXA_MEDI_RATE'], 0, ',', '.') . '</td>';
                    echo '<td>';
                    echo '<button type="button" class="btn-modern btn-delete" style="width: 50px; height:30px;" onclick="executeHapusTindakan(\'' . htmlspecialchars($tretcode) . '\', \'' . htmlspecialchars($medicode) . '\')"><i class="bi bi-trash-fill fs-6"></i></button>';
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