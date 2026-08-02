<?php
include "conf/config.php";

$regicode = isset($_POST['q']) ? trim($_POST['q']) : '';
if ($regicode === '') {
    echo '<div class="rm-empty">Kode pendaftaran tidak valid.</div>';
    exit;
}

$nama_hari_id = array(
    1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
    5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
);

$main_query = "
SELECT 
    r.TRXA_REGI_CODE AS REGI_CODE,
    r.TRXA_PATI_CODE AS PATI_CODE,
    r.TRXA_REGI_DATE,
    r.TRXA_ENTR_DATE,
    r.TRXA_ENTR_TIME,
    r.TRXA_REGI_LIST,
    r.TRXA_REGI_PAYM,
    r.TRXA_REGI_DOCT,
    r.TRXA_REGI_RUJUK_TYPE,
    r.TRXA_REGI_RUJUK_NOTE,
    p.PATI_MAIN_TITL,
    p.PATI_MAIN_NAME,
    p.PATI_MAIN_BIRT,
    p.PATI_MAIN_GEND,
    pl.TBLA_POLI_NAME AS POLI_NAME,
    d.PASS_USER_NAME AS DOCT_NAME,
    e.TRXA_EXAM_HGHT AS TINGGI,
    e.TRXA_EXAM_WGHT AS BERAT,
    e.TRXA_EXAM_WAIST AS LP,
    e.TRXA_EXAM_BMI AS IMT,
    e.TRXA_EXAM_BLOD AS DARAH,
    e.TRXA_EXAM_TEMP AS SUHU,
    e.TRXA_EXAM_RR AS RR,
    e.TRXA_EXAM_HR AS HR,
    e.TRXA_EXAM_COMP AS KELUHAN,
    e.TRXA_EXAM_ANAM AS ANAMNESA,
    e.TRXA_EXAM_BODY AS BODY,
    e.TRXA_EXAM_DIAG AS CATATAN,
    e.TRXA_EXAM_PRSC AS RESEP,
    dg.DIAGNOSA
FROM trxaregi r
LEFT JOIN patimast p ON p.PATI_MAST_CODE = r.TRXA_PATI_CODE
LEFT JOIN tblapoli pl ON pl.TBLA_POLI_CODE = r.TRXA_REGI_POLI
LEFT JOIN passiden d ON d.PASS_USER_IDEN = r.TRXA_REGI_DOCT
LEFT JOIN trxaexam e ON e.TRXA_EXAM_CODE = r.TRXA_REGI_CODE
LEFT JOIN (
    SELECT 
        TRXA_EXAM_CODE, 
        GROUP_CONCAT(CONCAT(TRXA_DIAG_CODE, ' - ', TRXA_DIAG_NAME) SEPARATOR ';<br>') AS DIAGNOSA
    FROM trxadiag
    GROUP BY TRXA_EXAM_CODE
) dg ON dg.TRXA_EXAM_CODE = r.TRXA_REGI_CODE
WHERE r.TRXA_REGI_CODE = :regicode
LIMIT 1
";

$st = $db->prepare($main_query);
$st->execute(array(':regicode' => $regicode));
$k = $st->fetch(PDO::FETCH_ASSOC);

if (!$k) {
    echo '<div class="rm-empty">Data rekam medis tidak ditemukan.</div>';
    exit;
}

function rm_v($val, $suf = '') {
    if ($val === null || trim($val) === '') return '-';
    return trim($val) . $suf;
}

// Header info
$regicode_out = htmlspecialchars($k['REGI_CODE']);
$pati_name = htmlspecialchars(trim(($k['PATI_MAIN_TITL'] ?? '') . ' ' . ($k['PATI_MAIN_NAME'] ?? '')));
$pati_birt = htmlspecialchars($k['PATI_MAIN_BIRT'] ?? '');
$pati_gend = ($k['PATI_MAIN_GEND'] === 'M') ? 'Pria' : (($k['PATI_MAIN_GEND'] === 'F') ? 'Wanita' : '-');
$poli_name = htmlspecialchars($k['POLI_NAME'] ?? '-');
$doct_name = htmlspecialchars($k['DOCT_NAME'] ?? '-');
$rujuk_note = htmlspecialchars(trim($k['TRXA_REGI_RUJUK_NOTE'] ?? ''));

$waktu_db = strtotime($k['TRXA_REGI_DATE']);
$hari_angka = date("N", $waktu_db);
$regidate = htmlspecialchars(($nama_hari_id[$hari_angka] ?? '') . ', ' . date("d-m-Y", $waktu_db));
$entr_time = htmlspecialchars($k['TRXA_ENTR_TIME'] ?? '');

