<?php
include "conf/config.php";
include "inc/sanie.php";
?>

<style>
    .table-container {
        overflow: auto;
        border-radius: 16px;
        max-height: 420px;
        border: 1px solid #e2e8f0;
        margin-top: 10px;
    }

    #tbllistitem-farm-receipt {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    #tbllistitem-farm-receipt th,
    #tbllistitem-farm-receipt td {
        padding: 10px 20px;
    }

    #tbllistitem-farm-receipt thead {
        background: #0D9488;
        color: white;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    #tbllistitem-farm-receipt th {
        font-size: 13px;
        font-weight: 700;
        text-align: center;
        vertical-align: middle;
        border: none;
        letter-spacing: 0.3px;
    }

    #tbllistitem-farm-receipt td {
        font-size: 12px;
        /* color: #374151; */
        text-align: center;
        vertical-align: middle;
        border-bottom: 1px solid #e5e7eb;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    #tbllistitem-farm-receipt th:nth-child(1),
    #tbllistitem-farm-receipt td:nth-child(1) {
        width: 30%;
    }

    #tbllistitem-farm-receipt th:nth-child(2),
    #tbllistitem-farm-receipt td:nth-child(2) {
        width: 10%;
    }

    #tbllistitem-farm-receipt th:nth-child(3),
    #tbllistitem-farm-receipt td:nth-child(3) {
        width: 20%;
    }

    #tbllistitem-farm-receipt th:nth-child(4),
    #tbllistitem-farm-receipt td:nth-child(4) {
        width: 20%;
    }

    #tbllistitem-farm-receipt th:nth-child(5),
    #tbllistitem-farm-receipt td:nth-child(5) {
        width: 20%;
    }
</style>

<div class="table-container">
    <table id="tbllistitem-farm-receipt">
        <thead>
            <tr>
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
                $price_prsc = pembulatan((int) round($harga_asli * $profit));

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
            class="btn-icon btn-del btn-fit" onclick="
            if (confirm (\'Hapus Item Resep ?\'))
              { 
            hapuscode(\'' . $prsccode . '\',\'' . $stockcode . '\');
            }"><i class="bi bi-trash-fill"></i>
              </button>';
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>