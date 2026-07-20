<?php
include "conf/config.php";

$regicode = isset($_POST['q']) ? trim($_POST['q']) : '';
if ($regicode === '') {
  echo json_encode(array('ok' => false));
  exit;
}

$xquery = "
SELECT 
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
  r.TRXA_REGI_RUJUK_TYPE AS RUJUK_TYPE,
  r.TRXA_REGI_RUJUK_NOTE AS RUJUK_NOTE,
  d.DIAGNOSA
FROM trxaregi r
LEFT JOIN trxaexam e ON e.TRXA_EXAM_CODE = r.TRXA_REGI_CODE
LEFT JOIN (
  SELECT 
    TRXA_EXAM_CODE, 
    GROUP_CONCAT(CONCAT(TRXA_DIAG_CODE ,' - ', TRXA_DIAG_NAME) SEPARATOR '; ') AS DIAGNOSA
  FROM trxadiag
  GROUP BY TRXA_EXAM_CODE
) d ON d.TRXA_EXAM_CODE = r.TRXA_REGI_CODE
WHERE r.TRXA_REGI_CODE = :regicode
LIMIT 1
";

$st = $db->prepare($xquery);
$st->execute(array(':regicode' => $regicode));
$k = $st->fetch(PDO::FETCH_ASSOC);

if (!$k) {
  echo json_encode(array('ok' => false));
  exit;
}

function v($val, $suf = '') {
  if ($val === null || $val === '') return '-';
  return $val . $suf;
}

$rujukType = isset($k['RUJUK_TYPE']) ? trim($k['RUJUK_TYPE']) : '';
$rujukNote = isset($k['RUJUK_NOTE']) ? trim($k['RUJUK_NOTE']) : '';
$rujukLabel = '-';
if ($rujukType === 'LB') {
  $rujukLabel = 'Laboratorium' . ($rujukNote !== '' ? ' - ' . $rujukNote : '');
} else if ($rujukType === 'RS') {
  $rujukLabel = 'Rumah Sakit' . ($rujukNote !== '' ? ' - ' . $rujukNote : '');
} else if ($rujukNote !== '') {
  $rujukLabel = $rujukNote;
}

$out = array(
  'ok' => true,
  'td' => v($k['DARAH'], ' mmHg'),
  'bb' => v($k['BERAT'], ' kg'),
  'tb' => v($k['TINGGI'], ' cm'),
  'nadi' => v($k['HR'], ' bpm'),
  'suhu' => v($k['SUHU'], ' °C'),
  'rr' => v($k['RR'], ' /mnt'),
  'lp' => v($k['LP'], ' cm'),
  'imt' => v($k['IMT']),
  'keluhan' => v($k['KELUHAN']),
  'anamnesa' => v($k['ANAMNESA']),
  'body' => v($k['BODY']),
  'diagnosa' => v($k['DIAGNOSA']),
  'rencana' => v($k['CATATAN']),
  'resep' => v($k['RESEP']),
  'rujukan' => $rujukLabel
);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($out);
