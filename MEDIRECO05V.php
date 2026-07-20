<?php
include "conf/config.php";
include "inc/sanie.php";

$kata = isset($_POST['q']) ? trim($_POST['q']) : (isset($_GET['q']) ? trim($_GET['q']) : '');
$page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : (isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1);
$perpage = 15;
$offset = ($page - 1) * $perpage;

$kata_like = '%' . $kata . '%';
$params = [];
$where = "p.PATI_VIEW_STAT = 'Y'";

if ($kata !== '') {
  $where .= " AND (p.PATI_MAIN_NAME LIKE :q1 OR p.PATI_MAST_CODE LIKE :q2 OR p.PATI_MAIN_PIDN LIKE :q3)";
  $params[':q1'] = $kata_like;
  $params[':q2'] = $kata_like;
  $params[':q3'] = $kata_like;
}

// count total
$sqlCount = "SELECT COUNT(*) FROM patimast p WHERE $where";
$stCount = $db->prepare($sqlCount);
$stCount->execute($params);
$total = (int) $stCount->fetchColumn();
$totalPages = max(1, (int) ceil($total / $perpage));
if ($page > $totalPages) {
  $page = $totalPages;
  $offset = ($page - 1) * $perpage;
}

if ($kata !== '') {
  // Search normally returns few patients. Use indexed per-patient lookup
  // instead of aggregating all historical visits before filtering patients.
  $sql = "
    SELECT
      p.PATI_MAST_CODE,
      p.PATI_MAIN_NAME,
      p.PATI_MAIN_GEND,
      p.PATI_MAIN_BIRT,
      (
        SELECT MAX(r.TRXA_REGI_DATE)
        FROM trxaregi r
        WHERE r.TRXA_PATI_CODE = p.PATI_MAST_CODE
          AND r.TRXA_VIEW_STAT = 'Y'
      ) AS LAST_VISIT
    FROM patimast p
    WHERE $where
    ORDER BY LAST_VISIT DESC, p.PATI_ENTR_DATE DESC, p.PATI_ENTR_TIME DESC
    LIMIT :limit OFFSET :offset
  ";
} else {
  $sql = "
    SELECT
      p.PATI_MAST_CODE,
      p.PATI_MAIN_NAME,
      p.PATI_MAIN_GEND,
      p.PATI_MAIN_BIRT,
      rv.LAST_VISIT
    FROM patimast p
    LEFT JOIN (
      SELECT TRXA_PATI_CODE, MAX(TRXA_REGI_DATE) AS LAST_VISIT
      FROM trxaregi
      WHERE TRXA_VIEW_STAT = 'Y'
      GROUP BY TRXA_PATI_CODE
    ) rv ON rv.TRXA_PATI_CODE = p.PATI_MAST_CODE
    WHERE $where
    ORDER BY LAST_VISIT DESC, p.PATI_ENTR_DATE DESC, p.PATI_ENTR_TIME DESC
    LIMIT :limit OFFSET :offset
  ";
}

$st = $db->prepare($sql);
foreach ($params as $k => $v) {
  $st->bindValue($k, $v);
}
$st->bindValue(':limit', $perpage, PDO::PARAM_INT);
$st->bindValue(':offset', $offset, PDO::PARAM_INT);
$st->execute();
$rows = $st->fetchAll(PDO::FETCH_ASSOC);

function rm05_usia($birt)
{
  if (empty($birt) || $birt === '0000-00-00') {
    return '-';
  }
  try {
    $tanggal = new DateTime($birt);
    $today = new DateTime('today');
    $diff = $today->diff($tanggal);
    return $diff->y . ' thn';
  } catch (Exception $e) {
    return '-';
  }
}

function rm05_gender($g)
{
  if ($g === 'M') return 'Laki-laki';
  if ($g === 'F') return 'Perempuan';
  return '-';
}

