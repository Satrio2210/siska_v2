<?php
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE);
include "conf/config.php";
include "inc/sanie.php";

$regicode = $_POST['q'];

$grand_total = 0;
$discount = 0;

$stRegi = $db->prepare("
    SELECT
        r.TRXA_PATI_CODE,
        r.TRXA_REGI_STAT,
        CONCAT(p.PATI_MAIN_TITL, ' ', p.PATI_MAIN_NAME) AS PATI_NAME,
        r.TRXA_REGI_FEE,
        r.TRXA_REGI_PAYM
    FROM trxaregi r
    LEFT JOIN patimast p ON p.PATI_MAST_CODE = r.TRXA_PATI_CODE
    WHERE r.TRXA_REGI_CODE = :regi
      AND r.TRXA_VIEW_STAT = 'Y'
");
$stRegi->execute([':regi' => $regicode]);
$row_regi = $stRegi->fetch(PDO::FETCH_ASSOC);

if (!$row_regi) {
    echo '<div class="alert alert-warning">Data registrasi tidak ditemukan.</div>';
    exit;
}

$patiname = $row_regi['PATI_NAME'];
$tipe_pembayaran = $row_regi['TRXA_REGI_PAYM'];
$fee_admin_aktif = ($row_regi['TRXA_REGI_FEE'] == 'Y');

if ($row_regi['TRXA_REGI_STAT'] == 'C') {
    $registat = 'Periksa';
} else if ($row_regi['TRXA_REGI_STAT'] == 'P') {
    $registat = 'Bayar';
} else {
    $registat = 'Antri';
}
?>

<style>
  .invoice-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    font-family: Inter, Arial, sans-serif;
    font-size: 13px;
    color: #1f2937;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    overflow: hidden;
  }

  .invoice-table thead th {
    background: #007aff;
    color: #fff;
    font-weight: 700;
    padding: 10px 12px;
    border: none;
    border-bottom: 1px solid #d1d5db;
    letter-spacing: 0.2px;
  }

  .invoice-table .section-header {
    background: #007aff !important;
    color: #fff !important;
    font-weight: 700;
    text-align: left;
    padding: 8px 12px;
    border: none;
    border-bottom: 1px solid #d1d5db;
    letter-spacing: 0.2px;
  }

  .invoice-table tbody tr td {
    border-bottom: 1px solid #d1d5db;
  }

  .invoice-table tbody tr.item-row td {
    background: #fff;
    padding: 8px 12px;
    border-bottom: 1px solid #d1d5db;
    vertical-align: middle;
  }

  .invoice-table tbody tr.item-row:nth-child(even) td {
    background: #f2f2f2;
  }

  .invoice-table tbody tr.item-row:hover td {
    background: #eaf3ff;
  }

  .invoice-table .text-left {
    text-align: left;
  }

  .invoice-table .text-center {
    text-align: center;
  }

  .invoice-table .text-right {
    text-align: right;
  }

  .invoice-table .font-bold {
    font-weight: 700;
  }

  .invoice-table tr.total-row td {
    background: #f8fafc;
    padding: 8px 12px;
    border-top: none;
    border-bottom: 1px solid #d1d5db;
    color: #111827;
  }

  .invoice-table tr.total-row.total-final td {
    background: #eaf3ff;
    color: #007aff;
    font-size: 14px;
    border-bottom: none;
  }
</style>

