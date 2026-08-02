<?php
include "conf/config.php";

$payment_types = [
    'U' => 'Umum',
    'B' => 'BPJS',
    'A' => 'Asuransi',
    'P' => 'Perusahaan',
    'H' => 'Halodoc'
];
?>

<link rel="stylesheet" href="assets/css/modern-table.css">
<div class="table-wrapper">
    <table class="modern-table">
        <thead>
            <tr>
                <th>Tgl Daftar</th>
                <th>No. Antrian</th>
                <th>Poli</th>
                <th>Nama Pasien</th>
                <th>Pembayaran</th>
                <th>Rujukan</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $kata = isset($_POST['q']) ? trim($_POST['q']) : '';
            $page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
            $perpage = 5;
            $offset = ($page - 1) * $perpage;

            $where = "r.TRXA_REGI_POLI = 'PU' 
                      AND r.TRXA_REGI_RUJUK_TYPE = 'RS' 
                      AND r.TRXA_VIEW_STAT = 'Y'";

            $params = [];
            if (!empty($kata)) {
                $where .= " AND (r.TRXA_REGI_CODE LIKE :kata OR p.PATI_MAIN_NAME LIKE :kata)";
                $params[':kata'] = "%$kata%";
            }

            $order = "ORDER BY r.TRXA_ENTR_DATE DESC, r.TRXA_ENTR_TIME DESC";

            $countSql = "SELECT COUNT(*)
                FROM trxaregi r
                LEFT JOIN patimast p ON p.PATI_MAST_CODE = r.TRXA_PATI_CODE
                WHERE $where";
            $stmtCount = $db->prepare($countSql);
            foreach ($params as $k => $v) {
                $stmtCount->bindValue($k, $v, PDO::PARAM_STR);
            }
            $stmtCount->execute();
            $total = (int) $stmtCount->fetchColumn();
            $totalPages = max(1, (int) ceil($total / $perpage));
            if ($page > $totalPages) {
                $page = $totalPages;
                $offset = ($page - 1) * $perpage;
            }

            $sql = "SELECT 
                        r.TRXA_REGI_CODE, 
                        r.TRXA_PATI_CODE,
                        r.TRXA_ENTR_DATE,
                        r.TRXA_REGI_LIST, 
                        r.TRXA_REGI_PAYM, 
                        r.TRXA_REGI_STAT,
                        r.TRXA_REGI_RUJUK_NOTE,
                        r.TRXA_ENTR_TIME,
                        CONCAT(p.PATI_MAIN_TITL, ' ', p.PATI_MAIN_NAME) AS PATI_NAME,
                        pl.TBLA_POLI_NAME AS POLI_NAME
                    FROM trxaregi r
                    LEFT JOIN patimast p ON p.PATI_MAST_CODE = r.TRXA_PATI_CODE
                    LEFT JOIN tblapoli pl ON pl.TBLA_POLI_CODE = r.TRXA_REGI_POLI
                    WHERE $where
                    $order
                    LIMIT $offset, $perpage";

            try {
                $stmt = $db->prepare($sql);
                foreach ($params as $k => $v) {
                    $stmt->bindValue($k, $v, PDO::PARAM_STR);
                }
                $stmt->execute();

                while ($k = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $regicode = htmlspecialchars($k['TRXA_REGI_CODE'] ?? '');
                    $tgl_daftar = htmlspecialchars($k['TRXA_ENTR_DATE'] ?? '');
                    $waktu_daftar = htmlspecialchars($k['TRXA_ENTR_TIME'] ?? '');
                    $no_antrian = htmlspecialchars($k['TRXA_REGI_LIST'] ?? '');
                    $poli_name = htmlspecialchars($k['POLI_NAME'] ?? '-');
                    $pati_name = htmlspecialchars($k['PATI_NAME'] ?? '-');
                    $rujuk_note = htmlspecialchars(trim($k['TRXA_REGI_RUJUK_NOTE'] ?? ''));

                    $xregipaym = $k['TRXA_REGI_PAYM'] ?? '';
                    $regipaym = $payment_types[$xregipaym] ?? 'Fail get Payment';
                    $paymtype = ($regipaym === 'BPJS')
                        ? '<span class="pay-bpjs">BPJS</span>'
                        : '<span class="pay-umum">' . htmlspecialchars($regipaym) . '</span>';

                    $rujuk_badge = '<span class="status-badge status-process">Rujukan RS</span>';
                    if ($rujuk_note !== '') {
                        $rujuk_badge .= '<br><small style="color:#64748b;">' . $rujuk_note . '</small>';
                    }

                    echo '<tr>';
                    echo '<td>' . $tgl_daftar . '<br>' . $waktu_daftar . '</td>';
                    echo '<td>' . $no_antrian . '</td>';
                    echo '<td>' . $poli_name . '</td>';
                    echo '<td>' . $pati_name . '</td>';
                    echo '<td>' . $paymtype . '</td>';
                    echo '<td>' . $rujuk_badge . '</td>';
                    echo '<td>';
                    echo '<div class="action-group">';
                    echo '<button type="button" class="button-view" onclick="lihatRekam(\'' . $regicode . '\');">Lihat</button>';
                    echo '</div>';
                    echo '</td>';
                    echo '</tr>';
                }

                if ($stmt->rowCount() == 0) {
                    echo '<tr><td colspan="7" style="text-align:center;">Tidak ada data pasien rujukan RS.</td></tr>';
                }

            } catch (PDOException $e) {
                echo '<tr><td colspan="7">Terjadi kesalahan pada sistem: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<?php if ($total > 0) { ?>
    <div class="table-pagination" id="tblscreenPagination">
        <?php
        $prev = $page - 1;
        $next = $page + 1;
        $prevClass = $page <= 1 ? 'disabled' : '';
        $nextClass = $page >= $totalPages ? 'disabled' : '';
        ?>
        <a href="#" class="<?php echo $prevClass; ?>" onclick="return tblScreenGo(event, <?php echo $prev; ?>);">&laquo;</a>

        <?php
        $start = max(1, $page - 2);
        $end = min($totalPages, $page + 2);
        if ($start > 1) {
            echo '<a href="#" onclick="return tblScreenGo(event, 1);">1</a>';
            if ($start > 2) {
                echo '<span>&hellip;</span>';
            }
        }
        for ($i = $start; $i <= $end; $i++) {
            if ($i === $page) {
                echo '<span class="active">' . $i . '</span>';
            } else {
                echo '<a href="#" onclick="return tblScreenGo(event, ' . $i . ');">' . $i . '</a>';
            }
        }
        if ($end < $totalPages) {
            if ($end < $totalPages - 1) {
                echo '<span>&hellip;</span>';
            }
            echo '<a href="#" onclick="return tblScreenGo(event, ' . $totalPages . ');">' . $totalPages . '</a>';
        }
        ?>

        <a href="#" class="<?php echo $nextClass; ?>" onclick="return tblScreenGo(event, <?php echo $next; ?>);">&raquo;</a>

        <span class="trx06-info"><?php echo $total; ?> pasien &middot; hlm <?php echo $page; ?>/<?php echo $totalPages; ?></span>
    </div>
<?php } ?>
