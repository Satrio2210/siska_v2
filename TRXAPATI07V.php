<?php
include "conf/config.php";
?>

<style>
    .table-container {
        overflow: auto;
        border-radius: 16px;
        max-height: 420px;
        border: 1px solid #e2e8f0;
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
        font-size: 11px;
        font-weight: 600;
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
        width: 60px;
    }

    #screen th:nth-child(2),
    #screen td:nth-child(2) {
        width: 200px;
    }

    #screen th:nth-child(3),
    #screen td:nth-child(3) {
        width: 85px;
    }

    #screen th:nth-child(4),
    #screen td:nth-child(4) {
        width: 150px;
    }

    #screen th:nth-child(5),
    #screen td:nth-child(5) {
        width: 90px;
    }

    #screen th:nth-child(6),
    #screen td:nth-child(6) {
        width: 95px;
    }

    #screen th:nth-child(7),
    #screen td:nth-child(7) {
        width: 155px;
    }

    #screen tr {
        transition: .2s;
    }

    #screen tbody tr:hover {
        background: #d0e3f7;
    }

    .badge {
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        display: inline-block;
        white-space: nowrap;
    }

    .badge-primary {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-secondary {
        background: #dcfce7;
        color: #166534;
    }
</style>

<div class="table-container">
    <table id="screen">
        <thead>
            <tr>
                <th>ANTRIAN</th>
                <th>PASIEN</th>
                <th>TIPE</th>
                <th>DOKTER</th>
                <th>POLI</th>
                <th>STATUS</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $kata = $_POST['q'];
            //$kode = 'ACC';
            $panjangkata = strlen($kata);
            if ($panjangkata == 0) {
                $xquery = "SELECT TRXA_REGI_CODE, TRXA_PATI_CODE,
              (SELECT CONCAT(PATI_MAIN_TITL,' ',PATI_MAIN_NAME) FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS PATI_NAME,
              TRXA_REGI_LIST, TRXA_REGI_PAYM, 
              TRXA_REGI_DOCT, (SELECT PASS_USER_NAME FROM passiden WHERE PASS_USER_IDEN = TRXA_REGI_DOCT) AS DOCT_NAME, 
              TRXA_REGI_POLI, (SELECT TBLA_POLI_NAME FROM tblapoli WHERE TBLA_POLI_CODE = TRXA_REGI_POLI) AS POLI_NAME,
              TRXA_REGI_STAT, TRXA_ENTR_USER, 
              (SELECT PASS_USER_NAME FROM passiden WHERE PASS_USER_IDEN=TRXA_ENTR_USER) AS ENTR_USER, 
              (SELECT COUNT(*) FROM trxaexam WHERE TRXA_EXAM_CODE = TRXA_REGI_CODE) AS SUDAH_PERIKSA
              FROM trxaregi
              WHERE TRXA_REGI_STAT = 'W' 
              AND TRXA_VIEW_STAT = 'Y' 
              AND DATE(TRXA_ENTR_DATE) >= CURDATE() - INTERVAL 2 DAY
              ORDER BY TRXA_ENTR_DATE DESC, TRXA_ENTR_TIME DESC";
            } else {
                $xquery = "SELECT TRXA_REGI_CODE, TRXA_PATI_CODE,
              (SELECT CONCAT(PATI_MAIN_TITL,' ',PATI_MAIN_NAME) FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS PATI_NAME,
              TRXA_REGI_LIST, TRXA_REGI_PAYM, 
              TRXA_REGI_DOCT, (SELECT PASS_USER_NAME FROM passiden WHERE PASS_USER_IDEN = TRXA_REGI_DOCT) AS DOCT_NAME, 
              TRXA_REGI_POLI, (SELECT TBLA_POLI_NAME FROM tblapoli WHERE TBLA_POLI_CODE = TRXA_REGI_POLI) AS POLI_NAME,
              TRXA_REGI_STAT, TRXA_ENTR_USER, 
              (SELECT PASS_USER_NAME FROM passiden WHERE PASS_USER_IDEN=TRXA_ENTR_USER) AS ENTR_USER, 
              (SELECT COUNT(*) FROM trxaexam WHERE TRXA_EXAM_CODE = TRXA_REGI_CODE) AS SUDAH_PERIKSA
              FROM trxaregi
              WHERE TRXA_REGI_STAT = 'W'
              AND TRXA_VIEW_STAT = 'Y' 
              AND DATE(TRXA_ENTR_DATE) >= CURDATE() - INTERVAL 2 DAY
              AND (
                  TRXA_PATI_CODE LIKE '%$kata%' 
                  OR (SELECT PATI_MAIN_NAME FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) LIKE '%$kata%' 
              )
              ORDER BY TRXA_REGI_LIST";
            }

            $q = $db->query($xquery) or die("Gagal Maning!!");
            while ($k = $q->fetch(PDO::FETCH_ASSOC)) {
                echo '<tr>';
                $regicode = $k['TRXA_REGI_CODE'];
                echo '<td>' . $k['TRXA_REGI_LIST'] . '</td>';
                echo '<td>' . $k['PATI_NAME'] . '</td>';
                // echo '<td style="width: 100px">' . $k['TRXA_PATI_CODE'] . '</td>';
            
                $xregipaym = $k['TRXA_REGI_PAYM'];
                if ($xregipaym == 'U') {
                    $regipaym = 'Umum';
                } else if ($xregipaym == 'B') {
                    $regipaym = 'BPJS';
                } else if ($xregipaym == 'A') {
                    $regipaym = 'Asuransi';
                } else if ($xregipaym == 'P') {
                    $regipaym = 'Perusahaan';
                } else if ($xregipaym == 'H') {
                    $regipaym = 'Halodoc';
                } else {
                    $regipaym = 'Fail get Payment';
                }

                echo '<td>' . $regipaym . '</td>';
                echo '<td>' . $k['DOCT_NAME'] . '</td>';
                echo '<td>' . $k['POLI_NAME'] . '</td>';
                // echo '>R'] . '</td>';
                $sudah_periksa = $k['SUDAH_PERIKSA'];
                $registat = $k['TRXA_REGI_STAT'];

                echo '<td>';
                if ($registat == 'W') {
                    // Kalo subquery nemu datanya di trxaexam (artinya sudah diklik submit/simpan)
                    if ($sudah_periksa > 0) {
                        echo '<span class="badge badge-secondary">Siap Diperiksa</span>';
                    }
                    // Kalo belum ada data pemeriksaan awal di trxaexam
                    else {
                        echo '<span class="badge badge-primary">Menunggu Skrining</span>';
                    }
                } else {
                    echo '<span>Bukan Antrian</span>';
                }
                echo '</td>';

                echo '<td>';
                echo '<button type="button" class="btn-modern btn-save" style="width: 100px;" onclick="viewcode(\'' . $regicode . '\');">Periksa</button>';
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>