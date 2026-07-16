<?php
include "conf/config.php";
?>

<style>
  #screen_suggestion {
    width: 100%;
    border-collapse: collapse;
  }

  #screen_suggestion th {
    padding: 8px 10px;
    font-size: 14px;
    font-weight: 700;
    text-align: center;
    vertical-align: middle;
    border: none;
    letter-spacing: 0.3px;
    border-bottom: 1px solid #e5e7eb;
  }

  #screen_suggestion td {
    padding: 8px 8px;
    font-size: 13px;
    font-weight: 500;
    /* color: #374151; */
    text-align: center;
    vertical-align: middle;
    border-bottom: 1px solid #e5e7eb;
    overflow-wrap: break-word;
    word-break: break-word;
  }

  #screen_suggestion th:nth-child(1),
  #screen_suggestion td:nth-child(1) {
    width: 80%;
  }

  #screen_suggestion th:nth-child(2),
  #screen_suggestion td:nth-child(2) {
    width: 20%;
  }

  #screen_suggestion tr {
    transition: .2s;
  }

  #screen_suggestion tbody tr:hover {
    background: #d0e3f7;
  }
</style>

<table id="screen_suggestion">
  <thead>
    <tr>
      <th>Layanan/Tindakan</th>
      <th>Harga</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $rawdata = $_POST['q'];
    list($kata, $regipoli, $regipaym) = explode("|", $rawdata);

    if (strlen($kata) == 0) {
      $maxquery = "SELECT TBLF_MEDI_CODE, TBLF_MEDI_NAME, TBLF_MEDI_RATE 
                  FROM tblfmedi 
                  WHERE TBLF_MEDI_ROOM = '$regipoli'
                  AND TBLF_MEDI_PAYM IN ('B', 'U')
                  AND TBLF_MEDI_ACTI = 'A'  
                  AND TBLF_VIEW_STAT = 'Y' ORDER BY TBLF_MEDI_CODE";
    } else {
      $maxquery = "SELECT TBLF_MEDI_CODE, TBLF_MEDI_NAME, TBLF_MEDI_RATE 
                  FROM tblfmedi 
                  WHERE TBLF_MEDI_NAME LIKE '%$kata%'
                  AND TBLF_MEDI_ROOM = '$regipoli'
                  AND TBLF_MEDI_PAYM IN ('B', 'U')
                  AND TBLF_MEDI_ACTI = 'A'  
                  AND TBLF_VIEW_STAT = 'Y' ORDER BY TBLF_MEDI_CODE";
    }

    $q = $db->query($maxquery) or die("Gagal ambil list tindakan !!");
    $count = 1;
    while ($k = $q->fetch(PDO::FETCH_ASSOC)) {
      $count++;
      $outmedicode = $k['TBLF_MEDI_CODE'];
      $outmediname = $k['TBLF_MEDI_NAME'];
      $outmedirate = $k['TBLF_MEDI_RATE'];
      $medirate = number_format($k['TBLF_MEDI_RATE'], 0, '', '.');

      echo '<tr onClick="isitindakan(\'' . $outmedicode . '\',\'' . addslashes($outmediname) . '\',\'' . $outmedirate . '\');">';
      echo '<td style="text-align: left;">' . $k['TBLF_MEDI_NAME'] . '</td>';
      echo '<td style="text-align: left; color: #10b981;">Rp.' . $medirate . '</td>';
      echo '</tr>';
    }
    if ($count == 0) {
      echo '<tr><td colspan="2" style="text-align: center; color: #9ca3af; padding: 10px;">Tidak ditemukan tindakan/layanan</td></tr>';
    }
    ?>
  </tbody>
</table>