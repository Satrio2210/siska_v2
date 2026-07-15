<?php
include "conf/config.php";
include "inc/sanie.php";
?>


<link rel="stylesheet" href="assets/css/modern-table.css">

<table id="screen" class="modern-table">
    <thead>
        <tr>

            <!-- <th style="width: 300px">Nama Item</th>
            <th style="width: 100px">Batch</th>
            <th style="width: 100px">Satuan</th>
            <th style="width: 100px">Harga</th>
            <th style="width: 100px">Qty</th>
            <th style="width: 100px">Total</th>
            <th style="width: 200px">Action</th> -->

            <th>Obat</th>
            <th>Qty</th>
            <th>Harga</th>
            <th>Total</th>
            <th>Action</th>

        </tr>
    </thead>
    <tbody>
        <?php
        $prsccode = $_POST['q'];

        $xquery = "SELECT TRXA_PRSC_CODE, TRXA_STOCK_CODE, TRXA_STOCK_BTCH, 
          (SELECT INVE_PART_NAME FROM invemast WHERE INVE_MAST_CODE = TRXA_STOCK_CODE) AS STOCK_NAME,
          (SELECT INVE_SALE_UNIT FROM invemast WHERE INVE_MAST_CODE = TRXA_STOCK_CODE) AS UNIT_CODE,
          (SELECT TBLI_UNIT_NAME FROM tbliunit WHERE TBLI_UNIT_CODE= UNIT_CODE) AS UNIT_NAME,

                (SELECT INVE_MAIN_SPEC FROM invemast WHERE INVE_MAST_CODE = TRXA_STOCK_CODE) AS SPEC_CODE,
                (SELECT TBLI_SPEC_NAME FROM tblispec WHERE TBLI_SPEC_CODE = SPEC_CODE) AS SPEC_NAME,

           TRXA_STOCK_PRIC, TRXA_STOCK_QUTY, (TRXA_STOCK_PRIC * TRXA_STOCK_QUTY) AS TOTAL_HNA,
           ((TRXA_STOCK_PRIC * '$profit') * TRXA_STOCK_QUTY) AS TOTAL_SALE
          FROM trxaprsc 
          WHERE TRXA_PRSC_STAT IN ('A','I') AND TRXA_PRSC_CODE='$prsccode' AND TRXA_VIEW_STAT='Y'
          ORDER BY TRXA_STOCK_CODE";
        $q = $db->query($xquery) or die("Gagal Ambil Daftar Item Resep!!");

        while ($k = $q->fetch(PDO::FETCH_ASSOC)) {

            echo '<tr>';
            $stockcode = $k['TRXA_STOCK_CODE'];
            echo '<td>' . $k['STOCK_NAME'] . ' ' . $k['SPEC_NAME'] . ' ' . $k['UNIT_NAME'] . '</td>';
            echo '<td>' . $k['TRXA_STOCK_QUTY'] . '</td>';

            $harga_asli = $k['TRXA_STOCK_PRIC'];
            $qty = $k['TRXA_STOCK_QUTY'];

            // 1. (Harga Asli * Profit) lalu Pembulatan
            $price_prsc = pembulatan((int)round($harga_asli * $profit));

            // 2. Harga Satuan x Qty = Total
            $total_prsc = $harga_asli * $qty;

            // 3. Format View
            $viewprice = number_format($harga_asli, 0, '', '.');
            $viewtotalhna = number_format($total_prsc, 0, '', '.');
            // $harga_bulat = pembulatan($k['TRXA_STOCK_PRIC']);
            // $viewprice = number_format($harga_bulat, 0, '', '.');

            echo '<td>' . $viewprice . '</td>';

            // $tothna_bulat = pembulatan($k['TOTAL_HNA']);
            // $viewtotalhna = number_format($tothna_bulat, 0, '', '.');
            echo '<td>' . $viewtotalhna . '</td>';

            // $viewtotalsale = number_format($k['TOTAL_SALE'], 0, '', '.');
            // echo '<td>' . $viewtotalsale . '</td>';
        
            // $xharga = round($k['TOTAL_SALE']);
            // $int = (int) $xharga;
        
            // $total_sale = pembulatan($int);
        
            // $viewtotalsale = number_format($total_sale, 0, '', '.');
            //echo '<td style="width: 100px; text-align: right;">'.$viewtotalsale.'</td>';
        
            echo '<td>';
            echo '<button type="button"
            class="btn-delete" onclick="
            if (confirm (\'Hapus Item Resep ?\'))
              { 
            hapuscode(\'' . $prsccode . '\',\'' . $stockcode . '\');
            }">
               ðŸ—‘<i class="fas fa-trash"></>
              </button>';
            echo '</td>';

            echo '</tr>';
        }
        ?>
    </tbody>
</table>






