<?php
// Minimal copy of filter for unit-style check
function filter_lab_rujukan($rujukan, $umur_tahun, $gender)
{
    if ($rujukan === null || trim($rujukan) === '') {
        return '';
    }
    $text = str_replace(array("\r\n", "\r", "<br>", "<br/>", "<br />"), "\n", $rujukan);
    $lines = explode("\n", $text);
    $parsed = array();
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') continue;
        if (preg_match('/^(.+?)\s*:\s*(.+)$/u', $line, $m)) {
            $label = strtoupper(trim($m[1]));
            $value = trim($m[2]);
            $label_norm = preg_replace('/[\s\-]+/', '', $label);
            $key = null;
            if (strpos($label_norm, 'ANAK') !== false) $key = 'ANAK';
            else if (strpos($label_norm, 'PEREMPUAN') !== false) $key = 'PEREMPUAN';
            else if (strpos($label_norm, 'LAKILAKI') !== false) $key = 'LAKI';
            if ($key !== null && $value !== '') $parsed[$key] = $value;
            else $parsed['_raw'][] = $line;
        } else {
            $parsed['_raw'][] = $line;
        }
    }
    if (!isset($parsed['ANAK']) && !isset($parsed['PEREMPUAN']) && !isset($parsed['LAKI'])) {
        return !empty($parsed['_raw']) ? implode(' / ', $parsed['_raw']) : trim($text);
    }
    $umur = is_numeric($umur_tahun) ? (int)$umur_tahun : null;
    $gend = strtoupper(trim((string)$gender));
    if ($umur !== null && $umur <= 17 && isset($parsed['ANAK'])) return $parsed['ANAK'];
    if ($umur !== null && $umur >= 18 && $gend === 'F' && isset($parsed['PEREMPUAN'])) return $parsed['PEREMPUAN'];
    if ($umur !== null && $umur >= 18 && $gend === 'M' && isset($parsed['LAKI'])) return $parsed['LAKI'];
    return 'FALLBACK';
}

$raw = "LAKI-LAKI : 13,0 - 18,0\nPEREMPUAN : 12,0 - 16,0\nANAK-ANAK : 11,0 - 16,0";
$tests = array(
    array(10, 'M', '11,0 - 16,0'),
    array(17, 'F', '11,0 - 16,0'),
    array(18, 'M', '13,0 - 18,0'),
    array(25, 'M', '13,0 - 18,0'),
    array(30, 'F', '12,0 - 16,0'),
    array(40, 'F', '12,0 - 16,0'),
);
$ok = true;
foreach ($tests as $t) {
    $got = filter_lab_rujukan($raw, $t[0], $t[1]);
    $pass = ($got === $t[2]);
    echo ($pass ? 'OK' : 'FAIL') . " umur={$t[0]} g={$t[1]} expect={$t[2]} got={$got}\n";
    if (!$pass) $ok = false;
}
$single = filter_lab_rujukan('4000 - 10000', 25, 'M');
echo ($single === '4000 - 10000' ? 'OK' : 'FAIL') . " single={$single}\n";
echo $ok ? "ALL FILTER TESTS PASSED\n" : "SOME FAILED\n";
