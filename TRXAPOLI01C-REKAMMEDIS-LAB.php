<?php
include "conf/config.php";
include "inc/sanie.php";
include "inc/lab_filter_rujukan.php";

$labregi = isset($_POST['q']) ? xss_clean($_POST['q']) : '';
if ($labregi === '') {
    echo '<div style="text-align:center;color:#9ca3af;padding:30px;">Kode lab tidak valid.</div>';
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
$qp->execute(array(':regi' => $labregi));
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

// header lab: nama pemeriksaan lab dari regis
$hl = $db->prepare("SELECT
    (SELECT TBLA_POLI_NAME FROM tblapoli WHERE TBLA_POLI_CODE = r.TRXA_REGI_POLI) AS POLI_NAME,
    (SELECT PASS_USER_NAME FROM passiden WHERE PASS_USER_IDEN = r.TRXA_REGI_DOCT) AS DOCT_NAME,
    r.TRXA_REGI_DATE, r.TRXA_ENTR_TIME
    FROM trxaregi r
    WHERE r.TRXA_REGI_CODE = :regi LIMIT 1");
$hl->execute(array(':regi' => $labregi));
$hrow = $hl->fetch(PDO::FETCH_ASSOC);

// hasil lab grouped by pemeriksaan
$groups = array();
$sg = $db->prepare("SELECT h.TRXA_MEDI_CODE AS MEDI_CODE,
        COALESCE(m.TBLF_MEDI_NAME, h.TRXA_MEDI_CODE, h.TEMP_CODE, 'HASIL LAB') AS MEDI_NAME,
        h.ITEM_NAME, h.ITEM_HASIL, h.ITEM_SATUAN, h.ITEM_RUJUKAN, h.ITEM_NOTE
    FROM trxalabo_detail_hasil h
    LEFT JOIN tblfmedi m ON m.TBLF_MEDI_CODE = h.TRXA_MEDI_CODE
    WHERE h.TRXA_LABO_REGI = :regi AND h.HASIL_VIEW_STAT = 'Y'
    ORDER BY h.TRXA_MEDI_CODE, h.ITEM_URUT, h.HASIL_ID");
$sg->execute(array(':regi' => $labregi));
foreach ($sg->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $key = $row['MEDI_CODE'] ?: '_root';
    if (!isset($groups[$key])) {
        $groups[$key] = array('name' => $row['MEDI_NAME'], 'items' => array());
    }
    $groups[$key]['items'][] = $row;
}
?>
<style>
    .rmlab-wrap {
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        color: #111827;
    }

    .rmlab-empty {
        text-align: center;
        color: #9ca3af;
        padding: 30px 10px;
    }

    .rmlab-head {
        background: #f0fdfa;
        border: 1px solid #99f6e4;
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 14px;
    }

    .rmlab-head b {
        color: #0f766e;
    }

    .rmlab-group-label {
        font-weight: 700;
        color: #0f766e;
        margin: 12px 0 6px;
        font-size: 13px;
    }

    .rmlab-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
        margin-bottom: 12px;
    }

    .rmlab-table th {
        background: #169C89;
        color: #fff;
        font-weight: 700;
        font-size: 12px;
        padding: 8px 10px;
        text-align: center;
        border: none;
    }

    .rmlab-table td {
        padding: 7px 10px;
        text-align: center;
        color: #1f2937;
        font-weight: 500;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .rmlab-table tbody tr:nth-child(even) {
        background: #f9fafb;
    }

    .rmlab-table td.rmlab-abn {
        font-weight: 700;
    }
</style>

<div class="rmlab-wrap">
    <?php if (count($groups) === 0) { ?>
        <div class="rmlab-empty">Belum ada hasil laboratorium yang diinput.</div>
    <?php } else { ?>
        <?php if ($hrow) { ?>
            <div class="rmlab-head">
                <b>Hasil Pemeriksaan Laboratorium</b><br>
                No. Lab: <?php echo htmlspecialchars($labregi, ENT_QUOTES, 'UTF-8'); ?>
                &nbsp;&middot;&nbsp;
                Tanggal: <?php echo htmlspecialchars($hrow['TRXA_REGI_DATE'] . ' ' . $hrow['TRXA_ENTR_TIME'], ENT_QUOTES, 'UTF-8'); ?>
                &nbsp;&middot;&nbsp;
                Dokter: <?php echo htmlspecialchars($hrow['DOCT_NAME'] ?: '-', ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php } ?>

        <?php foreach ($groups as $grp) {
            $no = 0; ?>
            <div class="rmlab-group-label"><?php echo htmlspecialchars($grp['name'], ENT_QUOTES, 'UTF-8'); ?></div>
            <table class="rmlab-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Item</th>
                        <th>Hasil</th>
                        <th>Satuan</th>
                        <th>Nilai Normal</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grp['items'] as $it) {
                        $no++;
                        $rujukan = filter_lab_rujukan($it['ITEM_RUJUKAN'], $pati_umur, $pati_gend);
                        $hasil = format_lab_hasil_flag($it['ITEM_HASIL'], $rujukan);
                        $isAbn = is_lab_hasil_abnormal($it['ITEM_HASIL'], $rujukan);
                        $rujukan = str_replace(array("\r\n", "\n", "\r"), ' / ', (string) $rujukan);
                        ?>
                        <tr>
                            <td><?php echo $no; ?></td>
                            <td style="text-align:left;"><?php echo htmlspecialchars($it['ITEM_NAME'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="<?php echo $isAbn ? 'rmlab-abn' : ''; ?>"><?php echo htmlspecialchars($hasil, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($it['ITEM_SATUAN'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($rujukan, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars((string) $it['ITEM_NOTE'], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    <?php } ?>
</div>
