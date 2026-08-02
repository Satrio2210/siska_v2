<?php
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE);
include "conf/config.php";
?>
<link rel="stylesheet" href="assets/css/modern-table.css">
<div class="table-wrapper">
  <table class="modern-table">
    <thead>
      <tr>
        <th>No Faktur</th>
        <th>Tanggal</th>
        <th>Nominal</th>
        <th>Pembayaran</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $kata = $_POST['q'];
      $panjangkata = strlen($kata);
      if ($panjangkata == 1) {
        $xquery = "SELECT TRXA_DRUG_CODE, TRXA_PAYM_AMNT, TRXA_PAYM_DISC, TRXA_PAYM_MODE, TRXA_DRUG_STAT, TRXA_UPDT_DATE, TRXA_UPDT_TIME 
           FROM trxadrug WHERE TRXA_DRUG_STAT = 'P' 
           AND TRXA_VIEW_STAT = 'Y' 
           ORDER BY TRXA_ENTR_DATE DESC, TRXA_ENTR_TIME DESC";
      } else {
        $xquery = "SELECT TRXA_DRUG_CODE, TRXA_PAYM_AMNT, TRXA_PAYM_DISC, TRXA_PAYM_MODE, TRXA_DRUG_STAT, TRXA_UPDT_DATE, TRXA_UPDT_TIME 
           FROM trxadrug WHERE TRXA_DRUG_CODE LIKE '$kata%' 
           AND TRXA_DRUG_STAT = 'P' 
           AND TRXA_VIEW_STAT = 'Y' 
           ORDER BY TRXA_ENTR_DATE DESC, TRXA_ENTR_TIME DESC";
      }

      $q = $db->query($xquery) or die("Gagal Maning!!");
      while ($k = $q->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>';
        $drugcode = $k['TRXA_DRUG_CODE'];
        echo '<td>' . $k['TRXA_DRUG_CODE'] . '</td>';

        $drugdate = $k['TRXA_UPDT_DATE'];
        $drugtime = $k['TRXA_UPDT_TIME'];
        echo '<td>' . $drugdate . '<br>' . $drugtime . '</td>';

        $view_paymamnt = number_format($k['TRXA_PAYM_AMNT'], 0, '', '.');
        echo '<td>' . $view_paymamnt . '</td>';

        // $view_paymdisc = number_format($k['TRXA_PAYM_DISC'], 0, '', '.');
        // echo '<td>' . $view_paymdisc . '</td>';
      
        if ($k['TRXA_PAYM_MODE'] == 'TUN') {
          $paymmode = 'Cash';
        } else if ($k['TRXA_PAYM_MODE'] == 'BCA') {
          $paymmode = 'Debit BCA';
        } else if ($k['TRXA_PAYM_MODE'] == 'MAN') {
          $paymmode = 'Debit Mandiri';
        } else if ($k['TRXA_PAYM_MODE'] == 'BNI') {
          $paymmode = 'Debit BNI';
        } else if ($k['TRXA_PAYM_MODE'] == 'BCM') {
          $paymmode = 'Transfer BCA';
        } else if ($k['TRXA_PAYM_MODE'] == 'LIN') {
          $paymmode = 'Transfer LinkAja';
        } else {
          $paymmode = $k['TRXA_PAYM_MODE'];
        }

        echo '<td>' . $paymmode . '</td>';

        if ($k['TRXA_DRUG_STAT'] == 'I') {
          $drugstat = '<span class="status-badge status-belum">Belum Lunas</span>';
        } else if ($k['TRXA_DRUG_STAT'] == 'P') {
          $drugstat = '<span class="status-badge status-lunas">Lunas</span>';
        } else {
          $drugstat = '<span class="status-badge status-belum">None</span>';
        }
        echo '<td>' . $drugstat . '</td>';

        echo '<td>';
        ?>
        <div class="action-group">
          <button type="button" class="button-print"
            onClick="javascript: location.href ='TRXADRUG03P.php?drugcode=<?php echo $drugcode; ?>'">Print</button>
        </div>
        <?php
        echo '</td>';
        echo '</tr>';
      }
      ?>
    </tbody>
  </table>
</div>