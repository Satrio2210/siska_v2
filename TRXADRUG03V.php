<?php
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE);
include "conf/config.php";
?>

<div class="table-wrapper">
  <table id="screen" class="custom-pharmacy-table">
    <thead>
      <tr>
        <th style="width: 16%">No Faktur</th>
        <th style="width: 12%">Tanggal</th>
        <th style="width: 14%">Nominal</th>
        <th style="width: 12%">Diskon</th>
        <th style="width: 16%">Pembayaran</th>
        <th style="width: 14%">Status</th>
        <th style="width: 16%">Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $kata = $_POST['q'];
      $panjangkata = strlen($kata);
      if ($panjangkata == 1) {
        $xquery = "SELECT TRXA_DRUG_CODE, TRXA_PAYM_AMNT, TRXA_PAYM_DISC, TRXA_PAYM_MODE, TRXA_DRUG_STAT, TRXA_UPDT_DATE 
           FROM trxadrug WHERE TRXA_DRUG_STAT = 'P' 
           AND TRXA_VIEW_STAT = 'Y' 
           ORDER BY TRXA_ENTR_DATE DESC, TRXA_ENTR_TIME DESC";
      } else {
        $xquery = "SELECT TRXA_DRUG_CODE, TRXA_PAYM_AMNT, TRXA_PAYM_DISC, TRXA_PAYM_MODE, TRXA_DRUG_STAT, TRXA_UPDT_DATE 
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
        $drugdate = date("d-m-Y", strtotime($k['TRXA_UPDT_DATE']));
        echo '<td>' . $drugdate . '</td>';

        $view_paymamnt = number_format($k['TRXA_PAYM_AMNT'], 0, '', '.');
        echo '<td style="text-align: right;">' . $view_paymamnt . '</td>';

        $view_paymdisc = number_format($k['TRXA_PAYM_DISC'], 0, '', '.');
        echo '<td style="text-align: right;">' . $view_paymdisc . '</td>';

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
          $drugstat = 'Out Standing';
        } else if ($k['TRXA_DRUG_STAT'] == 'P') {
          $drugstat = 'Paid Off';
        } else {
          $drugstat = 'None';
        }
        echo '<td>' . $drugstat . '</td>';

        echo '<td>';
        ?>
        <a class="btn-print"
          onClick="javascript: location.href ='TRXADRUG03P.php?drugcode=<?php echo $drugcode; ?>'">Print</a>
        <?php
        echo '</td>';
        echo '</tr>';
      }
      ?>
    </tbody>
  </table>
</div>
