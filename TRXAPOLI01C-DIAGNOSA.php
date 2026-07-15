<?php
include "conf/config.php";
?>


<link rel="stylesheet" href="assets/css/modern-table.css">
<table id="screen" class="modern-table">
  <thead>
    <tr>
      <th style="width: 100px;">Kode</th>
      <th style="width: 500px;">Diagnosa</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $rawdata = $_POST['q'];
    list($regicode, $diagcode) = explode("|", $rawdata);

    // $xquery = "SELECT DIAG_ICD_CODE AS ICD_CODE, DIAG_ICD_NOTE AS ICD_NAME
    //         FROM diagmast WHERE DIAG_ICD_CODE LIKE '$diagcode%' AND DIAG_VIEW_STAT='Y' 
    //         OR DIAG_ICD_NOTE LIKE '$diagcode%' AND DIAG_VIEW_STAT = 'Y'
    //         OR DIAG_ICD_NOTE LIKE '%$diagcode%' AND DIAG_VIEW_STAT = 'Y'
    //         ORDER BY DIAG_ICD_CODE";
    
    $xquery = "SELECT 
            DIAG_ICD_CODE AS ICD_CODE, 
            DIAG_ICD_NOTE AS ICD_NAME
          FROM diagmast
          WHERE 
          (
              DIAG_ICD_CODE LIKE '$diagcode%'
              OR DIAG_ICD_NOTE LIKE '$diagcode%'
              OR DIAG_ICD_NOTE LIKE '%$diagcode%'
          )
          AND DIAG_VIEW_STAT = 'Y'
          ORDER BY DIAG_ICD_CODE";

    $q = $db->query($xquery) or die("Gagal ambil data !!");
    $found = false;
    while ($k = $q->fetch(PDO::FETCH_ASSOC)) {

      $found = true;

      $outicdcode = $k['ICD_CODE'];
      $outicdname = $k['ICD_NAME'];

      echo '<tr>';

      echo '<td style="width: 100px;" onClick="isidiagnosa(\'' . $regicode . '\',\'' . $outicdcode . '\',\'' . $outicdname . '\');" 
      style="cursor:pointer">' . $outicdcode . '</td>';

      echo '<td style="width: 500px; text-align: left;" onClick="isidiagnosa(\'' . $regicode . '\',\'' . $outicdcode . '\',\'' . $outicdname . '\');" 
      style="cursor:pointer">' . $outicdname . '</td>';

      echo '</tr>';

      if (!$found) {
        echo '<tr>
            <td colspan="2" style="
                text-align:center;
                padding:14px;
                color:#6b7280;
            ">
                Diagnosa tidak ditemukan
            </td>
          </tr>';
      }
    }
    ?>
  </tbody>
</table>





