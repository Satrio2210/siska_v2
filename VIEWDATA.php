<?php
include "conf/config.php";

function sk_value($db, $query, $field, $fallback = 0)
{
    $result = $db->query($query);
    if (!$result) {
        return $fallback;
    }

    $row = $result->fetch(PDO::FETCH_ASSOC);
    return isset($row[$field]) ? $row[$field] : $fallback;
}

function sk_rows($db, $query)
{
    $result = $db->query($query);
    if (!$result) {
        return array();
    }

    return $result->fetchAll(PDO::FETCH_ASSOC);
}

function sk_number($value)
{
    return number_format((float) $value, 0, '', '.');
}

function sk_money($value)
{
    return 'Rp ' . sk_number($value);
}

function sk_percent($value, $total)
{
    if ($total <= 0) {
        return 0;
    }

    return round(($value / $total) * 100);
}

function sk_month_name($date)
{
    $months = array(
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'Mei',
        'Jun',
        'Jul',
        'Agu',
        'Sep',
        'Okt',
        'Nov',
        'Des'
    );

    return $months[(int) date('n', strtotime($date)) - 1];
}

$latest_date = sk_value(
    $db,
    "SELECT MAX(TRXA_REGI_DATE) AS LAST_DATE FROM trxaregi WHERE TRXA_VIEW_STAT = 'Y'",
    'LAST_DATE',
    $datenow
);

if (empty($latest_date)) {
    $latest_date = $datenow;
}

$latest_time = strtotime($latest_date);
$current_time = strtotime($datenow);
$month_start = date('Y-m-01', $current_time);
$month_end = date('Y-m-t', $current_time);
$month_label = sk_month_name($datenow) . ' ' . date('Y', $current_time);

$range_start = date('Y-m-01', strtotime('-5 month', $current_time));
$range_end = $month_end;
$latest_date_q = $db->quote($latest_date);
$month_start_q = $db->quote($month_start);
$month_end_q = $db->quote($month_end);
$range_start_q = $db->quote($range_start);
$range_end_q = $db->quote($range_end);

