<?php
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE);
include "conf/config.php";
include "inc/sanie.php";
?>



<link rel="stylesheet" href="assets/css/modern-table.css">
<table id="screen" class="modern-table">
  <!-- <thead>
  <tr>
  <th style="width: 100%;">Pasien</th>
  </tr>
  </thead> -->
  <tbody>
    <?php
    // 1. Pastikan $_POST['q'] ada untuk menghindari error "Undefined index"
    $kata = isset($_POST['q']) ? $_POST['q'] : '';

    // 2. Persiapkan parameter wildcard untuk keamanan PDO (menghindari SQL Injection)
    $paramStart = $kata . '%';     // Untuk pencarian dari depan
    $paramBoth = '%' . $kata . '%'; // Untuk pencarian di mana saja (tengah/belakang)
    
    // 3. Query yang lebih bersih. Kelompokkan kondisi OR dalam kurung ()
    $xquery = "SELECT PATI_MAST_CODE, PATI_MAIN_PIDN, PATI_MAIN_TITL, PATI_MAIN_NAME, 
                  PATI_MAIN_GEND, PATI_MAIN_BIRT, PATI_MAIN_BLOD, PATI_MAIN_ADDR, 
                  PATI_MAIN_WARD, PATI_MAIN_DIST, PATI_MAIN_CITY, PATI_MAIN_PROV,
                  PATI_MAIN_RELI, PATI_MAIN_CTZN, PATI_MAIN_STAT, PATI_MAIN_PROF,
                  PATI_MAIN_EDUC, PATI_MAIN_PHNE, PATI_MAIN_MAIL, PATI_MAIN_PRNT 
           FROM patimast 
           WHERE PATI_VIEW_STAT = 'Y' 
             AND (
                 PATI_MAST_CODE LIKE :paramStart 
              OR PATI_MAIN_PIDN LIKE :paramStart
              OR PATI_MAIN_NAME LIKE :paramBoth 
              OR PATI_MAIN_BIRT LIKE :paramStart
              OR PATI_MAIN_PRNT LIKE :paramStart
             )
           ORDER BY PATI_MAST_CODE";

    // 4. Gunakan Prepare dan Execute untuk PDO
    $q = $db->prepare($xquery);
    $q->execute([
      ':paramStart' => $paramStart,
      ':paramBoth' => $paramBoth
    ]);

    // Kolom yang berurutan sesuai dengan argumen fungsi isipaticode()
    $columns = [
      'PATI_MAST_CODE',
      'PATI_MAIN_PIDN',
      'PATI_MAIN_TITL',
      'PATI_MAIN_NAME',
      'PATI_MAIN_GEND',
      'PATI_MAIN_BIRT',
      'PATI_MAIN_BLOD',
      'PATI_MAIN_ADDR',
      'PATI_MAIN_WARD',
      'PATI_MAIN_DIST',
      'PATI_MAIN_CITY',
      'PATI_MAIN_PROV',
      'PATI_MAIN_RELI',
      'PATI_MAIN_CTZN',
      'PATI_MAIN_STAT',
      'PATI_MAIN_PROF',
      'PATI_MAIN_EDUC',
      'PATI_MAIN_PHNE',
      'PATI_MAIN_MAIL',
      'PATI_MAIN_PRNT'
    ];

    while ($k = $q->fetch(PDO::FETCH_ASSOC)) {

      // 5. Build argumen JS dinamis untuk menghindari syntax error JS jika ada spasi / tanda kutip (' atau ") di data
      $js_args = [];
      foreach ($columns as $col) {
        $val = $k[$col] ?? '';
        // addslashes() mengamankan single quote, htmlspecialchars() mengamankan HTML attribute
        $js_args[] = "'" . htmlspecialchars(addslashes($val), ENT_QUOTES, 'UTF-8') . "'";
      }
      $js_args_string = implode(', ', $js_args);

      // Variabel untuk ditampilin di text HTML
      $disp_name = htmlspecialchars($k['PATI_MAIN_NAME'] ?? '', ENT_QUOTES, 'UTF-8');
      $disp_birt = htmlspecialchars($k['PATI_MAIN_BIRT'] ?? '', ENT_QUOTES, 'UTF-8');
      $disp_addr = htmlspecialchars($k['PATI_MAIN_ADDR'] ?? '', ENT_QUOTES, 'UTF-8');

      // 6. Cetak HTML. Perhatikan js_args_string dipanggil langsung ke onclick
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





