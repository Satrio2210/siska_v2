<?php
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE);
include "conf/config.php";
include "inc/sanie.php";

$regicode = $_POST['q'];

// Variabel penampung total keseluruhan
$grand_total = 0;
$discount = 0; // Jika ada variabel diskon dari DB, bisa dimasukkan ke sini

// 1. Refactor: Data Pasien menggunakan LEFT JOIN
$query_regi = "SELECT 
        r.TRXA_PATI_CODE, 
        r.TRXA_REGI_STAT, 
        CONCAT(p.PATI_MAIN_TITL, ' ', p.PATI_MAIN_NAME) AS PATI_NAME,
        r.TRXA_REGI_FEE, 
        r.TRXA_REGI_PAYM
    FROM trxaregi r
    LEFT JOIN patimast p ON p.PATI_MAST_CODE = r.TRXA_PATI_CODE
    WHERE r.TRXA_REGI_CODE = '$regicode' 
    AND r.TRXA_VIEW_STAT = 'Y'
";

$qregi = $db->query($query_regi) or die("Gagal Ambil data Pasien!!");
$row_regi = $qregi->fetch(PDO::FETCH_ASSOC);

$patiname = $row_regi['PATI_NAME'];
$tipe_pembayaran = $row_regi['TRXA_REGI_PAYM'];
$fee_admin_aktif = ($row_regi && $row_regi['TRXA_REGI_FEE'] == 'Y') ? true : false;

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

    <!-- ================= BAGIAN LAYANAN ================= -->
    <tr>
      <td colspan="4" class="section-header">Layanan</td>
    </tr>

    <?php
    // Refactor: Layanan menggunakan LEFT JOIN
    $query_tret = "
            SELECT 
                a.TRXA_TRET_CODE, 
                a.TRXA_MEDI_CODE, 
                b.TBLF_MEDI_NAME AS MEDI_NAME, 
                a.TRXA_MEDI_RATE, 
                a.TRXA_TRET_QUTY, 
                (a.TRXA_MEDI_RATE * a.TRXA_TRET_QUTY) AS SUB_TOTAL
            FROM trxatret a
            LEFT JOIN tblfmedi b ON a.TRXA_MEDI_CODE = b.TBLF_MEDI_CODE
            WHERE b.TBLF_MEDI_TYPE = 'J' 
            AND a.TRXA_TRET_CODE = '$regicode' 
            AND a.TRXA_TRET_STAT = 'I' 
            AND a.TRXA_VIEW_STAT = 'Y'
        ";

    $qtret = $db->query($query_tret) or die("Gagal Ambil data tindakan!!");
    while ($row_tret = $qtret->fetch(PDO::FETCH_ASSOC)) {
      $grand_total += $row_tret['SUB_TOTAL']; // Tambah ke Grand Total
    
      echo '<tr class="item-row">';
      echo '<td>' . $row_tret['MEDI_NAME'] . '</td>';
      echo '<td>' . $row_tret['TRXA_TRET_QUTY'] . '</td>';
      echo '<td>Rp. ' . number_format($row_tret['TRXA_MEDI_RATE'], 0, '', '.') . '</td>';
      echo '<td>Rp. ' . number_format($row_tret['SUB_TOTAL'], 0, '', '.') . '</td>';
      echo '</tr>';
    }
    ?>

    <!-- ================= BAGIAN BIAYA ADMIN ================= -->
    <?php
    // Periksa ketersediaan racikan
    $periksaracikan = "SELECT COUNT(*) FROM trxaprsc WHERE TRXA_PRSC_CODE='$regicode' AND TRXA_PRSC_CONC='Y' AND TRXA_PRSC_STAT='I' AND TRXA_VIEW_STAT='Y'";
    $ketersediaan_racikan = $db->query($periksaracikan)->fetchColumn();

    if ($ketersediaan_racikan == 0) {
      $periksaresep = "SELECT COUNT(*) FROM trxaprsc WHERE TRXA_PRSC_CODE='$regicode' AND TRXA_PRSC_STAT='I' AND TRXA_VIEW_STAT='Y'";
      $ketersediaan_resep = $db->query($periksaresep)->fetchColumn();

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

    $grand_total += $total_admin; // Tambah ke Grand Total
    
    echo '<tr class="item-row">';
    echo '<td>Biaya Admin</td>';
    echo '<td>1</td>';
    echo '<td>Rp. ' . number_format($total_admin, 0, '', '.') . '</td>';
    echo '<td>Rp. ' . number_format($total_admin, 0, '', '.') . '</td>';
    echo '</tr>';
    ?>

    <!-- ================= BAGIAN TINDAKAN ================= -->
    <tr>
      <td colspan="4" class="section-header">Tindakan</td>
    </tr>
    <?php
    // Refactor: Tindakan menggunakan LEFT JOIN
    $query_action = "
            SELECT 
                a.TRXA_TRET_CODE, 
                a.TRXA_MEDI_CODE, 
                b.TBLF_MEDI_NAME AS MEDI_NAME, 
                a.TRXA_MEDI_RATE, 
                a.TRXA_TRET_QUTY, 
                (a.TRXA_MEDI_RATE * a.TRXA_TRET_QUTY) AS SUB_TOTAL
            FROM trxatret a
            LEFT JOIN tblfmedi b ON a.TRXA_MEDI_CODE = b.TBLF_MEDI_CODE
            WHERE b.TBLF_MEDI_TYPE IN ('O','N')  
            AND a.TRXA_TRET_CODE = '$regicode' 
            AND a.TRXA_TRET_STAT = 'I' 
            AND a.TRXA_VIEW_STAT = 'Y'
        ";

    $qaction = $db->query($query_action) or die("Gagal Ambil data tindakan!!");
    while ($row_action = $qaction->fetch(PDO::FETCH_ASSOC)) {
      $grand_total += $row_action['SUB_TOTAL']; // Tambah ke Grand Total
    
      echo '<tr class="item-row">';
      echo '<td>' . $row_action['MEDI_NAME'] . '</td>';
      echo '<td>' . $row_action['TRXA_TRET_QUTY'] . '</td>';
      echo '<td>Rp. ' . number_format($row_action['TRXA_MEDI_RATE'], 0, '', '.') . '</td>';
      echo '<td>Rp. ' . number_format($row_action['SUB_TOTAL'], 0, '', '.') . '</td>';
      echo '</tr>';
    }
    ?>

    <!-- ================= BAGIAN BHP ================= -->
    <tr>
      <td colspan="4" class="section-header">BHP</td>
    </tr>
    <?php
    // Refactor: BHP menggunakan LEFT JOIN
    $query_csbl = "
            SELECT 
                a.TRXA_CSBL_CODE, 
                a.TRXA_STOCK_CODE, 
                b.INVE_PART_NAME AS STOCK_NAME, 
                a.TRXA_STOCK_PRIC AS STOCK_PRIC, 
                a.TRXA_STOCK_QUTY
            FROM trxacsbl a
            LEFT JOIN invemast b ON a.TRXA_STOCK_CODE = b.INVE_MAST_CODE
            LEFT JOIN tblitype t ON b.INVE_MAIN_TYPE = t.TBLI_TYPE_CODE
            WHERE a.TRXA_CSBL_CODE = '$regicode'
            AND t.TBLI_TYPE_CATE = 'FG' 
            AND a.TRXA_CSBL_STAT = 'I' 
            AND a.TRXA_VIEW_STAT = 'Y'
        ";

    $qcsbl = $db->query($query_csbl) or die("Gagal Ambil data obat!!");
    while ($row_csbl = $qcsbl->fetch(PDO::FETCH_ASSOC)) {
      $qty_csbl = $row_csbl['TRXA_STOCK_QUTY'];
      $stockpric_bulat = pembulatan((int) round($row_csbl['STOCK_PRIC']));

      $tott = $stockpric_bulat * $qty_csbl;
      $totapric = pembulatan($tott);

      $grand_total += $totapric; // Tambah ke Grand Total
    
      echo '<tr class="item-row">';
      echo '<td>' . $row_csbl['STOCK_NAME'] . '</td>';
      echo '<td>' . $qty_csbl . '</td>';
      echo '<td>Rp. ' . number_format($stockpric_bulat, 0, '', '.') . '</td>';
      echo '<td>Rp. ' . number_format($totapric, 0, '', '.') . '</td>';
      echo '</tr>';
    }
    ?>

    <!-- ================= BAGIAN RESEP/OBAT ================= -->
    <tr>
      <td colspan="4" class="section-header">Resep/Obat</td>
    </tr>

    <?php
    // Refactor: Obat menggunakan LEFT JOIN
    $query_prsc = "
            SELECT 
                a.TRXA_PRSC_CODE, 
                a.TRXA_STOCK_CODE, 
                b.INVE_PART_NAME AS STOCK_NAME, 
                a.TRXA_STOCK_PRIC AS STOCK_PRIC, 
                a.TRXA_STOCK_QUTY, 
                c.TRXA_REGI_PAYM AS PAYM_TYPE,
                a.TRXA_PRSC_CONC, 
                a.TRXA_RACIK_ID
            FROM trxaprsc a
            LEFT JOIN invemast b ON a.TRXA_STOCK_CODE = b.INVE_MAST_CODE
            LEFT JOIN trxaregi c ON a.TRXA_PRSC_CODE = c.TRXA_REGI_CODE
            WHERE a.TRXA_PRSC_CODE = '$regicode' 
            AND a.TRXA_PRSC_STAT = 'I' 
            AND a.TRXA_VIEW_STAT = 'Y'
        ";

    $qprsc = $db->query($query_prsc) or die("Gagal Ambil data obat!!");
    $all_prsc_rows = [];
    while ($row_prsc = $qprsc->fetch(PDO::FETCH_ASSOC)) {
      $all_prsc_rows[] = $row_prsc;
    }

    $final_items = [];
    $racik_indices = [];

    foreach ($all_prsc_rows as $row) {
      $qty_prsc = $row['TRXA_STOCK_QUTY'];
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
        if (!isset($racik_indices[$racik_id])) {
          $qhead = $db->query("SELECT TRXAR_NAMA, TRXAR_QTY FROM trxaracik_head WHERE TRXAR_ID = " . (int) $racik_id . " LIMIT 1");
          $head_row = $qhead ? $qhead->fetch(PDO::FETCH_ASSOC) : null;

          $racik_nama = ($head_row && !empty($head_row['TRXAR_NAMA'])) ? $head_row['TRXAR_NAMA'] : 'Obat';
          $racik_qty = ($head_row && isset($head_row['TRXAR_QTY'])) ? $head_row['TRXAR_QTY'] : 1;

          $final_items[] = [
            'is_racikan' => true,
            'racik_id' => $racik_id,
            'name' => $racik_nama . ' (Racikan)',
            'qty' => $racik_qty,
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

    foreach ($final_items as &$f_item) {
      if ($f_item['is_racikan']) {
        if ($f_item['paym_type'] === 'B') {
          $f_item['total_price'] = 0;
        } else {
          $f_item['total_price'] += 30000;
        }
        $f_item['total_price'] = pembulatan($f_item['total_price']);
        $f_item['stock_pric'] = pembulatan((int) round($f_item['total_price'] / $f_item['qty']));
      }
    }
    unset($f_item);

    foreach ($final_items as $item) {
      $grand_total += $item['total_price']; // Tambah ke Grand Total
    
      echo '<tr class="item-row">';
      echo '<td>' . htmlspecialchars($item['name']) . '</td>';
      echo '<td>' . htmlspecialchars($item['qty']) . '</td>';
      echo '<td>Rp. ' . number_format($item['stock_pric'], 0, '', '.') . '</td>';
      echo '<td>Rp. ' . number_format($item['total_price'], 0, '', '.') . '</td>';
      echo '</tr>';
    }
    ?>

    <!-- ================= BAGIAN FOOTER (TOTAL) ================= -->
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