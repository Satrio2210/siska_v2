<?php
include "conf/config.php";
?>


<link rel="stylesheet" href="assets/css/modern-table.css">

<div class="table-container">
  <table id="screen" class="modern-table">
    <thead>
      <tr>
        <th style="width: 20%;">Dokter<br>Pemeriksa</th>
        <th style="width: 25%;">TTV<br>Antropometri</th>
        <th style="width: 30%;">Hasil<br>Pemeriksaan</th>
        <th style="width: 25%;">Tindak Lanjut</th>
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

      // Penambahan subquery untuk mapping kolom TTV dan Hasil
      $xquery = "
      SELECT 
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
          AND r.TRXA_REGI_STAT IN ('C','X')
          AND r.TRXA_REGI_POLI <> '$code_lab_room'
      ORDER BY r.TRXA_REGI_DATE DESC
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

        echo '<tr>';

        // KOLOM 1: Dokter Pemeriksa
        echo '<td style="text-align: center; vertical-align: middle;">';
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
        echo nl2br($outresep);
        echo '</td>';

        echo '</tr>';
      }
      ?>
    </tbody>
  </table>
</div>





