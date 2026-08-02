<?php
include "conf/config.php";
?>

<link rel="stylesheet" href="assets/css/rekam-medis-table.css">
<style>
    .rm-lab-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 4px;
        padding: 6px 12px;
        border: none;
        border-radius: 8px;
        background: #169C89;
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: .15s;
    }

    .rm-lab-btn:hover {
        background: #0f7a6b;
    }
</style>
<div class="rekam-medis-wrap">
  <table class="rekam-medis-table">
    <thead>
      <tr>
        <th>Dokter<br>Pemeriksa</th>
        <th>TTV<br>Antropometri</th>
        <th>Hasil<br>Pemeriksaan</th>
        <th>Tindak Lanjut</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Array untuk translate hari ke Bahasa Indonesia
      $nama_hari_id = array(
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu'
      );

      $koderm = $_POST['q'];
      $page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
      $perpage = 1;
      $offset = ($page - 1) * $perpage;

      // Hitung total kunjungan untuk pagination
      $countSql = "SELECT COUNT(*)
          FROM trxaregi r
          WHERE r.TRXA_PATI_CODE = '$koderm'
            AND r.TRXA_REGI_STAT IN ('C','P','X')
            AND r.TRXA_REGI_POLI <> '$code_lab_room'";
      $total = (int) $db->query($countSql)->fetchColumn();
      $totalPages = max(1, (int) ceil($total / $perpage));
      if ($page > $totalPages) {
        $page = $totalPages;
        $offset = ($page - 1) * $perpage;
      }

      // Penambahan subquery untuk mapping kolom TTV dan Hasil
      $xquery = "SELECT 
          r.TRXA_REGI_CODE AS REGI_CODE, 
          r.TRXA_PATI_CODE AS PATI_CODE, 
          r.TRXA_REGI_DATE, 
          r.TRXA_REGI_DOCT, 
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
          SELECT 
              TRXA_EXAM_CODE, 
              GROUP_CONCAT(CONCAT(TRXA_DIAG_CODE ,' - ', TRXA_DIAG_NAME) SEPARATOR ';<br>') AS DIAGNOSA
          FROM trxadiag
          GROUP BY TRXA_EXAM_CODE
      ) d ON d.TRXA_EXAM_CODE = r.TRXA_REGI_CODE
      WHERE 
          r.TRXA_PATI_CODE = '$koderm' 
          AND r.TRXA_REGI_STAT IN ('C','P','X')
          AND r.TRXA_REGI_POLI <> '$code_lab_room'
      ORDER BY r.TRXA_REGI_DATE DESC
      LIMIT $offset, $perpage
  ";

      $q = $db->query($xquery) or die("Gagal Ambil Data / Data Tidak di Temukan!!");

      while ($k = $q->fetch(PDO::FETCH_ASSOC)) {
        // Cek ketersediaan data untuk meminimalisir error notice
        $outdoctname = $k['DOCT_NAME'] ? $k['DOCT_NAME'] : '-';

        // Format Tanggal (Contoh: Kamis, 2026-07-09)
        $waktu_db = strtotime($k['TRXA_REGI_DATE']);
        $hari_angka = date("N", $waktu_db);
        $outregidate = $nama_hari_id[$hari_angka] . ', ' . date("Y-m-d", $waktu_db);

        // Variabel TTV
        $outtinggi = !empty($k['TINGGI']) ? $k['TINGGI'] . 'cm' : '-';
        $outberat = !empty($k['BERAT']) ? $k['BERAT'] . 'kg' : '-';
        $outlp = !empty($k['LP']) ? $k['LP'] . 'cm' : '-';
        $outimt = !empty($k['IMT']) ? $k['IMT'] . 'kg.m2' : '-';
        $outdarah = !empty($k['DARAH']) ? $k['DARAH'] . 'mmHg' : '-';
        $outsuhu = !empty($k['SUHU']) ? $k['SUHU'] . '&deg;C' : '-';
        $outrr = !empty($k['RR']) ? $k['RR'] . '/minute' : '-';
        $outhr = !empty($k['HR']) ? $k['HR'] . 'bpm' : '-';

        // Variabel Hasil Pemeriksaan
        $outkeluhan = !empty($k['KELUHAN']) ? $k['KELUHAN'] : '-';
        $outanamnesa = !empty($k['ANAMNESA']) ? $k['ANAMNESA'] : '-';
        $outbody = !empty($k['BODY']) ? $k['BODY'] : '-';
        $outdiagnosa = !empty($k['DIAGNOSA']) ? $k['DIAGNOSA'] : '-';

        // Variabel Tindak Lanjut
        $outcatatan = !empty($k['CATATAN']) ? $k['CATATAN'] : '';
        $outresep = !empty($k['RESEP']) ? $k['RESEP'] : '-';

        // Cari registrasi laboratorium utk kunjungan ini (pasien + tanggal sama,
        // hanya yang sudah punya hasil lab)
        $labRegis = array();
        $qlab = $db->prepare("SELECT r.TRXA_REGI_CODE FROM trxaregi r
            WHERE r.TRXA_PATI_CODE = :pati AND r.TRXA_REGI_DATE = :tgl
            AND r.TRXA_REGI_POLI = 'LB' AND r.TRXA_VIEW_STAT = 'Y'
            AND EXISTS (
                SELECT 1 FROM trxalabo_detail_hasil h
                WHERE h.TRXA_LABO_REGI = r.TRXA_REGI_CODE AND h.HASIL_VIEW_STAT = 'Y'
            )
            ORDER BY r.TRXA_ENTR_TIME DESC");
        $qlab->execute(array(':pati' => $k['PATI_CODE'], ':tgl' => $k['TRXA_REGI_DATE']));
        $labRegis = $qlab->fetchAll(PDO::FETCH_COLUMN);

        echo '<tr>';

        // KOLOM 1: Dokter Pemeriksa
        echo '<td class="rm-col-dokter">';
        echo $outdoctname . '<br>';
        echo 'pada<br>';
        echo $outregidate;
        echo '</td>';

        // KOLOM 2: TTV Antropometri (Menggunakan inner-table agar titik dua sejajar)
        echo '<td>';
        echo '<table class="inner-table">';
        echo '<tr><td class="label-bold">-Tinggi</td><td style="width:5px;">:</td><td>' . $outtinggi . '</td></tr>';
        echo '<tr><td class="label-bold">-BB</td><td>:</td><td>' . $outberat . '</td></tr>';
        echo '<tr><td class="label-bold">-LP</td><td>:</td><td>' . $outlp . '</td></tr>';
        echo '<tr><td class="label-bold">-IMT</td><td>:</td><td>' . $outimt . '</td></tr>';
        echo '<tr><td class="label-bold">-TD</td><td>:</td><td>' . $outdarah . '</td></tr>';
        echo '<tr><td class="label-bold">-Suhu</td><td>:</td><td>' . $outsuhu . '</td></tr>';
        echo '<tr><td class="label-bold">-RR</td><td>:</td><td>' . $outrr . '</td></tr>';
        echo '<tr><td class="label-bold">-HR</td><td>:</td><td>' . $outhr . '</td></tr>';
        echo '</table>';
        echo '</td>';

        // echo '<td>';
        // echo '<span class="label-bold">-Tinggi : </span>';
        // echo nl2br($outtinggi) . '<br>';
        // echo '<span class="label-bold">-BB : </span>';
        // echo nl2br($outberat) . '<br>';
        // echo '<span class="label-bold">-LP : </span>';
        // echo nl2br($outlp) . '<br>';
        // echo '<span class="label-bold">-IMT : </span>';
        // echo nl2br($outimt) . '<br>';
        // echo '<span class="label-bold">-TD : </span>';
        // echo nl2br($outdarah) . '<br>';
        // echo '<span class="label-bold">-Suhu : </span>';
        // echo nl2br($outsuhu) . '<br>';
        // echo '<span class="label-bold">-RR : </span>';
        // echo nl2br($outrr) . '<br>';
        // echo '<span class="label-bold">-HR : </span>';
        // echo nl2br($outhr) . '<br>';
        // echo '</td>';
      
        // KOLOM 3: Hasil Pemeriksaan (nl2br() untuk membaca enter/break baris dari DB)
        echo '<td>';
        echo '<span class="label-bold">-Keluhan</span><br>';
        echo nl2br($outkeluhan) . '<br>';

        echo '<span class="label-bold">-Anamnesa</span><br>';
        echo nl2br($outanamnesa) . '<br>';

        echo '<span class="label-bold">-Pemeriksaan Fisik</span><br>';
        echo nl2br($outbody) . '<br>';

        echo '<span class="label-bold">-Diagnosa :</span><br>';
        echo $outdiagnosa;
        echo '</td>';

        // KOLOM 4: Tindak Lanjut
        echo '<td>';
        // Hanya tampilkan catatan jika ada isinya
        if ($outcatatan != '') {
          echo '<span class="label-bold">-Catatan</span><br>';
          echo nl2br($outcatatan) . '<br>';
        }

        echo '<span class="label-bold">-Non-Farmakologis</span><br>';
        echo '-<br>'; // Sementara diisi "-" sesuai request
      
        echo '<span class="label-bold">-Farmakoterapi</span><br>';
        echo nl2br($outresep) . '<br>';

        echo '<span class="label-bold">-Pemeriksaan Laboratorium</span><br>';
        if (count($labRegis) > 0) {
          foreach ($labRegis as $lcode) {
            echo '<button type="button" class="rm-lab-btn" onclick="lihatHasilLab(\'' . htmlspecialchars($lcode) . '\')">
                    <i class="bi bi-eyedropper"></i> Lihat Hasil Lab</button><br>';
          }
        } else {
          echo '-';
        }
        echo '</td>';

        echo '</tr>';
      }
      ?>
    </tbody>
  </table>

  <?php if ($total > 0) { ?>
    <div class="table-pagination" id="rmPagination">
      <?php
      $prev = $page - 1;
      $next = $page + 1;
      $prevClass = $page <= 1 ? 'disabled' : '';
      $nextClass = $page >= $totalPages ? 'disabled' : '';
      ?>
      <a href="#" class="<?php echo $prevClass; ?>" onclick="return goRekamMedisPage(event, <?php echo $prev; ?>);">&laquo;</a>

      <?php
      $start = max(1, $page - 2);
      $end = min($totalPages, $page + 2);
      if ($start > 1) {
        echo '<a href="#" onclick="return goRekamMedisPage(event, 1);">1</a>';
        if ($start > 2) {
          echo '<span>&hellip;</span>';
        }
      }
      for ($i = $start; $i <= $end; $i++) {
        if ($i === $page) {
          echo '<span class="active">' . $i . '</span>';
        } else {
          echo '<a href="#" onclick="return goRekamMedisPage(event, ' . $i . ');">' . $i . '</a>';
        }
      }
      if ($end < $totalPages) {
        if ($end < $totalPages - 1) {
          echo '<span>&hellip;</span>';
        }
        echo '<a href="#" onclick="return goRekamMedisPage(event, ' . $totalPages . ');">' . $totalPages . '</a>';
      }
      ?>

      <a href="#" class="<?php echo $nextClass; ?>" onclick="return goRekamMedisPage(event, <?php echo $next; ?>);">&raquo;</a>

      <span class="trx06-info"><?php echo $total; ?> kunjungan &middot; hlm <?php echo $page; ?>/<?php echo $totalPages; ?></span>
    </div>
  <?php } ?>
</div>