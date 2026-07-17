<?php
/**
 * Filter multi-line nilai rujukan lab berdasarkan umur & jenis kelamin.
 * Rules:
 *  - umur <= 17  -> ANAK-ANAK
 *  - umur >= 18 + F -> PEREMPUAN
 *  - umur >= 18 + M -> LAKI-LAKI
 */
function filter_lab_rujukan($rujukan, $umur_tahun, $gender)
{
    if ($rujukan === null || trim($rujukan) === '') {
        return '';
    }

    $text = str_replace(array("\r\n", "\r", "<br>", "<br/>", "<br />", "<BR>", "<BR/>", "<BR />"), "\n", $rujukan);
    $lines = explode("\n", $text);

    $parsed = array();
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }

        if (preg_match('/^(.+?)\s*:\s*(.+)$/u', $line, $m)) {
            $label = strtoupper(trim($m[1]));
            $value = trim($m[2]);
            $label_norm = preg_replace('/[\s\-]+/', '', $label);

            $key = null;
            if (strpos($label_norm, 'ANAK') !== false) {
                $key = 'ANAK';
            } else if (strpos($label_norm, 'PEREMPUAN') !== false || strpos($label_norm, 'WANITA') !== false) {
                $key = 'PEREMPUAN';
            } else if (strpos($label_norm, 'LAKILAKI') !== false || $label_norm === 'PRIA' || $label_norm === 'LAKI') {
                $key = 'LAKI';
            }

            if ($key !== null && $value !== '') {
                $parsed[$key] = $value;
            } else {
                $parsed['_raw'][] = $line;
            }
        } else {
            $parsed['_raw'][] = $line;
        }
    }

    if (!isset($parsed['ANAK']) && !isset($parsed['PEREMPUAN']) && !isset($parsed['LAKI'])) {
        if (!empty($parsed['_raw'])) {
            return implode(' / ', $parsed['_raw']);
        }
        return trim(preg_replace("/\n+/", ' / ', $text));
    }

    $umur = is_numeric($umur_tahun) ? (int)$umur_tahun : null;
    $gend = strtoupper(trim((string)$gender));

    if ($umur !== null && $umur <= 17) {
        if (isset($parsed['ANAK'])) {
            return $parsed['ANAK'];
        }
    }

    if ($umur !== null && $umur >= 18 && ($gend === 'F' || $gend === 'P' || $gend === 'PEREMPUAN')) {
        if (isset($parsed['PEREMPUAN'])) {
            return $parsed['PEREMPUAN'];
        }
    }

    if ($umur !== null && $umur >= 18 && ($gend === 'M' || $gend === 'L' || $gend === 'LAKI-LAKI' || $gend === 'LAKI')) {
        if (isset($parsed['LAKI'])) {
            return $parsed['LAKI'];
        }
    }

    if (!empty($parsed['_raw'])) {
        return implode(' / ', $parsed['_raw']);
    }

    $fallback = array();
    if (isset($parsed['LAKI'])) $fallback[] = 'LAKI-LAKI : ' . $parsed['LAKI'];
    if (isset($parsed['PEREMPUAN'])) $fallback[] = 'PEREMPUAN : ' . $parsed['PEREMPUAN'];
    if (isset($parsed['ANAK'])) $fallback[] = 'ANAK-ANAK : ' . $parsed['ANAK'];
    return implode("\n", $fallback);
}

/**
 * Parse angka dari string lab (dukung koma desimal Indonesia: 13,0).
 */
function lab_parse_number($value)
{
    if ($value === null) {
        return null;
    }
    $s = trim((string)$value);
    $s = preg_replace('/^\*+/', '', $s);
    $s = str_replace(array(' ', "\xc2\xa0"), '', $s);
    // 13,0 -> 13.0 ; 150.000 (ribuan) jarang di lab hasil, prioritaskan koma desimal
    if (strpos($s, ',') !== false && strpos($s, '.') === false) {
        $s = str_replace(',', '.', $s);
    } else if (strpos($s, ',') !== false && strpos($s, '.') !== false) {
        // 1.500,5 -> 1500.5
        $s = str_replace('.', '', $s);
        $s = str_replace(',', '.', $s);
    }
    if (!is_numeric($s)) {
        return null;
    }
    return (float)$s;
}

/**
 * Cek apakah hasil di luar rentang nilai normal.
 * Rujukan contoh: "12,0 - 16,0", "4000 - 10000", "< 5", "> 10"
 * Return true jika abnormal (hasil < min ATAU hasil > max).
 */
function is_lab_hasil_abnormal($hasil, $rujukan)
{
    $num = lab_parse_number($hasil);
    if ($num === null) {
        return false;
    }
    if ($rujukan === null || trim((string)$rujukan) === '') {
        return false;
    }

    $ref = trim((string)$rujukan);
    $ref = str_replace(array("\r\n", "\n", "\r"), ' ', $ref);

    // "< 5" / "<= 5"
    if (preg_match('/^<\s*=?\s*(.+)$/u', $ref, $m)) {
        $max = lab_parse_number($m[1]);
        return ($max !== null && $num > $max);
    }
    // "> 10" / ">= 10"
    if (preg_match('/^>\s*=?\s*(.+)$/u', $ref, $m)) {
        $min = lab_parse_number($m[1]);
        return ($min !== null && $num < $min);
    }
    // "12,0 - 16,0" / "12,0–16,0" / "12,0 s/d 16,0"
    if (preg_match('/([0-9]+(?:[.,][0-9]+)?)\s*(?:-|–|—|s\/d|sd|sampai)\s*([0-9]+(?:[.,][0-9]+)?)/iu', $ref, $m)) {
        $min = lab_parse_number($m[1]);
        $max = lab_parse_number($m[2]);
        if ($min === null || $max === null) {
            return false;
        }
        if ($min > $max) {
            $tmp = $min;
            $min = $max;
            $max = $tmp;
        }
        return ($num < $min || $num > $max);
    }

    return false;
}

/**
 * Prefix "*" di depan hasil jika di luar nilai normal.
 * Contoh: 11 dengan rujukan 12,0 - 16,0 -> *11
 */
function format_lab_hasil_flag($hasil, $rujukan)
{
    $hasil = ($hasil === null) ? '' : trim((string)$hasil);
    if ($hasil === '') {
        return $hasil;
    }
    // hindari double star
    $clean = preg_replace('/^\*+\s*/', '', $hasil);
    if (is_lab_hasil_abnormal($clean, $rujukan)) {
        return '*' . $clean;
    }
    return $clean;
}