// TTV
$ttv = array(
    array('Tinggi Badan', rm_v($k['TINGGI'], ' cm')),
    array('Berat Badan', rm_v($k['BERAT'], ' kg')),
    array('Lingkar Perut', rm_v($k['LP'], ' cm')),
    array('IMT', rm_v($k['IMT'], ' kg/m2')),
    array('Tekanan Darah', rm_v($k['DARAH'], ' mmHg')),
    array('Suhu Tubuh', rm_v($k['SUHU'], ' C')),
    array('RR', rm_v($k['RR'], ' /menit')),
    array('HR', rm_v($k['HR'], ' bpm')),
);

// Pemeriksaan & Diagnosa
$keluhan = nl2br(htmlspecialchars(rm_v($k['KELUHAN'])));
$anamnesa = nl2br(htmlspecialchars(rm_v($k['ANAMNESA'])));
$body = nl2br(htmlspecialchars(rm_v($k['BODY'])));
$diagnosa = $k['DIAGNOSA'] ? $k['DIAGNOSA'] : '-';
$catatan = nl2br(htmlspecialchars(rm_v($k['CATATAN'])));
$resep_note = nl2br(htmlspecialchars(rm_v($k['RESEP'])));

// Tindakan & Obat
$tindakan_rows = array();
$tt_stmt = $db->prepare("SELECT 
        TRXA_MEDI_CODE,
        (SELECT TBLF_MEDI_NAME FROM tblfmedi WHERE TBLF_MEDI_CODE = t.TRXA_MEDI_CODE) AS MEDI_NAME,
        TRXA_MEDI_RATE, TRXA_TRET_QUTY
    FROM trxatret t
    WHERE TRXA_TRET_CODE = :regicode 
      AND TRXA_TRET_STAT = 'I' 
      AND TRXA_VIEW_STAT = 'Y'
      AND TRXA_MEDI_ROOM NOT IN (:lab, :keb)
    ORDER BY TRXA_MEDI_CODE");
$tt_stmt->execute(array(':regicode' => $regicode, ':lab' => $code_lab_room, ':keb' => $code_keb_room));
$tindakan_rows = $tt_stmt->fetchAll(PDO::FETCH_ASSOC);

$obat_rows = array();
$ob_stmt = $db->prepare("SELECT 
        TRXA_STOCK_CODE,
        (SELECT INVE_PART_NAME FROM invemast WHERE INVE_MAST_CODE = o.TRXA_STOCK_CODE) AS STOCK_NAME,
        (SELECT INVE_SALE_UNIT FROM invemast WHERE INVE_MAST_CODE = o.TRXA_STOCK_CODE) AS UNIT_CODE,
        TRXA_STOCK_PRIC, TRXA_STOCK_QUTY
    FROM trxaprsc o
    WHERE TRXA_PRSC_CODE = :regicode 
      AND TRXA_PRSC_STAT = 'A' 
      AND TRXA_VIEW_STAT = 'Y'
    ORDER BY TRXA_STOCK_CODE");
$ob_stmt->execute(array(':regicode' => $regicode));
$obat_rows = $ob_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
    .rm-modal-wrap {
        font-family: 'Inter', sans-serif;
        font-size: 13px;
        color: #111827;
    }

    .rm-empty {
        text-align: center;
        color: #9ca3af;
        padding: 30px 10px;
    }

    .rm-patient-head {
        background: #f0fdfa;
        border: 1px solid #99f6e4;
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 14px;
    }

    .rm-patient-head h5 {
        margin: 0 0 8px;
        font-size: 15px;
        font-weight: 700;
        color: #0f766e;
    }

    .rm-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 6px 16px;
        font-size: 12px;
    }

    .rm-grid .rm-label {
        font-weight: 700;
        color: #374151;
    }

    .rm-grid .rm-label:after {
        content: ': ';
    }

    .rm-section {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 14px;
        background: #fff;
    }

    .rm-section-title {
        font-size: 13px;
        font-weight: 700;
        color: #0f766e;
        margin: 0 0 10px;
        padding-bottom: 6px;
        border-bottom: 2px solid #10b981;
    }

    .rm-ttv {
        width: 100%;
        border-collapse: collapse;
    }

    .rm-ttv td {
        padding: 5px 8px;
        font-size: 12px;
        vertical-align: top;
        border: 1px solid #f1f5f9;
    }

    .rm-ttv td:first-child {
        font-weight: 700;
        color: #374151;
        width: 40%;
        background: #f8fafc;
    }

    .rm-sub {
        font-weight: 700;
        color: #374151;
        margin: 8px 0 3px;
        font-size: 12px;
    }

    .rm-text {
        margin: 0 0 8px;
        font-size: 12px;
        line-height: 1.55;
        color: #111827;
    }

    .rm-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 4px;
    }

    .rm-table th {
        background: #0d9488;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 8px 10px;
        text-align: center;
        border: none;
    }

    .rm-table td {
        padding: 7px 10px;
        font-size: 12px;
        text-align: center;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .rm-table td:first-child {
        text-align: left;
    }

    .rm-table tbody tr:nth-child(even) {
        background: #f9fafb;
    }

    .rm-none {
        text-align: center;
        color: #9ca3af;
        padding: 14px 8px;
        font-size: 12px;
    }
</style>

<div class="rm-modal-wrap">
    <div class="rm-patient-head">
        <h5>&#x1F9D1; <?php echo $pati_name; ?></h5>
        <div class="rm-grid">
            <div><span class="rm-label">No. Pendaftaran</span><?php echo $regicode_out; ?></div>
            <div><span class="rm-label">Tanggal</span><?php echo $regidate . ' ' . $entr_time; ?></div>
            <div><span class="rm-label">Poli</span><?php echo $poli_name; ?></div>
            <div><span class="rm-label">Dokter</span><?php echo $doct_name; ?></div>
            <div><span class="rm-label">Tgl Lahir</span><?php echo $pati_birt !== '' ? $pati_birt : '-'; ?></div>
            <div><span class="rm-label">JK</span><?php echo $pati_gend; ?></div>
            <div><span class="rm-label">Rujukan RS</span><?php echo $rujuk_note !== '' ? $rujuk_note : '-'; ?></div>
        </div>
    </div>

    <div class="rm-section">
        <div class="rm-section-title">&#x1F4C8; Tanda-tanda Vital</div>
        <table class="rm-ttv">
            <?php foreach ($ttv as $row) { ?>
                <tr><td><?php echo $row[0]; ?></td><td><?php echo $row[1]; ?></td></tr>
            <?php } ?>
        </table>
    </div>

    <div class="rm-section">
        <div class="rm-section-title">&#x1F52C; Pemeriksaan &amp; Diagnosa</div>
        <div class="rm-sub">Keluhan</div>
        <p class="rm-text"><?php echo $keluhan; ?></p>
        <div class="rm-sub">Anamnesa</div>
        <p class="rm-text"><?php echo $anamnesa; ?></p>
        <div class="rm-sub">Pemeriksaan Fisik</div>
        <p class="rm-text"><?php echo $body; ?></p>
        <div class="rm-sub">Diagnosa</div>
        <p class="rm-text"><?php echo $diagnosa; ?></p>
        <?php if ($catatan !== '-') { ?>
            <div class="rm-sub">Catatan Dokter</div>
            <p class="rm-text"><?php echo $catatan; ?></p>
        <?php } ?>
    </div>

    <div class="rm-section">
        <div class="rm-section-title">&#x1F4CC; Tindakan &amp; Obat</div>

        <div class="rm-sub">Tindakan</div>
        <?php if (count($tindakan_rows) > 0) { ?>
            <table class="rm-table">
                <thead>
                    <tr>
                        <th>Layanan / Tindakan</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_tindakan = 0;
                    foreach ($tindakan_rows as $t) {
                        $rate = (float) $t['TRXA_MEDI_RATE'];
                        $qty = (float) $t['TRXA_TRET_QUTY'];
                        $subtotal = $rate * $qty;
                        $total_tindakan += $subtotal;
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($t['MEDI_NAME'] ?? '-') . '</td>';
                        echo '<td>Rp ' . number_format($rate, 0, ',', '.') . '</td>';
                        echo '<td>' . (int) $qty . '</td>';
                        echo '<td>Rp ' . number_format($subtotal, 0, ',', '.') . '</td>';
                        echo '</tr>';
                    }
                    ?>
                    <tr>
                        <td colspan="3" style="text-align:right;font-weight:700;">Subtotal Tindakan</td>
                        <td style="font-weight:700;">Rp <?php echo number_format($total_tindakan, 0, ',', '.'); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="rm-none">Tidak ada data tindakan.</div>
        <?php } ?>

        <div class="rm-sub">Obat / Resep</div>
        <?php if (count($obat_rows) > 0) { ?>
            <table class="rm-table">
                <thead>
                    <tr>
                        <th>Nama Obat</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_obat = 0;
                    foreach ($obat_rows as $o) {
                        $price = (float) $o['TRXA_STOCK_PRIC'];
                        $qty = (float) $o['TRXA_STOCK_QUTY'];
                        $subtotal = $price * $qty;
                        $total_obat += $subtotal;
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($o['STOCK_NAME'] ?? '-') . '</td>';
                        echo '<td>Rp ' . number_format($price, 0, ',', '.') . '</td>';
                        echo '<td>' . (int) $qty . '</td>';
                        echo '<td>Rp ' . number_format($subtotal, 0, ',', '.') . '</td>';
                        echo '</tr>';
                    }
                    ?>
                    <tr>
                        <td colspan="3" style="text-align:right;font-weight:700;">Subtotal Obat</td>
                        <td style="font-weight:700;">Rp <?php echo number_format($total_obat, 0, ',', '.'); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php } else { ?>
            <div class="rm-none">Tidak ada data obat / resep.</div>
        <?php } ?>
    </div>
</div>
