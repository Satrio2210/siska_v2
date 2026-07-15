<?php
include "conf/config.php";
?>

<style>
    body {
        background: #f3f4f6;
    }

    #screen {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
        font-size: 13px;
        background: #fff;
    }

    #screen thead th {
        padding: 10px 10px 8px;
        text-align: left;
        font-size: 13px;
        font-weight: 700;
        color: #ffffff;
        border-bottom: 1px solid #d1d5db;
        background: #10b981;
    }

    #screen td {
        padding: 7px 10px;
        color: #374151;
        vertical-align: top;
        border-bottom: 1px solid #e5e7eb;
        word-break: break-word;
    }

    #screen tr:nth-child(even) {
        background: #f9fafb;
    }

    #screen tr:hover {
        background: #f3f4f6;
    }

    #screen th:nth-child(1),
    #screen td:nth-child(1) {
        width: 72px;
    }

    #screen th:nth-child(2),
    #screen td:nth-child(2) {
        width: 210px;
    }

    #screen th:nth-child(3),
    #screen td:nth-child(3) {
        width: 95px;
    }

    #screen th:nth-child(4),
    #screen td:nth-child(4) {
        width: 88px;
    }

    #screen th:nth-child(5),
    #screen td:nth-child(5) {
        width: 155px;
    }

    #screen th:nth-child(6),
    #screen td:nth-child(6) {
        width: 95px;
    }

    #screen th:nth-child(7),
    #screen td:nth-child(7) {
        width: 155px;
    }

    #screen th:nth-child(8),
    #screen td:nth-child(8) {
        width: 95px;
    }

    #screen th:nth-child(9),
    #screen td:nth-child(9) {
        width: 110px;
    }

    .button-view.pure-button,
    .button-view {
        background: #e5e7eb;
        color: #374151;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
        box-shadow: none;
    }

    .button-view:hover {
        background: #d1d5db;
        color: #111827;
    }

    .table-wrap {
        overflow: auto;
        border-radius: 16px;
        max-height: 420px;
        border: 1px solid #e2e8f0;
    }
</style>
<link rel="stylesheet" href="assets/css/modern-table.css">


<div class="table-wrap">
    <table id="screen" class="modern-table">
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
                echo '<td style="width: 100px">' . $k['TRXA_REGI_LIST'] . '</td>';
                echo '<td style="width: 200px; text-align: left;">' . $k['PATI_NAME'] . '</td>';
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

                echo '<td style="width: 100px">' . $regipaym . '</td>';
                echo '<td style="width: 200px; text-align: left;">' . $k['DOCT_NAME'] . '</td>';
                echo '<td style="width: 100px">' . $k['POLI_NAME'] . '</td>';
                // echo '<td style="width: 200px">' . $k['ENTR_USER'] . '</td>';
                $sudah_periksa = $k['SUDAH_PERIKSA'];
                $registat = $k['TRXA_REGI_STAT'];
                // if ($registat == 'W') 
//   { 
//     echo '<td style="width: 100px; background-color: #fbf705;"><b>Antri</b></td>';
//   }
// else if ($registat == 'C') 
//   { 
//     echo '<td style="width: 100px; background-color: #64cdcd;"><b>Periksa</b></td>';
//   }
// else if ($registat == 'P') 
//   { 
//     echo '<td style="width: 100px; background-color: #87fd65;"><b>Bayar</b></td>';
//   }
// else 
//   { 
//     echo '<td style="width: 100px;">No Status</b></td>';
//   }
            
                if ($registat == 'W') {
                    // Kalo subquery nemu datanya di trxaexam (artinya sudah diklik submit/simpan)
                    if ($sudah_periksa > 0) {
                        echo '<td style="width: 100px; background-color: #059669; color: white;">Siap Diperiksa</td>';
                    }
                    // Kalo belum ada data pemeriksaan awal di trxaexam
                    else {
                        echo '<td style="width: 100px; background-color: #ffc107; color: black;">Menunggu Skrining</td>';
                    }
                } else {
                    echo '<td style="width: 100px;">Bukan Antrian</td>';
                }


                echo '<td style="width: 200px">';
                echo '<a class="button-view pure-button" onclick="viewcode(\'' . $regicode . '\');">Periksa</a>';
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>







