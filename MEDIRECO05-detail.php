<!doctype html>
<?php
include "conf/config.php";
include "inc/sanie.php";
session_start();

if (!isset($_SESSION['username'])) {
  header("Location: signin.php");
  exit;
}

$user = $_SESSION['username'];
$paticode = isset($_GET['id']) ? trim($_GET['id']) : '';
$visit_id = isset($_GET['visit_id']) ? trim($_GET['visit_id']) : '';

if ($paticode === '') {
  header("Location: MEDIRECO05.php");
  exit;
}

// --- Data pasien ---
$stP = $db->prepare("
  SELECT PATI_MAST_CODE, PATI_MAIN_PIDN, PATI_MAIN_NAME, PATI_MAIN_GEND,
         PATI_MAIN_BIRT, PATI_MAIN_BLOD, PATI_MAIN_ADDR, PATI_MAIN_PHNE,
         PATI_MAIN_WARD, PATI_MAIN_DIST, PATI_MAIN_CITY
  FROM patimast
  WHERE PATI_MAST_CODE = :code AND PATI_VIEW_STAT = 'Y'
  LIMIT 1
");
$stP->execute([':code' => $paticode]);
$pasien = $stP->fetch(PDO::FETCH_ASSOC);

if (!$pasien) {
  header("Location: MEDIRECO05.php");
  exit;
}

$mainname = $pasien['PATI_MAIN_NAME'];
$maingend = $pasien['PATI_MAIN_GEND'];
$mainbirt = $pasien['PATI_MAIN_BIRT'];
$mainblod = $pasien['PATI_MAIN_BLOD'];
$mainaddr = $pasien['PATI_MAIN_ADDR'];
$mainphne = $pasien['PATI_MAIN_PHNE'];

$mainage = '-';
if (!empty($mainbirt) && $mainbirt !== '0000-00-00') {
  try {
    $tanggal = new DateTime($mainbirt);
    $today = new DateTime('today');
    $d = $today->diff($tanggal);
    $mainage = $d->y . ' tahun ' . $d->m . ' bulan ' . $d->d . ' hari';
  } catch (Exception $e) {
    $mainage = '-';
  }
}

$genderLabel = ($maingend === 'M') ? 'Laki-laki' : (($maingend === 'F') ? 'Perempuan' : '-');
$genderIcon = ($maingend === 'M') ? 'bi-gender-male' : (($maingend === 'F') ? 'bi-gender-female' : 'bi-person');
$bloodLabel = (!empty($mainblod) && $mainblod !== 'X') ? $mainblod : 'N/A';

$nama_hari_id = [
  1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis',
  5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'
];

// --- Riwayat kunjungan ---
$stV = $db->prepare("
  SELECT
    r.TRXA_REGI_CODE AS REGI_CODE,
    r.TRXA_REGI_DATE,
    r.TRXA_REGI_DOCT,
    r.TRXA_REGI_POLI,
    (SELECT TBLA_POLI_NAME FROM tblapoli WHERE TBLA_POLI_CODE = r.TRXA_REGI_POLI) AS POLI_NAME,
    p.PASS_USER_NAME AS DOCT_NAME,
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
    d.DIAGNOSA
  FROM trxaregi r
  LEFT JOIN trxaexam e ON e.TRXA_EXAM_CODE = r.TRXA_REGI_CODE
  LEFT JOIN passiden p ON p.PASS_USER_IDEN = r.TRXA_REGI_DOCT
  LEFT JOIN (
    SELECT TRXA_EXAM_CODE,
           GROUP_CONCAT(CONCAT(TRXA_DIAG_CODE, ' - ', TRXA_DIAG_NAME) SEPARATOR ';<br>') AS DIAGNOSA
    FROM trxadiag
    GROUP BY TRXA_EXAM_CODE
  ) d ON d.TRXA_EXAM_CODE = r.TRXA_REGI_CODE
  WHERE r.TRXA_PATI_CODE = :code
    AND r.TRXA_REGI_STAT IN ('C','X')
    AND r.TRXA_REGI_POLI <> :lab
  ORDER BY r.TRXA_REGI_DATE DESC, r.TRXA_REGI_CODE DESC
");
$stV->execute([':code' => $paticode, ':lab' => $code_lab_room]);
$visits = $stV->fetchAll(PDO::FETCH_ASSOC);

// pilih visit aktif
$activeVisit = null;
if (count($visits) > 0) {
  if ($visit_id !== '') {
    foreach ($visits as $v) {
      if ($v['REGI_CODE'] === $visit_id) {
        $activeVisit = $v;
        break;
      }
    }
  }
  if (!$activeVisit) {
    $activeVisit = $visits[0];
    $visit_id = $activeVisit['REGI_CODE'];
  }
}

function rm05_val($v, $suffix = '')
{
  if ($v === null || $v === '') return '-';
  return htmlspecialchars($v, ENT_QUOTES, 'UTF-8') . $suffix;
}

function rm05_text($v)
{
  if ($v === null || $v === '') return '-';
  return nl2br(htmlspecialchars($v, ENT_QUOTES, 'UTF-8'));
}
?>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Sistem Informasi Klinik Pratama">
  <title>Detail Rekam Medis - <?php echo htmlspecialchars($mainname, ENT_QUOTES, 'UTF-8'); ?></title>
  <link rel="shortcut icon" href="assets/img/logo.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/bootstrap/bootstrap.css">
  <link rel="stylesheet" href="assets/css/layouts/header.css">
  <link rel="stylesheet" href="assets/css/layouts/side-menu.css">
  <link rel="stylesheet" href="assets/css/trxapati-shared.css">
  <style>
    :root {
      --rm05-tosca: #169C89;
      --rm05-tosca-dark: #0f7a6b;
    }
    .rm05-profile {
      display: flex;
      gap: 20px;
      align-items: flex-start;
      flex-wrap: wrap;
    }
    .rm05-avatar {
      width: 96px;
      height: 96px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid var(--rm05-tosca);
      background: #e5e7eb;
      flex-shrink: 0;
    }
    .rm05-profile-main {
      flex: 1;
      min-width: 220px;
    }
    .rm05-name {
      font-size: 22px;
      font-weight: 700;
      color: #0f172a;
      margin: 0 0 6px;
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }
    .rm05-name .bi-gender-male { color: #2563eb; }
    .rm05-name .bi-gender-female { color: #db2777; }
    .rm05-meta {
      color: #64748b;
      font-size: 14px;
      font-weight: 500;
      margin-bottom: 10px;
    }
    .rm05-info-row {
      display: flex;
      flex-wrap: wrap;
      gap: 16px 28px;
      font-size: 13px;
      color: #374151;
      font-weight: 500;
    }
    .rm05-info-row i {
      color: var(--rm05-tosca);
      margin-right: 6px;
    }
    .rm05-blood {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      background: #fee2e2;
      color: #b91c1c;
      font-weight: 700;
      font-size: 12px;
      padding: 4px 10px;
      border-radius: 999px;
    }
    .rm05-split {
      display: grid;
      grid-template-columns: 280px 1fr;
      gap: 16px;
      align-items: start;
    }
    @media (max-width: 992px) {
      .rm05-split { grid-template-columns: 1fr; }
    }
    .rm05-visit-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
      max-height: 620px;
      overflow-y: auto;
      padding-right: 4px;
    }
    .rm05-visit-card {
      display: block;
      text-decoration: none;
      border: 2px solid #d1d5db;
      border-radius: 14px;
      padding: 12px 14px;
      background: #f9fafb;
      color: #374151;
      transition: .15s;
    }
    .rm05-visit-card:hover {
      border-color: #9ca3af;
      color: #111827;
    }
    .rm05-visit-card.active {
      border-color: var(--rm05-tosca);
      background: #fff;
      box-shadow: 0 2px 8px rgba(22, 156, 137, 0.15);
    }
    .rm05-visit-card .v-date {
      font-weight: 700;
      font-size: 13px;
      margin-bottom: 4px;
    }
    .rm05-visit-card .v-poli {
      font-size: 12px;
      color: #64748b;
      font-weight: 500;
    }
    .rm05-visit-card .v-doc {
      font-size: 12px;
      color: #6b7280;
      margin-top: 4px;
    }
    .rm05-detail-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
      margin-bottom: 14px;
    }
    .rm05-visit-banner {
      background: var(--rm05-tosca);
      color: #fff;
      border-radius: 12px;
      padding: 12px 16px;
      font-weight: 700;
      font-size: 15px;
      margin-bottom: 8px;
    }
    .rm05-doct {
      font-size: 13px;
      color: #64748b;
      font-weight: 600;
      margin-bottom: 16px;
    }
    .rm05-blocks {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 14px;
    }
    @media (max-width: 1100px) {
      .rm05-blocks { grid-template-columns: 1fr; }
    }
    .rm05-block {
      background: #f9fafb;
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      padding: 14px;
      min-height: 180px;
    }
    .rm05-block h4 {
      font-size: 14px;
      font-weight: 700;
      color: var(--rm05-tosca);
      margin: 0 0 12px;
      padding-bottom: 8px;
      border-bottom: 2px solid #d1fae5;
    }
    .rm05-kv {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
    }
    .rm05-kv td {
      padding: 4px 0;
      vertical-align: top;
      color: #1f2937;
      font-weight: 500;
    }
    .rm05-kv td.lbl {
      width: 110px;
      font-weight: 700;
      color: #4b5563;
      white-space: nowrap;
    }
    .rm05-kv td.sep {
      width: 10px;
      color: #9ca3af;
    }
    .rm05-section-label {
      font-weight: 700;
      color: #374151;
      font-size: 13px;
      margin: 10px 0 4px;
    }
    .rm05-section-label:first-child { margin-top: 0; }
    .rm05-section-body {
      font-size: 13px;
      color: #1f2937;
      font-weight: 500;
      line-height: 1.45;
      margin-bottom: 6px;
    }
    .rm05-back {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin-bottom: 12px;
      color: var(--rm05-tosca);
      font-weight: 600;
      text-decoration: none;
      font-size: 14px;
    }
    .rm05-back:hover { color: var(--rm05-tosca-dark); }
    .rm05-empty-visit {
      text-align: center;
      padding: 40px 16px;
      color: #94a3b8;
      font-weight: 500;
    }
    @media print {
      #sidebar, .header, .footerdate, .footertime, .rm05-back, .rm05-no-print {
        display: none !important;
      }
      #content-wrapper { margin: 0 !important; width: 100% !important; }
      .rm05-split { grid-template-columns: 1fr; }
      .rm05-visit-list { display: none; }
      .card-modern { box-shadow: none; border: 1px solid #ccc; }
    }
  </style>
</head>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="js/jquery.js"></script>
<script src="js/sweetalert.min.js"></script>
<script>
  $(document).ready(function () {
    setInterval(timestamp, 1000);
  });
  function timestamp() {
    $.ajax({ url: 'inc/timestamp.php', success: function (data) { $('#timestamp').html(data); } });
  }
</script>

<body onLoad="periksaakses('PASS_MEDI_REPO');">
  <div id="wrapper">
    <?php include "inc/side-menu.php"; ?>
    <div id="content-wrapper">
      <?php include "inc/header.php"; ?>
      <div class="content">
        <div class="content-modern">

          <a href="MEDIRECO05.php" class="rm05-back rm05-no-print">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pasien
          </a>

          <!-- Profil Pasien -->
          <div class="card-modern">
            <div class="rm05-profile">
              <img class="rm05-avatar"
                   src="https://placehold.co/96x96/e5e7eb/64748b?text=<?php echo urlencode(mb_substr($mainname, 0, 1)); ?>"
                   alt="Foto Pasien"
                   onerror="this.src='assets/img/logo.png'">
              <div class="rm05-profile-main">
                <h2 class="rm05-name">
                  <?php echo htmlspecialchars($mainname, ENT_QUOTES, 'UTF-8'); ?>
                  <i class="bi <?php echo $genderIcon; ?>" title="<?php echo $genderLabel; ?>"></i>
                </h2>
                <div class="rm05-meta">
                  No. RM: <strong><?php echo htmlspecialchars($paticode, ENT_QUOTES, 'UTF-8'); ?></strong>
                  &nbsp;·&nbsp; <?php echo htmlspecialchars($mainage, ENT_QUOTES, 'UTF-8'); ?>
                  &nbsp;·&nbsp; <?php echo $genderLabel; ?>
                </div>
                <div class="rm05-info-row">
                  <span><i class="bi bi-geo-alt-fill"></i><?php echo htmlspecialchars($mainaddr ?: '-', ENT_QUOTES, 'UTF-8'); ?></span>
                  <span><i class="bi bi-phone-fill"></i><?php echo htmlspecialchars($mainphne ?: '-', ENT_QUOTES, 'UTF-8'); ?></span>
                  <span class="rm05-blood"><i class="bi bi-droplet-fill"></i> Blood Type: <?php echo htmlspecialchars($bloodLabel, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
              </div>
            </div>
          </div>

          <!-- Split: Riwayat | Detail -->
          <div class="rm05-split">
            <div class="card-modern">
              <div class="card-title"><i class="bi bi-clock-history"></i> RIWAYAT MEDIS</div>
              <div class="rm05-visit-list">
                <?php if (count($visits) === 0) { ?>
                  <div class="rm05-empty-visit">Belum ada riwayat kunjungan.</div>
                <?php } else {
                  foreach ($visits as $v) {
                    $vid = $v['REGI_CODE'];
                    $isActive = ($vid === $visit_id);
                    $ts = strtotime($v['TRXA_REGI_DATE']);
                    $hari = $nama_hari_id[(int) date('N', $ts)];
                    $tgl = $hari . ', ' . date('d-m-Y', $ts);
                    $poli = $v['POLI_NAME'] ?: ($v['TRXA_REGI_POLI'] ?: '-');
                    $doc = $v['DOCT_NAME'] ?: '-';
                    $href = 'MEDIRECO05-detail.php?id=' . urlencode($paticode) . '&visit_id=' . urlencode($vid);
                ?>
                  <a href="<?php echo htmlspecialchars($href, ENT_QUOTES, 'UTF-8'); ?>"
                     class="rm05-visit-card<?php echo $isActive ? ' active' : ''; ?>">
                    <div class="v-date"><?php echo htmlspecialchars($tgl, ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="v-poli"><i class="bi bi-hospital"></i> <?php echo htmlspecialchars($poli, ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="v-doc"><i class="bi bi-person-badge"></i> <?php echo htmlspecialchars($doc, ENT_QUOTES, 'UTF-8'); ?></div>
                  </a>
                <?php }
                } ?>
              </div>
            </div>

            <div class="card-modern" id="rm05VisitDetail">
              <?php if (!$activeVisit) { ?>
                <div class="rm05-empty-visit">Pilih kunjungan untuk melihat detail rekam medis.</div>
              <?php } else {
                $av = $activeVisit;
                $ts = strtotime($av['TRXA_REGI_DATE']);
                $hari = $nama_hari_id[(int) date('N', $ts)];
                $tglBanner = $hari . ', ' . date('d-m-Y', $ts);
                $poliBanner = $av['POLI_NAME'] ?: ($av['TRXA_REGI_POLI'] ?: '-');
                $doctBanner = $av['DOCT_NAME'] ?: '-';

                $outtinggi = rm05_val($av['TINGGI'], ' cm');
                $outberat  = rm05_val($av['BERAT'], ' kg');
                $outlp     = rm05_val($av['LP'], ' cm');
                $outimt    = rm05_val($av['IMT'], ' kg.m2');
                $outdarah  = rm05_val($av['DARAH'], ' mmHg');
                $outsuhu   = !empty($av['SUHU']) ? htmlspecialchars($av['SUHU'], ENT_QUOTES, 'UTF-8') . ' °C' : '-';
                $outrr     = rm05_val($av['RR'], ' /minute');
                $outhr     = rm05_val($av['HR'], ' bpm');

                $outkeluhan  = rm05_text($av['KELUHAN']);
                $outanamnesa = rm05_text($av['ANAMNESA']);
                $outbody     = rm05_text($av['BODY']);
                $outdiagnosa = !empty($av['DIAGNOSA']) ? $av['DIAGNOSA'] : '-';
                $outcatatan  = rm05_text($av['CATATAN']);
                $outresep    = rm05_text($av['RESEP']);
              ?>
                <div class="rm05-detail-head">
                  <div class="card-title" style="margin-bottom:0;">Detail Kunjungan</div>
                  <button type="button" class="btn-modern btn-print rm05-no-print" onclick="window.print();">
                    <i class="bi bi-printer"></i> Cetak Resume
                  </button>
                </div>

                <div class="rm05-visit-banner">
                  <?php echo htmlspecialchars($tglBanner, ENT_QUOTES, 'UTF-8'); ?>
                  &nbsp;·&nbsp;
                  <?php echo htmlspecialchars($poliBanner, ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="rm05-doct">
                  <i class="bi bi-stethoscope"></i>
                  Dokter Pemeriksa: <strong><?php echo htmlspecialchars($doctBanner, ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>

                <div class="rm05-blocks">
                  <!-- TTV -->
                  <div class="rm05-block">
                    <h4><i class="bi bi-heart-pulse"></i> Tanda-tanda Vital</h4>
                    <table class="rm05-kv">
                      <tr><td class="lbl">Tinggi</td><td class="sep">:</td><td><?php echo $outtinggi; ?></td></tr>
                      <tr><td class="lbl">BB</td><td class="sep">:</td><td><?php echo $outberat; ?></td></tr>
                      <tr><td class="lbl">LP</td><td class="sep">:</td><td><?php echo $outlp; ?></td></tr>
                      <tr><td class="lbl">IMT</td><td class="sep">:</td><td><?php echo $outimt; ?></td></tr>
                      <tr><td class="lbl">TD</td><td class="sep">:</td><td><?php echo $outdarah; ?></td></tr>
                      <tr><td class="lbl">Suhu</td><td class="sep">:</td><td><?php echo $outsuhu; ?></td></tr>
                      <tr><td class="lbl">RR</td><td class="sep">:</td><td><?php echo $outrr; ?></td></tr>
                      <tr><td class="lbl">HR</td><td class="sep">:</td><td><?php echo $outhr; ?></td></tr>
                    </table>
                  </div>

                  <!-- Pemeriksaan & Diagnosa -->
                  <div class="rm05-block">
                    <h4><i class="bi bi-clipboard2-check"></i> Pemeriksaan &amp; Diagnosa</h4>
                    <div class="rm05-section-label">Keluhan</div>
                    <div class="rm05-section-body"><?php echo $outkeluhan; ?></div>
                    <div class="rm05-section-label">Anamnesa</div>
                    <div class="rm05-section-body"><?php echo $outanamnesa; ?></div>
                    <div class="rm05-section-label">Pemeriksaan Fisik</div>
                    <div class="rm05-section-body"><?php echo $outbody; ?></div>
                    <div class="rm05-section-label">Diagnosa (ICD)</div>
                    <div class="rm05-section-body"><?php echo $outdiagnosa; ?></div>
                  </div>

                  <!-- Tindakan & Obat -->
                  <div class="rm05-block">
                    <h4><i class="bi bi-capsule"></i> Tindakan &amp; Obat</h4>
                    <?php if ($av['CATATAN'] !== null && $av['CATATAN'] !== '') { ?>
                      <div class="rm05-section-label">Catatan</div>
                      <div class="rm05-section-body"><?php echo $outcatatan; ?></div>
                    <?php } ?>
                    <div class="rm05-section-label">Non-Farmakologis</div>
                    <div class="rm05-section-body">-</div>
                    <div class="rm05-section-label">Farmakoterapi</div>
                    <div class="rm05-section-body"><?php echo $outresep; ?></div>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>

        </div>
      </div>
      <div class="footerdate"><?php echo isset($datenow) ? $datenow : date('Y-m-d'); ?></div>
      <div class="footertime"><span id="timestamp"></span></div>
    </div>
  </div>
  <script src="js/MEDIRECO05.js?v=<?php echo time(); ?>"></script>
  <script src="js/ui.js"></script>
</body>

</html>