<table class="invoice-table">
  <thead>
    <tr>
      <th>Keterangan</th>
      <th>Jumlah</th>
      <th>Biaya</th>
      <th>Sub Total</th>
    </tr>
  </thead>
  <tbody>

    <tr>
      <td colspan="4" class="section-header">Layanan</td>
    </tr>

    <?php
    $stTret = $db->prepare("
        SELECT
            a.TRXA_MEDI_CODE,
            b.TBLF_MEDI_NAME AS MEDI_NAME,
            a.TRXA_MEDI_RATE,
            a.TRXA_TRET_QUTY,
            (a.TRXA_MEDI_RATE * a.TRXA_TRET_QUTY) AS SUB_TOTAL,
            b.TBLF_MEDI_TYPE
        FROM trxatret a
        LEFT JOIN tblfmedi b ON a.TRXA_MEDI_CODE = b.TBLF_MEDI_CODE
        WHERE a.TRXA_TRET_CODE = :regi
          AND a.TRXA_TRET_STAT = 'I'
          AND a.TRXA_VIEW_STAT = 'Y'
        ORDER BY b.TBLF_MEDI_TYPE, a.TRXA_MEDI_CODE
    ");
    $stTret->execute([':regi' => $regicode]);

    $rows_layanan = [];
    $rows_tindakan = [];
    while ($row_tret = $stTret->fetch(PDO::FETCH_ASSOC)) {
        if ($row_tret['TBLF_MEDI_TYPE'] === 'J') {
            $rows_layanan[] = $row_tret;
        } else if (in_array($row_tret['TBLF_MEDI_TYPE'], ['O', 'N'], true)) {
            $rows_tindakan[] = $row_tret;
        }
    }

    foreach ($rows_layanan as $row_tret) {
        $grand_total += $row_tret['SUB_TOTAL'];

        echo '<tr class="item-row">';
        echo '<td class="text-left">' . htmlspecialchars($row_tret['MEDI_NAME']) . '</td>';
        echo '<td class="text-center">' . (int) $row_tret['TRXA_TRET_QUTY'] . '</td>';
        echo '<td class="text-right">Rp. ' . number_format($row_tret['TRXA_MEDI_RATE'], 0, '', '.') . '</td>';
        echo '<td class="text-right">Rp. ' . number_format($row_tret['SUB_TOTAL'], 0, '', '.') . '</td>';
        echo '</tr>';
    }
    ?>

    <tr>
      <td colspan="4" class="section-header">Biaya Admin</td>
    </tr>

    <?php
    $stRacikCount = $db->prepare("
        SELECT
            SUM(CASE WHEN TRXA_PRSC_CONC = 'Y' THEN 1 ELSE 0 END) AS RACIKAN,
            SUM(CASE WHEN TRXA_PRSC_STAT  = 'I' THEN 1 ELSE 0 END) AS RESEP
        FROM trxaprsc
        WHERE TRXA_PRSC_CODE = :regi
          AND TRXA_VIEW_STAT = 'Y'
          AND TRXA_PRSC_STAT = 'I'
    ");
    $stRacikCount->execute([':regi' => $regicode]);
    $rowRC = $stRacikCount->fetch(PDO::FETCH_ASSOC);
    $ketersediaan_racikan = (int) ($rowRC['RACIKAN'] ?? 0);
    $ketersediaan_resep = (int) ($rowRC['RESEP'] ?? 0);

    if ($ketersediaan_racikan == 0) {
        if ($ketersediaan_resep == 0) {
            $total_admin = (!$fee_admin_aktif) ? 0 : $fee_admin;
        } else {
            $total_admin = ($fee_admin + $fee_resep);
        }
    } else {
        $total_admin = ($fee_admin + ($fee_resep + $fee_racikan));
    }

    if ($tipe_pembayaran == 'B') {
        $total_admin = 0;
    }

    $grand_total += $total_admin;

    echo '<tr class="item-row">';
    echo '<td>Biaya Admin</td>';
    echo '<td>1</td>';
    echo '<td>Rp. ' . number_format($total_admin, 0, '', '.') . '</td>';
    echo '<td>Rp. ' . number_format($total_admin, 0, '', '.') . '</td>';
    echo '</tr>';
    ?>

    <tr>
      <td colspan="4" class="section-header">Tindakan</td>
    </tr>

    <?php
    foreach ($rows_tindakan as $row_action) {
        $grand_total += $row_action['SUB_TOTAL'];

        echo '<tr class="item-row">';
        echo '<td>' . htmlspecialchars($row_action['MEDI_NAME']) . '</td>';
        echo '<td>' . (int) $row_action['TRXA_TRET_QUTY'] . '</td>';
        echo '<td>Rp. ' . number_format($row_action['TRXA_MEDI_RATE'], 0, '', '.') . '</td>';
        echo '<td>Rp. ' . number_format($row_action['SUB_TOTAL'], 0, '', '.') . '</td>';
        echo '</tr>';
    }
    ?>

    <tr>
      <td colspan="4" class="section-header">BHP</td>
    </tr>

    <?php
    $stCsbl = $db->prepare("
        SELECT
            a.TRXA_STOCK_CODE,
            b.INVE_PART_NAME AS STOCK_NAME,
            a.TRXA_STOCK_PRIC AS STOCK_PRIC,
            a.TRXA_STOCK_QUTY
        FROM trxacsbl a
        LEFT JOIN invemast b ON a.TRXA_STOCK_CODE = b.INVE_MAST_CODE
        LEFT JOIN tblitype t ON b.INVE_MAIN_TYPE = t.TBLI_TYPE_CODE
        WHERE a.TRXA_CSBL_CODE = :regi
          AND t.TBLI_TYPE_CATE = 'FG'
          AND a.TRXA_CSBL_STAT = 'I'
          AND a.TRXA_VIEW_STAT = 'Y'
    ");
    $stCsbl->execute([':regi' => $regicode]);

    while ($row_csbl = $stCsbl->fetch(PDO::FETCH_ASSOC)) {
        $qty_csbl = (int) $row_csbl['TRXA_STOCK_QUTY'];
        $stockpric_bulat = pembulatan((int) round($row_csbl['STOCK_PRIC']));

        $tott = $stockpric_bulat * $qty_csbl;
        $totapric = pembulatan($tott);

        if ($tipe_pembayaran == 'B') {
            $stockpric_bulat = 0;
            $totapric = 0;
        }

        $grand_total += $totapric;

        echo '<tr class="item-row">';
        echo '<td class="text-left">' . htmlspecialchars($row_csbl['STOCK_NAME']) . '</td>';
        echo '<td class="text-center">' . $qty_csbl . '</td>';
        echo '<td class="text-right">Rp. ' . number_format($stockpric_bulat, 0, '', '.') . '</td>';
        echo '<td class="text-right">Rp. ' . number_format($totapric, 0, '', '.') . '</td>';
        echo '</tr>';
    }
    ?>

    <tr>
      <td colspan="4" class="section-header">Resep/Obat</td>
    </tr>

    <?php
    $stPrsc = $db->prepare("
        SELECT
            a.TRXA_STOCK_PRIC AS STOCK_PRIC,
            a.TRXA_STOCK_QUTY,
            a.TRXA_PRSC_CONC,
            a.TRXA_RACIK_ID,
            c.TRXA_REGI_PAYM AS PAYM_TYPE,
            b.INVE_PART_NAME AS STOCK_NAME
        FROM trxaprsc a
        LEFT JOIN invemast b ON a.TRXA_STOCK_CODE = b.INVE_MAST_CODE
        LEFT JOIN trxaregi c ON c.TRXA_REGI_CODE = a.TRXA_PRSC_CODE
                              AND c.TRXA_VIEW_STAT = 'Y'
        WHERE a.TRXA_PRSC_CODE = :regi
          AND a.TRXA_PRSC_STAT = 'I'
          AND a.TRXA_VIEW_STAT = 'Y'
        ORDER BY a.TRXA_RACIK_ID, a.TRXA_STOCK_CODE
    ");
    $stPrsc->execute([':regi' => $regicode]);

    $all_prsc_rows = $stPrsc->fetchAll(PDO::FETCH_ASSOC);

    $final_items = [];
    $racik_indices = [];
    $racik_ids = [];

    foreach ($all_prsc_rows as $row) {
        $qty_prsc = (int) $row['TRXA_STOCK_QUTY'];
        $raw_pric_prsc = $row['STOCK_PRIC'];

        $stockpric_bulat = pembulatan((int) round($raw_pric_prsc));
        if ($row['PAYM_TYPE'] === 'B') {
            $stockpric_bulat = 0;
        }

        $tott = $stockpric_bulat * $qty_prsc;
        $totapric = pembulatan($tott);
        if ($row['PAYM_TYPE'] === 'B') {
            $totapric = 0;
        }

        $is_racikan = ($row['TRXA_PRSC_CONC'] === 'Y' && !empty($row['TRXA_RACIK_ID']) && $row['TRXA_RACIK_ID'] > 0);

        if ($is_racikan) {
            $racik_id = $row['TRXA_RACIK_ID'];
            $racik_ids[$racik_id] = true;
            if (!isset($racik_indices[$racik_id])) {
                $final_items[] = [
                    'is_racikan' => true,
                    'racik_id' => $racik_id,
                    'name' => 'Obat',
                    'qty' => 1,
                    'total_price' => 0,
                    'paym_type' => $row['PAYM_TYPE']
                ];
                $racik_indices[$racik_id] = count($final_items) - 1;
            }
            $final_items[$racik_indices[$racik_id]]['total_price'] += $totapric;
        } else {
            $final_items[] = [
                'is_racikan' => false,
                'name' => $row['STOCK_NAME'],
                'qty' => $qty_prsc,
                'stock_pric' => $stockpric_bulat,
                'total_price' => $totapric,
                'paym_type' => $row['PAYM_TYPE']
            ];
        }
    }

    if (!empty($racik_ids)) {
        $placeholders = [];
        $params = [];
        $i = 0;
        foreach (array_keys($racik_ids) as $rid) {
            $key = ':r' . $i++;
            $placeholders[] = $key;
            $params[$key] = (int) $rid;
        }
        $inList = implode(',', $placeholders);
        $stHead = $db->prepare("SELECT TRXAR_ID, TRXAR_NAMA, TRXAR_QTY FROM trxaracik_head WHERE TRXAR_ID IN ($inList)");
        $stHead->execute($params);
        while ($h = $stHead->fetch(PDO::FETCH_ASSOC)) {
            $rid = (int) $h['TRXAR_ID'];
            if (isset($racik_indices[$rid])) {
                $idx = $racik_indices[$rid];
                $final_items[$idx]['name'] = !empty($h['TRXAR_NAMA']) ? $h['TRXAR_NAMA'] : 'Obat';
                $final_items[$idx]['qty'] = isset($h['TRXAR_QTY']) ? (int) $h['TRXAR_QTY'] : 1;
            }
        }
    }

    foreach ($final_items as &$f_item) {
        if ($f_item['is_racikan']) {
            if ($f_item['paym_type'] === 'B') {
                $f_item['total_price'] = 0;
            } else {
                $f_item['total_price'] += 30000;
            }
            $f_item['total_price'] = pembulatan($f_item['total_price']);
            $f_item['stock_pric'] = pembulatan((int) round($f_item['total_price'] / max(1, $f_item['qty'])));
        }
    }
    unset($f_item);

    foreach ($final_items as $item) {
        $grand_total += $item['total_price'];

        echo '<tr class="item-row">';
        echo '<td>' . htmlspecialchars($item['name']) . '</td>';
        echo '<td>' . (int) $item['qty'] . '</td>';
        echo '<td>Rp. ' . number_format($item['stock_pric'], 0, '', '.') . '</td>';
        echo '<td>Rp. ' . number_format($item['total_price'], 0, '', '.') . '</td>';
        echo '</tr>';
    }
    ?>

    <tr class="total-row">
      <td colspan="3" class="text-right font-bold">Sub Total:</td>
      <td class="text-right font-bold">Rp.
        <?php echo number_format($grand_total, 0, '', '.'); ?>
      </td>
    </tr>
    <tr class="total-row">
      <td colspan="3" class="text-right font-bold">Discount:</td>
      <td class="text-right font-bold">Rp.
        <?php echo number_format($discount, 0, '', '.'); ?>
      </td>
    </tr>
    <tr class="total-row total-final">
      <td colspan="3" class="text-right font-bold">Total:</td>
      <td class="text-right font-bold">Rp.
        <?php echo number_format($grand_total - $discount, 0, '', '.'); ?>
      </td>
    </tr>

  </tbody>
</table>