$regi_batch = $db->query("
    SELECT
        SUM(CASE WHEN TRXA_REGI_DATE = $latest_date_q THEN 1 ELSE 0 END) AS TOTAL_ANTRIAN,
        SUM(CASE WHEN TRXA_REGI_DATE = $latest_date_q AND TRXA_REGI_PAYM = 'U' THEN 1 ELSE 0 END) AS PASIEN_UMUM,
        SUM(CASE WHEN TRXA_REGI_DATE = $latest_date_q AND TRXA_REGI_PAYM = 'B' THEN 1 ELSE 0 END) AS PASIEN_BPJS,
        SUM(CASE WHEN TRXA_REGI_DATE BETWEEN $month_start_q AND $month_end_q AND TRXA_REGI_POLI = 'PU' THEN 1 ELSE 0 END) AS POLI_UMUM,
        SUM(CASE WHEN TRXA_REGI_DATE BETWEEN $month_start_q AND $month_end_q AND TRXA_REGI_POLI = 'KB' THEN 1 ELSE 0 END) AS POLI_KIA,
        SUM(CASE WHEN TRXA_REGI_DATE BETWEEN $month_start_q AND $month_end_q AND TRXA_REGI_POLI = 'LB' THEN 1 ELSE 0 END) AS POLI_LAB,
        SUM(CASE WHEN TRXA_REGI_DATE BETWEEN $month_start_q AND $month_end_q AND TRXA_REGI_POLI = 'PG' THEN 1 ELSE 0 END) AS POLI_GIGI
    FROM trxaregi
    WHERE TRXA_VIEW_STAT = 'Y'
      AND TRXA_REGI_DATE BETWEEN $range_start_q AND $range_end_q
")->fetch(PDO::FETCH_ASSOC);

$total_antrian     = (int) ($regi_batch['TOTAL_ANTRIAN'] ?? 0);
$pasien_umum_hari  = (int) ($regi_batch['PASIEN_UMUM'] ?? 0);
$pasien_bpjs_hari  = (int) ($regi_batch['PASIEN_BPJS'] ?? 0);
$poli_umum         = (int) ($regi_batch['POLI_UMUM'] ?? 0);
$poli_kia          = (int) ($regi_batch['POLI_KIA'] ?? 0);
$poli_lab          = (int) ($regi_batch['POLI_LAB'] ?? 0);
$poli_gigi         = (int) ($regi_batch['POLI_GIGI'] ?? 0);
$total_poli        = $poli_umum + $poli_kia + $poli_lab + $poli_gigi;
$total_pasien_bulan = $total_poli;

$pendapatan_hari_rajal = sk_value(
    $db,
    "SELECT SUM(TRXA_PAYM_AMNT) AS TOTAL FROM trxasale
     WHERE TRXA_VIEW_STAT = 'Y' AND TRXA_ENTR_DATE = $latest_date_q",
    'TOTAL'
);

$pendapatan_hari_obat = sk_value(
    $db,
    "SELECT SUM(TRXA_PAYM_OUTS) AS TOTAL FROM trxadrug
     WHERE TRXA_VIEW_STAT = 'Y' AND TRXA_DRUG_STAT = 'P' AND TRXA_ENTR_DATE = $latest_date_q",
    'TOTAL'
);

$pendapatan_hari = (float) $pendapatan_hari_rajal + (float) $pendapatan_hari_obat;

$pendapatan_bulan_rajal = sk_value(
    $db,
    "SELECT SUM(TRXA_PAYM_AMNT) AS TOTAL FROM trxasale
     WHERE TRXA_VIEW_STAT = 'Y' AND TRXA_ENTR_DATE BETWEEN $month_start_q AND $month_end_q",
    'TOTAL'
);

$pendapatan_bulan_obat = sk_value(
    $db,
    "SELECT SUM(TRXA_PAYM_OUTS) AS TOTAL FROM trxadrug
     WHERE TRXA_VIEW_STAT = 'Y' AND TRXA_DRUG_STAT = 'P'
     AND TRXA_ENTR_DATE BETWEEN $month_start_q AND $month_end_q",
    'TOTAL'
);

$pendapatan_bulan = (float) $pendapatan_bulan_rajal + (float) $pendapatan_bulan_obat;

$kunjungan_rows = $db->query("
    SELECT DATE_FORMAT(TRXA_REGI_DATE, '%Y-%m') AS BULAN,
            SUM(CASE WHEN TRXA_REGI_PAYM = 'U' THEN 1 ELSE 0 END) AS TOTAL_UMUM,
            SUM(CASE WHEN TRXA_REGI_PAYM = 'B' THEN 1 ELSE 0 END) AS TOTAL_BPJS
     FROM trxaregi
     WHERE TRXA_VIEW_STAT = 'Y'
       AND TRXA_REGI_DATE BETWEEN $range_start_q AND $range_end_q
     GROUP BY DATE_FORMAT(TRXA_REGI_DATE, '%Y-%m')
     ORDER BY BULAN
")->fetchAll(PDO::FETCH_ASSOC);

$kunjungan_map_umum = array();
$kunjungan_map_bpjs = array();
$kunjungan_map_total = array();

$monthly_keys = array();
for ($i = 5; $i >= 0; $i--) {
    $date = date('Y-m-01', strtotime("-$i month", strtotime($month_start)));
    $monthly_keys[] = date('Y-m', strtotime($date));
}

foreach ($kunjungan_rows as $row) {
    $bulan = $row['BULAN'];
    $kunjungan_map_umum[$bulan] = (int) $row['TOTAL_UMUM'];
    $kunjungan_map_bpjs[$bulan] = (int) $row['TOTAL_BPJS'];
    $kunjungan_map_total[$bulan] = $kunjungan_map_umum[$bulan] + $kunjungan_map_bpjs[$bulan];
}

$monthly_labels = array();
$monthly_visits_umum = array();
$monthly_visits_bpjs = array();

$monthly_visits = array();
$max_visit = 1;

foreach ($monthly_keys as $key) {
    $dateAny = $key . '-01';
    $umum = isset($kunjungan_map_umum[$key]) ? $kunjungan_map_umum[$key] : 0;
    $bpjs = isset($kunjungan_map_bpjs[$key]) ? $kunjungan_map_bpjs[$key] : 0;
    $total = isset($kunjungan_map_total[$key]) ? $kunjungan_map_total[$key] : ($umum + $bpjs);

    $monthly_labels[] = sk_month_name($dateAny);
    $monthly_visits_umum[] = $umum;
    $monthly_visits_bpjs[] = $bpjs;

    $max_visit = max($max_visit, $total);
    $monthly_visits[] = array(
        'label' => sk_month_name($dateAny),
        'total' => $total,
    );
}

$pie_umum = $total_poli > 0 ? sk_percent($poli_umum, $total_poli) : 0;
$pie_kia = $total_poli > 0 ? sk_percent($poli_kia, $total_poli) : 0;
$pie_lab = $total_poli > 0 ? sk_percent($poli_lab, $total_poli) : 0;
$pie_gigi = $total_poli > 0 ? max(0, 100 - $pie_umum - $pie_kia - $pie_lab) : 0;
$pie_background = $total_poli > 0
    ? "radial-gradient(circle, #ffffff 0 38%, transparent 39%), conic-gradient(#4f97ca 0 $pie_umum%, #4fb48f $pie_umum% " . ($pie_umum + $pie_kia) . "%, #b8ad46 " . ($pie_umum + $pie_kia) . "% " . ($pie_umum + $pie_kia + $pie_lab) . "%, #f19a4e " . ($pie_umum + $pie_kia + $pie_lab) . "% 100%)"
    : "radial-gradient(circle, #ffffff 0 38%, transparent 39%), conic-gradient(#e5eaf0 0 100%)";

$top_obat = $db->query("
    SELECT COALESCE(i.INVE_PART_NAME, p.TRXA_STOCK_CODE) AS NAME,
           SUM(p.TRXA_STOCK_QUTY) AS TOTAL
    FROM trxaprsc p
    LEFT JOIN invemast i ON i.INVE_MAST_CODE = p.TRXA_STOCK_CODE
    WHERE p.TRXA_VIEW_STAT = 'Y'
      AND p.TRXA_ENTR_DATE BETWEEN $month_start_q AND $month_end_q
    GROUP BY COALESCE(i.INVE_PART_NAME, p.TRXA_STOCK_CODE)
    ORDER BY TOTAL DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$top_diagnosis = $db->query("
    SELECT TRXA_DIAG_NAME AS NAME, COUNT(*) AS TOTAL
    FROM trxadiag
    WHERE TRXA_VIEW_STAT = 'Y'
      AND TRXA_ENTR_DATE BETWEEN $month_start_q AND $month_end_q
    GROUP BY TRXA_DIAG_NAME
    ORDER BY TOTAL DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

if (count($top_obat) === 0) {
    $top_obat = array(
        array('NAME' => 'Paracetamol', 'TOTAL' => 0),
        array('NAME' => 'Amoxicillin', 'TOTAL' => 0),
        array('NAME' => 'Vitamin', 'TOTAL' => 0),
        array('NAME' => 'Antasida', 'TOTAL' => 0),
        array('NAME' => 'Cetirizine', 'TOTAL' => 0),
    );
}

if (count($top_diagnosis) === 0) {
    $top_diagnosis = array(
        array('NAME' => 'Poli Umum', 'TOTAL' => $poli_umum),
        array('NAME' => 'Poli KIA', 'TOTAL' => $poli_kia),
        array('NAME' => 'Laboratorium', 'TOTAL' => $poli_lab),
        array('NAME' => 'Poli Gigi', 'TOTAL' => $poli_gigi),
        array('NAME' => 'Lainnya', 'TOTAL' => 0),
    );
}

$total_paym_hari = $pasien_umum_hari + $pasien_bpjs_hari;
$persen_umum_hari = sk_percent($pasien_umum_hari, max($total_paym_hari, 1));
$persen_bpjs_hari = max(0, 100 - $persen_umum_hari);
?>

<style type="text/css">
    #tblviewdata {
        background: #ffffff;
        border-radius: 10px;
    }

    .simdash {
        color: #111827;
        font-family: Arial, Helvetica, sans-serif;
        padding: 24px;
    }

    .simdash * {
        box-sizing: border-box;
    }

    .simdash-header {
        align-items: center;
        display: flex;
        justify-content: space-between;
        margin-bottom: 18px;
    }

    .simdash-title {
        font-size: 26px;
        font-weight: 800;
        line-height: 1.1;
        margin: 0;
        text-transform: uppercase;
    }

    .simdash-subtitle {
        color: #6b7280;
        font-size: 14px;
        margin: 6px 0 0;
    }

    .simdash-profile {
        align-items: center;
        display: flex;
        gap: 10px;
        font-size: 14px;
    }

    .simdash-avatar {
        align-items: center;
        background: #dbeafe;
        border-radius: 50%;
        color: #2563eb;
        display: flex;
        font-weight: 800;
        height: 34px;
        justify-content: center;
        width: 34px;
    }

    .simdash-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: repeat(12, minmax(0, 1fr));
    }

    .simdash-card {
        background: #ffffff;
        border: 1px solid #dde5ee;
        border-radius: 10px;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
        min-width: 0;
        padding: 16px;
    }

    .simdash-kpi {
        grid-column: span 3;
        min-height: 112px;
    }

    .simdash-chart {
        grid-column: span 6;
        min-height: 245px;
    }

    .simdash-bottom {
        grid-column: span 4;
        min-height: 245px;
    }

    .simdash-label,
    .simdash-card-title {
        color: #111827;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 0;
        line-height: 1.2;
        margin: 0;
        text-transform: uppercase;
    }

    .simdash-value {
        color: #327f8f;
        font-size: 30px;
        font-weight: 800;
        line-height: 1;
        margin-top: 14px;
    }

    .simdash-value-money {
        color: #8a8730;
        font-size: 28px;
    }

    .simdash-note {
        color: #6b7280;
        font-size: 13px;
        margin-top: 8px;
    }

    .simdash-chart-wrap {
        height: 168px;
        margin-top: 20px;
        position: relative;
    }

    .simdash-bars {
        align-items: end;
        border-bottom: 1px solid #d6dee8;
        border-left: 1px solid #d6dee8;
        display: grid;
        gap: 18px;
        grid-template-columns: repeat(6, 1fr);
        height: 140px;
        padding: 0 14px;
        position: relative;
    }

    .simdash-bars:before {
        background: linear-gradient(to bottom, #e6edf5 1px, transparent 1px);
        background-size: 100% 35px;
        content: "";
        inset: 0;
        pointer-events: none;
        position: absolute;
    }

    .simdash-bar-item {
        align-items: end;
        display: flex;
        height: 100%;
        justify-content: center;
        position: relative;
        z-index: 1;
    }

    .simdash-bar {
        background: #4f97ca;
        border-radius: 5px 5px 0 0;
        min-height: 5px;
        width: 34px;
    }

    .simdash-bar-labels {
        display: grid;
        gap: 18px;
        grid-template-columns: repeat(6, 1fr);
        padding: 8px 14px 0;
    }

    .simdash-bar-labels span {
        color: #374151;
        font-size: 12px;
        text-align: center;
    }

    .simdash-pie-area {
        align-items: center;
        display: grid;
        gap: 22px;
        grid-template-columns: 170px 1fr;
        margin-top: 18px;
    }

    .simdash-pie {
        border-radius: 50%;
        height: 155px;
        width: 155px;
    }

    .simdash-legend {
        display: grid;
        gap: 10px;
    }

    .simdash-legend-row {
        align-items: center;
        color: #374151;
        display: flex;
        font-size: 13px;
        gap: 8px;
        justify-content: space-between;
    }

    .simdash-legend-name {
        align-items: center;
        display: flex;
        gap: 8px;
    }

    .simdash-dot {
        border-radius: 4px;
        display: inline-block;
        height: 12px;
        width: 12px;
    }

    .simdash-blue {
        background: #4f97ca;
    }

    .simdash-green {
        background: #4fb48f;
    }

    .simdash-olive {
        background: #b8ad46;
    }

    .simdash-orange {
        background: #f19a4e;
    }

    .simdash-table {
        border-collapse: collapse;
        margin-top: 14px;
        width: 100%;
    }

    .simdash-table th {
        background: #f2f5f8;
        border: 1px solid #dbe3ec;
        color: #111827;
        font-size: 12px;
        padding: 8px;
        text-align: left;
    }

    .simdash-table td {
        border: 1px solid #e1e7ee;
        color: #111827;
        font-size: 12px;
        padding: 8px;
    }

    .simdash-table th:last-child,
    .simdash-table td:last-child {
        text-align: right;
        white-space: nowrap;
    }

    .simdash-percent {
        margin-top: 26px;
    }

    .simdash-percent-main {
        background: #e5eaf0;
        border-radius: 999px;
        display: flex;
        height: 18px;
        margin-bottom: 20px;
        overflow: hidden;
    }

    .simdash-percent-umum {
        background: #4f97ca;
    }

    .simdash-percent-bpjs {
        background: #4fb48f;
    }

    .simdash-percent-row {
        align-items: center;
        display: flex;
        justify-content: space-between;
        margin-top: 12px;
    }

    .simdash-percent-name {
        align-items: center;
        color: #374151;
        display: flex;
        font-size: 14px;
        gap: 8px;
    }

    .simdash-percent-value {
        color: #111827;
        font-size: 20px;
        font-weight: 800;
    }

    @media (max-width: 1100px) {

        .simdash-kpi,
        .simdash-chart,
        .simdash-bottom {
            grid-column: span 6;
        }
    }

    @media (max-width: 760px) {
        .simdash {
            padding: 16px;
        }

        .simdash-header,
        .simdash-pie-area {
            align-items: flex-start;
            grid-template-columns: 1fr;
            flex-direction: column;
        }

        .simdash-kpi,
        .simdash-chart,
        .simdash-bottom {
            grid-column: span 12;
        }
    }
</style>

<div class="simdash">
    <div class="simdash-header">
        <div>
            <h2 class="simdash-title">Dashboard</h2>
            <p class="simdash-subtitle">Ringkasan data operasional klinik hari ini</p>
        </div>
        <!-- <div class="simdash-profile">
            <span>Profil Admin</span>
            <span class="simdash-avatar">A</span>
        </div> -->
    </div>

    <div class="simdash-grid">
        <div class="simdash-card simdash-kpi">
            <p class="simdash-label">Kunjungan Pasien Hari Ini</p>
            <div class="simdash-value"><?php echo sk_number($total_antrian); ?></div>
            <div class="simdash-note"><?php echo date('d-m-Y', $latest_time); ?></div>
        </div>

        <div class="simdash-card simdash-kpi">
            <p class="simdash-label">Total Kunjungan Pasien Bulan Ini</p>
            <div class="simdash-value"><?php echo sk_number($total_pasien_bulan); ?></div>
            <div class="simdash-note">Umum: <?php echo sk_number($poli_umum); ?> | Gigi:
                <?php echo sk_number($poli_gigi); ?> | KIA: <?php echo sk_number($poli_kia); ?> | Lab:
                <?php echo sk_number($poli_lab); ?></div>
        </div>

        <div class="simdash-card simdash-kpi">
            <p class="simdash-label">Pendapatan Hari Ini</p>
            <!-- <div class="simdash-value simdash-value-money"><?php echo sk_money($pendapatan_hari); ?></div> -->
            <div class="simdash-value simdash-value-money">-</div>
            <div class="simdash-note">Transaksi harian</div>
        </div>

        <div class="simdash-card simdash-kpi">
            <p class="simdash-label">Total Pendapatan Bulan Ini</p>
            <!-- <div class="simdash-value simdash-value-money"><?php echo sk_money($pendapatan_bulan); ?></div> -->
            <div class="simdash-value simdash-value-money">-</div>
            <div class="simdash-note"><?php echo $month_label; ?></div>
        </div>

        <div class="simdash-card simdash-chart">
            <p class="simdash-card-title" style="margin-bottom: 30px;">Total Kunjungan Pasien</p>
            <div class="simdash-chart-wrap" style="height: 200px;">
                <canvas id="lineChartKunjungan"></canvas>
            </div>
        </div>

        <div class="simdash-card simdash-chart">
            <p class="simdash-card-title">Kunjungan Poliklinik (<?php echo date("F Y", $latest_time); ?>)</p>
            <div class="simdash-pie-area">
                <div class="simdash-pie"
                    style="background: radial-gradient(circle, #ffffff 0 38%, transparent 39%), conic-gradient(#4f97ca 0 <?php echo $pie_umum; ?>%, #4fb48f <?php echo $pie_umum; ?>% <?php echo $pie_umum + $pie_kia; ?>%, #b8ad46 <?php echo $pie_umum + $pie_kia; ?>% <?php echo $pie_umum + $pie_kia + $pie_lab; ?>%, #f19a4e <?php echo $pie_umum + $pie_kia + $pie_lab; ?>% 100%);">
                </div>
                <div class="simdash-legend">
                    <div class="simdash-legend-row">
                        <span class="simdash-legend-name"><span class="simdash-dot simdash-blue"></span>Poli Umum</span>
                        <strong><?php echo $pie_umum; ?>%</strong>
                    </div>
                    <div class="simdash-legend-row">
                        <span class="simdash-legend-name"><span class="simdash-dot simdash-green"></span>Poli KIA</span>
                        <strong><?php echo $pie_kia; ?>%</strong>
                    </div>
                    <div class="simdash-legend-row">
                        <span class="simdash-legend-name"><span
                                class="simdash-dot simdash-olive"></span>Laboratorium</span>
                        <strong><?php echo $pie_lab; ?>%</strong>
                    </div>
                    <div class="simdash-legend-row">
                        <span class="simdash-legend-name"><span class="simdash-dot simdash-orange"></span>Poli
                            Gigi</span>
                        <strong><?php echo $pie_gigi; ?>%</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="simdash-card simdash-bottom">
            <p class="simdash-card-title">Pemakaian Obat (<?php echo date("F Y", $latest_time); ?>)</p>
            <div class="simdash-note">Top 5 pemakaian obat tertinggi</div>
            <table class="simdash-table">
                <thead>
                    <tr>
                        <th>Nama Obat</th>
                        <th>Qty</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_obat as $obat) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($obat['NAME']); ?></td>
                            <td><?php echo sk_number($obat['TOTAL']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="simdash-card simdash-bottom">
            <p class="simdash-card-title">Top 5 Diagnosis (<?php echo date("F Y", $latest_time); ?>)</p>
            <div class="simdash-note">Top 5 diagnosis tertinggi</div>
            <table class="simdash-table">
                <thead>
                    <tr>
                        <th>Diagnosis</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($top_diagnosis as $diagnosis) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($diagnosis['NAME']); ?></td>
                            <td><?php echo sk_number($diagnosis['TOTAL']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="simdash-card simdash-bottom">
            <p class="simdash-card-title">Persentase Kunjungan Harian</p>
            <div class="simdash-note">Pasien umum dan BPJS hari ini</div>

            <div class="simdash-percent">
                <div class="simdash-percent-main">
                    <div class="simdash-percent-umum" style="width: <?php echo $persen_umum_hari; ?>%;"></div>
                    <div class="simdash-percent-bpjs" style="width: <?php echo $persen_bpjs_hari; ?>%;"></div>
                </div>

                <div class="simdash-percent-row">
                    <span class="simdash-percent-name"><span class="simdash-dot simdash-green"></span>Umum</span>
                    <span class="simdash-percent-value"><?php echo $persen_umum_hari; ?>%</span>
                </div>
                <div class="simdash-percent-row">
                    <span class="simdash-percent-name"><span class="simdash-dot simdash-blue"></span>BPJS</span>
                    <span class="simdash-percent-value"><?php echo $persen_bpjs_hari; ?>%</span>
                </div>
                <div class="simdash-note">
                    <?php echo sk_number($pasien_umum_hari); ?> pasien umum /
                    <?php echo sk_number($pasien_bpjs_hari); ?> pasien BPJS
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function renderLineChartDashboard() {
        const canvasElement = document.getElementById('lineChartKunjungan');
        if (!canvasElement) return;

        const labelBulan = <?php echo json_encode($monthly_labels); ?>;
        const dataUmum = <?php echo json_encode($monthly_visits_umum); ?>;
        const dataBpjs = <?php echo json_encode($monthly_visits_bpjs); ?>;

        const ctx = canvasElement.getContext('2d');

        let gradientBpjs = ctx.createLinearGradient(0, 0, 0, 400);
        gradientBpjs.addColorStop(0, 'rgba(79, 151, 202, 0.4)');
        gradientBpjs.addColorStop(1, 'rgba(79, 151, 202, 0)');

        let gradientUmum = ctx.createLinearGradient(0, 0, 0, 400);
        gradientUmum.addColorStop(0, 'rgba(79, 180, 143, 0.4)');
        gradientUmum.addColorStop(1, 'rgba(79, 180, 143, 0)');

        if (window.myLineChart) {
            window.myLineChart.destroy();
        }

        window.myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labelBulan,
                datasets: [
                    {
                        label: 'Pasien BPJS',
                        data: dataBpjs,
                        borderColor: '#4f97ca',
                        backgroundColor: gradientBpjs,
                        borderWidth: 2,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4f97ca',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Pasien Umum',
                        data: dataUmum,
                        borderColor: '#4fb48f',
                        backgroundColor: gradientUmum,
                        borderWidth: 2,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4fb48f',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#111827',
                        padding: 10,
                        cornerRadius: 8,
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#e5eaf0',
                            drawBorder: false,
                        },
                        ticks: {
                            color: '#6b7280',
                            font: { size: 11 }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false,
                        },
                        ticks: {
                            color: '#6b7280',
                            font: { size: 11 }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            }
        });
    }
</script>
