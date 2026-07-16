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
    }

    #screendiag {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    #screendiag thead {
        background: #10b981;
        color: white;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    #screendiag th {
        padding: 10px 6px;
        font-size: 13px;
        font-weight: 700;
        text-align: center;
        vertical-align: middle;
        border: none;
        letter-spacing: 0.3px;
    }

    #screendiag td {
        padding: 8px 6px;
        font-size: 12px;
        color: black;
        text-align: center;
        vertical-align: middle;
        border-bottom: 1px solid #e5e7eb;
        overflow-wrap: break-word;
        word-break: break-word;
    }

    #screendiag th:nth-child(1),
    #screendiag td:nth-child(1) {
        width: 20%;
    }

    #screendiag th:nth-child(2),
    #screendiag td:nth-child(2) {
        width: 50%;
    }

    #screendiag th:nth-child(3),
    #screendiag td:nth-child(3) {
        width: 30%;
    }
</style>
<div class="table-container">
    <table id="screendiag">
        <thead>
            <tr>
                <th>Code</th>
                <th>Diagnosa</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $examcode = $_POST['q'];

            $xquery = "SELECT TRXA_EXAM_CODE, TRXA_DIAG_CODE, TRXA_DIAG_NAME
            FROM trxadiag WHERE TRXA_VIEW_STAT='Y'
            AND TRXA_EXAM_CODE =  '$examcode'
            ORDER BY TRXA_DIAG_CODE
            ";

            $q = $db->query($xquery) or die("Gagal Maning!!");
            while ($k = $q->fetch(PDO::FETCH_ASSOC)) {

                echo '<tr>';
                $examcode = $k['TRXA_EXAM_CODE'];
                $diagcode = $k['TRXA_DIAG_CODE'];
                $diagname = $k['TRXA_DIAG_NAME'];
                echo '<td>' . $k['TRXA_DIAG_CODE'] . '</td>';
                echo '<td>' . $k['TRXA_DIAG_NAME'] . '</td>';
                echo '<td>';
                echo '<button type="button" class="btn-modern btn-delete" style="width: 60px; height: 30px;" onclick="hapuscode(\'' . $examcode . '\',\'' . $diagcode . '\');">Delete</button>';
                echo '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>