$qEnc = urlencode($kata);
?>
<table id="screen" class="modern-table">
  <thead>
    <tr>
      <th style="width:110px">No. RM</th>
      <th>Nama Pasien</th>
      <th style="width:120px">Jenis Kelamin</th>
      <th style="width:90px">Usia</th>
      <th style="width:150px">Kunjungan Terakhir</th>
      <th style="width:130px">Action</th>
    </tr>
  </thead>
  <tbody>
    <?php if (count($rows) === 0) { ?>
      <tr>
        <td colspan="6" class="rm05-empty">Data pasien tidak ditemukan.</td>
      </tr>
    <?php } else {
      foreach ($rows as $k) {
        $paticode = htmlspecialchars($k['PATI_MAST_CODE'], ENT_QUOTES, 'UTF-8');
        $nama = htmlspecialchars($k['PATI_MAIN_NAME'], ENT_QUOTES, 'UTF-8');
        $jk = rm05_gender($k['PATI_MAIN_GEND']);
        $usia = rm05_usia($k['PATI_MAIN_BIRT']);
        $last = !empty($k['LAST_VISIT']) ? date('d-m-Y', strtotime($k['LAST_VISIT'])) : '-';
    ?>
      <tr>
        <td><?php echo $paticode; ?></td>
        <td style="text-align:left;"><?php echo $nama; ?></td>
        <td><?php echo $jk; ?></td>
        <td><?php echo $usia; ?></td>
        <td><?php echo $last; ?></td>
        <td>
          <a class="rm05-btn-detail" href="MEDIRECO05-detail.php?id=<?php echo $paticode; ?>">
            <i class="bi bi-eye"></i> Lihat Detail
          </a>
        </td>
      </tr>
    <?php }
    } ?>
  </tbody>
</table>

<?php if ($total > 0) { ?>
<div class="rm05-pagination" id="rm05Pagination">
  <?php
  $prev = $page - 1;
  $next = $page + 1;
  $prevClass = $page <= 1 ? 'disabled' : '';
  $nextClass = $page >= $totalPages ? 'disabled' : '';
  ?>
  <a href="MEDIRECO05.php?q=<?php echo $qEnc; ?>&page=<?php echo $prev; ?>"
     class="<?php echo $prevClass; ?>"
     data-page="<?php echo $prev; ?>"
     onclick="return goPage(event, <?php echo $prev; ?>);">&laquo;</a>

  <?php
  $start = max(1, $page - 2);
  $end = min($totalPages, $page + 2);
  if ($start > 1) {
    echo '<a href="MEDIRECO05.php?q=' . $qEnc . '&page=1" data-page="1" onclick="return goPage(event,1);">1</a>';
    if ($start > 2) echo '<span>…</span>';
  }
  for ($i = $start; $i <= $end; $i++) {
    $cls = ($i === $page) ? 'active' : '';
    if ($i === $page) {
      echo '<span class="active">' . $i . '</span>';
    } else {
      echo '<a href="MEDIRECO05.php?q=' . $qEnc . '&page=' . $i . '" class="' . $cls . '" data-page="' . $i . '" onclick="return goPage(event,' . $i . ');">' . $i . '</a>';
    }
  }
  if ($end < $totalPages) {
    if ($end < $totalPages - 1) echo '<span>…</span>';
    echo '<a href="MEDIRECO05.php?q=' . $qEnc . '&page=' . $totalPages . '" data-page="' . $totalPages . '" onclick="return goPage(event,' . $totalPages . ');">' . $totalPages . '</a>';
  }
  ?>

  <a href="MEDIRECO05.php?q=<?php echo $qEnc; ?>&page=<?php echo $next; ?>"
     class="<?php echo $nextClass; ?>"
     data-page="<?php echo $next; ?>"
     onclick="return goPage(event, <?php echo $next; ?>);">&raquo;</a>

  <span style="border:none;background:transparent;font-weight:500;color:#64748b;min-width:auto;">
    <?php echo $total; ?> data · hlm <?php echo $page; ?>/<?php echo $totalPages; ?>
  </span>
</div>
<?php } ?>
