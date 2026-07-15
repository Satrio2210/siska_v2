<?php
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE);
include "conf/config.php";
include "inc/sanie.php";
?>


<link rel="stylesheet" href="assets/css/modern-table.css">
<table id="screen" class="modern-table">
  <tbody>
    <?php
    // 1. Ambil input pencarian
    $kata = $_POST['q'] ?? '';

    // 2. Base Query (Mencegah ngetik ulang)
    $xquery = "SELECT PATI_MAST_CODE, PATI_MAIN_PIDN, PATI_MAIN_NAME, PATI_MAIN_GEND, PATI_MAIN_BIRT, 
                      PATI_MAIN_BLOD, PATI_MAIN_ADDR, PATI_MAIN_PHNE, PATI_MAIN_PRNT 
               FROM patimast 
               WHERE PATI_VIEW_STAT = 'Y'";

    $params = [];

    // 3. Tambahkan kondisi LIKE jika panjang karakter lebih dari 1
    if (strlen($kata) > 1) {
      $xquery .= " AND (
                        PATI_MAIN_PIDN LIKE :kata_mid 
                        OR PATI_MAIN_NAME LIKE :kata_start 
                        OR PATI_MAIN_BIRT LIKE :kata_start 
                        OR PATI_MAIN_PRNT LIKE :kata_start
                     )";

      // Setup parameter untuk mencegah SQL Injection
      $params[':kata_mid'] = "%$kata%";
      $params[':kata_start'] = "$kata%";
    }

    // 4. Tambahkan pengurutan
    $xquery .= " ORDER BY PATI_MAST_CODE";

    // 5. Eksekusi pakai Prepared Statement
    $stmt = $db->prepare($xquery);

    // Validasi prepare statement sebelum execute
    if (!$stmt) {
      die("Gagal menyiapkan query !!");
    }

    $stmt->execute($params);

    // 6. Looping data
    while ($k = $stmt->fetch(PDO::FETCH_ASSOC)) {

      // Definisikan kolom-kolom yang akan dikirim ke fungsi JS (Sesuai urutan isipaticode)
      // Perhatikan saya asumsikan formatTanggal() sudah tersedia di kodemu
      $js_raw_args = [
        $k['PATI_MAST_CODE'] ?? '',
        $k['PATI_MAIN_NAME'] ?? '',
        $k['PATI_MAIN_GEND'] ?? '',
        isset($k['PATI_MAIN_BIRT']) ? formatTanggal($k['PATI_MAIN_BIRT']) : '',
        $k['PATI_MAIN_BLOD'] ?? '',
        $k['PATI_MAIN_ADDR'] ?? '',
        $k['PATI_MAIN_PHNE'] ?? ''
      ];

      // WAJIB: Amankan string untuk dimasukkan ke fungsi JS di dalam atribut HTML
      $js_safe_args = array_map(function ($val) {
        // addslashes() amankan quote tunggal ' -> \' untuk JS
        // htmlspecialchars() amankan quote ganda " -> &quot; untuk HTML atribut
        return "'" . htmlspecialchars(addslashes($val), ENT_QUOTES, 'UTF-8') . "'";
      }, $js_raw_args);

      // Gabungkan array menjadi string dipisah koma: 'arg1', 'arg2', 'arg3'
      $js_args_string = implode(', ', $js_safe_args);

      // Variabel untuk ditampilin di HTML
      $disp_name = htmlspecialchars($k['PATI_MAIN_NAME'] ?? '', ENT_QUOTES, 'UTF-8');
      $disp_birt = htmlspecialchars($k['PATI_MAIN_BIRT'] ?? '', ENT_QUOTES, 'UTF-8');
      $disp_addr = htmlspecialchars($k['PATI_MAIN_ADDR'] ?? '', ENT_QUOTES, 'UTF-8');

      // Cetak HTML pakai heredoc (<<<HTML)
      echo <<<HTML
        <tr onclick="isipaticode({$js_args_string})" style="cursor:pointer;">
            <td>
                <div class="patient-name">
                    {$disp_name}
                </div>
                <div class="patient-birth">
                    {$disp_birt}
                </div>
                <div class="patient-addr">
                    {$disp_addr}
                </div>
            </td>
        </tr>
        HTML;
    }
    ?>
  </tbody>
</table>





