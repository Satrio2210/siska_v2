<link rel="stylesheet" href="assets/css/modern-table.css">
<?php
include "conf/config.php";
include "inc/sanie.php";
include "inc/lab_filter_rujukan.php";

$kodelb = isset($_POST['q']) ? xss_clean($_POST['q']) : '';
if ($kodelb === '') {
    exit;
}

// umur + gender pasien untuk filter rujukan
$pati_umur = null;
$pati_gend = '';
$qp = $db->prepare("SELECT p.PATI_MAIN_GEND AS GEND, p.PATI_MAIN_BIRT AS BIRT
                    FROM trxaregi r
                    INNER JOIN patimast p ON p.PATI_MAST_CODE = r.TRXA_PATI_CODE
                    WHERE r.TRXA_REGI_CODE = :regi AND r.TRXA_VIEW_STAT = 'Y'
                    LIMIT 1");
$qp->execute(array(':regi' => $kodelb));
$rp = $qp->fetch(PDO::FETCH_ASSOC);
if ($rp) {
    $pati_gend = strtoupper(trim((string) $rp['GEND']));
    if (!empty($rp['BIRT']) && $rp['BIRT'] !== '0000-00-00') {
        try {
            $lahir = new DateTime($rp['BIRT']);
            $today = new DateTime('today');
            $pati_umur = (int) $today->diff($lahir)->y;
        } catch (Exception $e) {
            $pati_umur = null;
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

    #tblresult-labo {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    #tblresult-labo thead {
        background: #10b981;
        color: white;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    #tblresult-labo th {
        padding: 6px 6px;
        font-size: 13px;
        font-weight: 700;
        text-align: center;
        vertical-align: middle;
        border: none;
        letter-spacing: 0.3px;
    }

    #tblresult-labo td {
        padding: 8px 6px;
        font-size: 12px;
        /* color: #374151; */
        text-align: center;
        vertical-align: middle;
        border-bottom: 1px solid #e5e7eb;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    #tblresult-labo th:nth-child(1),
    #tblresult-labo td:nth-child(1) {
        width: 5%;
    }

    #tblresult-labo th:nth-child(2),
    #tblresult-labo td:nth-child(2) {
        width: 20%;
    }

    #tblresult-labo th:nth-child(3),
    #tblresult-labo td:nth-child(3) {
        width: 20%;
    }

    #tblresult-labo th:nth-child(4),
    #tblresult-labo td:nth-child(4) {
        width: 10%;
    }

    #tblresult-labo th:nth-child(5),
    #tblresult-labo td:nth-child(5) {
        width: 25%;
    }

    #tblresult-labo th:nth-child(6),
    #tblresult-labo td:nth-child(6) {
        width: 15%;
    }

    #tblresult-labo th:nth-child(7),
    #tblresult-labo td:nth-child(7) {
        width: 5%;
    }
</style>

<div class="table-container">
    <table id="tblresult-labo">
        <thead>
            <tr>
                <th>No</th>
                <th>Pemeriksaan</th>
                <th>Item</th>
                <th>Hasil</th>
                <th>Satuan</th>
                <th>Rujukan</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $xquery = "SELECT h.HASIL_ID, h.TRXA_MEDI_CODE,
           COALESCE(m.TBLF_MEDI_NAME, h.TRXA_MEDI_CODE, h.TEMP_CODE, '-') AS MEDI_NAME,
           h.ITEM_NAME, h.ITEM_HASIL, h.ITEM_SATUAN, h.ITEM_RUJUKAN, h.ITEM_NOTE
           FROM trxalabo_detail_hasil h
           LEFT JOIN tblfmedi m ON m.TBLF_MEDI_CODE = h.TRXA_MEDI_CODE
           WHERE h.TRXA_LABO_REGI = :regi
             AND h.HASIL_VIEW_STAT = 'Y'
           ORDER BY h.TRXA_MEDI_CODE, h.ITEM_URUT, h.HASIL_ID";

            $q = $db->prepare($xquery);
            $q->execute(array(':regi' => $kodelb));
            $no = 0;
            while ($k = $q->fetch(PDO::FETCH_ASSOC)) {
                $no++;
                $hasilid = $k['HASIL_ID'];
                $rujukan = filter_lab_rujukan($k['ITEM_RUJUKAN'], $pati_umur, $pati_gend);
                $hasil = format_lab_hasil_flag($k['ITEM_HASIL'], $rujukan);
                $is_abn = is_lab_hasil_abnormal($k['ITEM_HASIL'], $rujukan);

                echo '<tr>';
                echo '<td>' . $no . '</td>';
                echo '<td>' . htmlspecialchars($k['MEDI_NAME']) . '</td>';
                echo '<td>' . htmlspecialchars($k['ITEM_NAME']) . '</td>';
                echo '<td style="text-align:center;' . ($is_abn ? ' font-weight:700;' : '') . '">' . htmlspecialchars($hasil) . '</td>';
                echo '<td>' . htmlspecialchars($k['ITEM_SATUAN']) . '</td>';
                echo '<td>' . htmlspecialchars($rujukan) . '</td>';
                echo '<td>';
                echo '<a class="btn-icon-del" onclick="if(confirm(\'Hapus item hasil ini?\')){hapuscode(\'' . htmlspecialchars($kodelb) . '\',\'' . $hasilid . '\');}"><i class="bi bi-trash"></i></a>';
                echo '</td>';
                echo '</tr>';
            }

            if ($no === 0) {
                echo '<tr><td colspan="7" style="text-align:center;color:#888;">Belum ada hasil yang disimpan</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>