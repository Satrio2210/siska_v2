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

    #screenlybl {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    #screenlybl thead {
        background: #10b981;
        color: white;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    #screenlybl th {
        padding: 6px 6px;
        font-size: 13px;
        font-weight: 700;
        text-align: center;
        vertical-align: middle;
        border: none;
        letter-spacing: 0.3px;
    }

    #screenlybl td {
        padding: 8px 6px;
        font-size: 12px;
        /* color: #374151; */
        text-align: center;
        vertical-align: middle;
        border-bottom: 1px solid #e5e7eb;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    #screenlybl th:nth-child(1),
    #screenlybl td:nth-child(1) {
        width: 40%;
    }

    #screenlybl th:nth-child(2),
    #screenlybl td:nth-child(2) {
        width: 20%;
    }

    #screenlybl th:nth-child(3),
    #screenlybl td:nth-child(3) {
        width: 20%;
    }

    #screenlybl th:nth-child(4),
    #screenlybl td:nth-child(4) {
        width: 20%;
    }
</style>
<div class="table-container">
    <table id="screenlybl">
        <thead>
            <tr>
                <th>Layanan</th>
                <th>Qty</th>
                <th>Total.</th>
                <th>Action</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $tretcode = $_POST['q'];

            $xquery = "SELECT TRXA_TRET_CODE, TRXA_MEDI_CODE, (SELECT TBLF_MEDI_NAME FROM tblfmedi WHERE TBLF_MEDI_CODE = TRXA_MEDI_CODE) AS MEDI_NAME,
           TRXA_MEDI_RATE, TRXA_TRET_QUTY, (TRXA_MEDI_RATE * TRXA_TRET_QUTY) AS TOTAL
          FROM trxatret 
          WHERE TRXA_TRET_STAT='I' AND TRXA_TRET_CODE='$tretcode' AND TRXA_VIEW_STAT='Y'
          ORDER BY TRXA_MEDI_CODE";

            $q = $db->query($xquery) or die("Gagal Maning!!");
            while ($k = $q->fetch(PDO::FETCH_ASSOC)) {

                echo '<tr>';
                $medicode = $k['TRXA_MEDI_CODE'];
                echo '<td>' . $k['MEDI_NAME'] . '</td>';

                $viewrate = number_format($k['TRXA_MEDI_RATE'], 0, '', '.');
                // echo '<td style="width: 100px; text-align: right;">'.$viewrate.'</td>';
            
                echo '<td>' . $k['TRXA_TRET_QUTY'] . '</td>';

                $viewtotal = number_format($k['TOTAL'], 0, '', '.');
                echo '<td>' . $viewtotal . '</td>';

                echo '<td>';
                echo '<a class="btn-modern btn-delete"
              onclick="if (confirm (\'Are You Sure To Delete ?\'))
              { hapuscode(\'' . $tretcode . '\',\'' . $medicode . '\');}
              else
              { document.getElementById(\'txtmedicode\').focus();}
              "><i class="bi bi-trash-fill"></i></a>';

                echo '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>