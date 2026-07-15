<?php
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE);
include "conf/config.php";
?>


<link rel="stylesheet" href="assets/css/modern-table.css">
<table id="screen" class="modern-table">
  <!-- <thead>
    <tr>
      <th style="width: 200px;">DOCTOR</th>
      <th style="width: 200px;">MEDICAL ROOM</th>
    </tr>
  </thead> -->
  <tbody>
    <?php
    $kata = $_POST['q'];
    $weekdays = date('w');

    if (strlen($kata) == 1) {
      $xquery = "SELECT TRXA_DOCT_USER, TRXA_DOCT_NAME, TRXA_MEDI_ROOM,
            (SELECT TBLA_POLI_NAME FROM tblapoli WHERE TBLA_POLI_CODE = TRXA_MEDI_ROOM) AS ROOM_NAME
            FROM trxaschd WHERE TRXA_SCHD_DAYS = '$weekdays' AND TRXA_VIEW_STAT = 'Y'";

    } else {
      $xquery = "SELECT TRXA_DOCT_USER, TRXA_DOCT_NAME, TRXA_MEDI_ROOM,
            (SELECT TBLA_POLI_NAME FROM tblapoli WHERE TBLA_POLI_CODE = TRXA_MEDI_ROOM) AS ROOM_NAME
            FROM trxaschd WHERE TRXA_DOCT_NAME LIKE '$kata%'
            AND TRXA_SCHD_DAYS = '$weekdays' AND TRXA_VIEW_STAT = 'Y'
            OR TRXA_DOCT_NAME LIKE '%$kata%'
            AND TRXA_SCHD_DAYS = '$weekdays' AND TRXA_VIEW_STAT = 'Y'
            ";
    }

    $q = $db->query($xquery) or die("Gagal ambil data !!");
    while ($k = $q->fetch(PDO::FETCH_ASSOC)) {
      $outdoctuser = $k['TRXA_DOCT_USER'];
      $outdoctname = $k['TRXA_DOCT_NAME'];
      $outmediroom = $k['TRXA_MEDI_ROOM'];
      $outroomname = $k['ROOM_NAME'];

      // echo '<tr>';

      // echo '<td style="width: 200px;" onClick="isidoctuser(\'' . $outdoctuser . '\',\'' . $outdoctname . '\',\'' . $outmediroom . '\',\'' . $outroomname . '\');" 
      // style="cursor:pointer">' . $k['TRXA_DOCT_NAME'] . '</td>';
      // echo '<td style="width: 200px;" onClick="isidoctuser(\'' . $outdoctuser . '\',\'' . $outdoctname . '\',\'' . $outmediroom . '\',\'' . $outroomname . '\');" 
      // style="cursor:pointer">' . $k['ROOM_NAME'] . '</td>';

      // echo '</tr>';

      echo '
      <tr onclick="isidoctuser(
      \'' . $outdoctuser . '\',
      \'' . $outdoctname . '\',
      \'' . $outmediroom . '\',
      \'' . $outroomname . '\'
      )">

      <td>

      <div class="doctor-name">
      ' . $k['TRXA_DOCT_NAME'] . '
      </div>

      <div class="room-name">
      ' . $k['ROOM_NAME'] . '
      </div>

      </td>

      </tr>
      ';
    }
    ?>
  </tbody>
</table>





