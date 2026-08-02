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
                <th>Kode</th>
                <th>Nama Tindakan</th>
                <th>Tarif</th>
                <th>Tipe</th>
                <th>Bayar</th>
                <th>Poli</th>

            </tr>
        </thead>
        <tbody>
            <?php
            $kata = isset($_POST['q']) ? $_POST['q'] : '';
            $page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
            $perpage = 7;
            $offset = ($page - 1) * $perpage;
            $panjangkata = strlen($kata);

            $select = "SELECT TBLF_MEDI_CODE AS MEDI_CODE, TBLF_MEDI_NAME, TBLF_MEDI_RATE, 
                  TBLF_MEDI_ROOM, (SELECT TBLA_POLI_NAME FROM tblapoli WHERE TBLA_POLI_CODE = TBLF_MEDI_ROOM) AS MEDI_ROOM,
                  CASE
                      WHEN TBLF_MEDI_TYPE = 'J' THEN 'Jasa'
                      WHEN TBLF_MEDI_TYPE = 'O' THEN 'Operasi'
                      WHEN TBLF_MEDI_TYPE = 'N' THEN 'Non Operasi'
                      ELSE 'Jenis baru'
                  END AS MEDI_TYPE,
                  CASE
                      WHEN TBLF_MEDI_PAYM = 'U' THEN 'Umum'
                      WHEN TBLF_MEDI_PAYM = 'B' THEN 'BPJS'
                      WHEN TBLF_MEDI_PAYM = 'A' THEN 'Asuransi'
                      WHEN TBLF_MEDI_PAYM = 'P' THEN 'Perusahaan'
                      ELSE 'Pembayaran baru'
                  END AS MEDI_PAYM";

            if ($panjangkata > 1) {
                $kata_esc = addslashes($kata);
                $where = "(TBLF_MEDI_NAME LIKE '$kata_esc%' 
                        OR TBLF_MEDI_NAME LIKE '%$kata_esc%') 
                        AND TBLF_MEDI_ACTI = 'A'
                        AND TBLF_VIEW_STAT = 'Y'";
            } else {
                $where = "TBLF_MEDI_ACTI = 'A' AND TBLF_VIEW_STAT = 'Y'";
            }

            $total = (int) $db->query("SELECT COUNT(*) FROM tblfmedi WHERE $where")->fetchColumn();
            $totalPages = max(1, (int) ceil($total / $perpage));
            if ($page > $totalPages) {
                $page = $totalPages;
                $offset = ($page - 1) * $perpage;
            }

            $xquery = "$select FROM tblfmedi WHERE $where ORDER BY TBLF_MEDI_CODE LIMIT $offset, $perpage";

            $q = $db->query($xquery) or die("Gagal Maning!!");
            while ($row = $q->fetch(PDO::FETCH_ASSOC)) {

                echo '<tr>';
                echo '<td>' . $row['MEDI_CODE'] . '</td>';
                echo '<td>' . $row['TBLF_MEDI_NAME'] . '</td>';
                $view_medi_rate = number_format($row['TBLF_MEDI_RATE'], 0, ',', '.');
                echo '<td>Rp. ' . $view_medi_rate . '</td>';
                echo '<td>' . $row['MEDI_TYPE'] . '</td>';
                echo '<td>' . $row['MEDI_PAYM'] . '</td>';
                echo '<td>' . $row['MEDI_ROOM'] . '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<?php if ($total > 0) { ?>
    <div class="table-pagination">
        <?php
        $prev = $page - 1;
        $next = $page + 1;
        $prevClass = $page <= 1 ? 'disabled' : '';
        $nextClass = $page >= $totalPages ? 'disabled' : '';
        ?>
        <a href="#" class="<?php echo $prevClass; ?>" onclick="return trx06Go(event, <?php echo $prev; ?>);">&laquo;</a>

        <?php
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        if ($start > 1) {
            echo '<a href="#" onclick="return trx06Go(event, 1);">1</a>';
            if ($start > 2) {
                echo '<span>&hellip;</span>';
            }
        }
        for ($i = $start; $i <= $end; $i++) {
            if ($i === $page) {
                echo '<span class="active">' . $i . '</span>';
            } else {
                echo '<a href="#" onclick="return trx06Go(event, ' . $i . ');">' . $i . '</a>';
            }
        }
        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                echo '<span>&hellip;</span>';
            }
            echo '<a href="#" onclick="return trx06Go(event, ' . $totalPages . ');">' . $totalPages . '</a>';
        }
        ?>

        <a href="#" class="<?php echo $nextClass; ?>" onclick="return trx06Go(event, <?php echo $next; ?>);">&raquo;</a>

        <span class="trx06-info"><?php echo $total; ?> data &middot; hlm
            <?php echo $page; ?>/<?php echo $totalPages; ?></span>
    </div>
<?php } ?>