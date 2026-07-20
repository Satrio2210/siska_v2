<?php
error_reporting(E_ALL ^ E_DEPRECATED);
error_reporting(E_ALL & ~E_NOTICE);

include "conf/config.php";
include "inc/sanie.php";

$prsccode = $_POST['q'];
$xhead = "SELECT
         r.TRXA_REGI_CODE,
         r.TRXA_REGI_LIST,
         r.TRXA_REGI_PAYM,
         pm.PATI_MAIN_TITL,
         pm.PATI_MAIN_NAME,
         d.PASS_USER_NAME
         FROM trxaregi r
         LEFT JOIN patimast pm
         ON pm.PATI_MAST_CODE=r.TRXA_PATI_CODE
         LEFT JOIN passiden d
         ON d.PASS_USER_IDEN=r.TRXA_REGI_DOCT
         WHERE r.TRXA_REGI_CODE='$prsccode'
         ";

$qhead = $db->query($xhead) or die($db->errorInfo()[2]);
$row = $qhead->fetch(PDO::FETCH_ASSOC);

$xdetail = "SELECT
            p.TRXA_STOCK_CODE,
            p.TRXA_STOCK_BTCH,
            p.TRXA_PRSC_CONC,
            p.TRXA_RACIK_ID,
            (SELECT TRXAR_NAMA FROM trxaracik_head WHERE TRXAR_ID = p.TRXA_RACIK_ID) AS RACIK_NAMA,
            i.INVE_PART_NAME,
            s.TBLI_SPEC_NAME,
            u.TBLI_UNIT_NAME,
            p.TRXA_STOCK_QUTY,
            IFNULL(p.TRXA_STOCK_BTCH,'Belum Diisi') AS BTCH,
            CASE
            WHEN p.TRXA_PRSC_CONC='Y'
            THEN 'Racikan'
            ELSE 'Non Racikan'
            END AS JENIS
            FROM trxaprsc p
            LEFT JOIN invemast i
            ON i.INVE_MAST_CODE=p.TRXA_STOCK_CODE
            LEFT JOIN tblispec s
            ON s.TBLI_SPEC_CODE=i.INVE_MAIN_SPEC
            LEFT JOIN tbliunit u
            ON u.TBLI_UNIT_CODE=i.INVE_SALE_UNIT
            WHERE
            p.TRXA_PRSC_CODE='$prsccode'
            AND p.TRXA_VIEW_STAT='Y'
            ";

$qdetail = $db->query($xdetail) or die($db->errorInfo()[2]);
?> <!-- Tutup tag PHP di sini, mulai HTML murni -->

<style>
  .detail-header {
    font-size: 13px;
  }

  .table-container {
    overflow: auto;
    border-radius: 16px;
    max-height: 420px;
    border: 1px solid #e2e8f0;
    margin-top: 10px;
  }

  #tbldetail-farm-receipt {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
  }

  #tbldetail-farm-receipt th,
  #tbldetail-farm-receipt td {
    padding: 10px 20px;
  }

  #tbldetail-farm-receipt thead {
    background: #10b981;
    color: white;
    position: sticky;
    top: 0;
    z-index: 10;
  }

  #tbldetail-farm-receipt th {
    font-size: 13px;
    font-weight: 700;
    text-align: center;
    vertical-align: middle;
    border: none;
    letter-spacing: 0.3px;
  }

  #tbldetail-farm-receipt td {
    font-size: 12px;
    /* color: #374151; */
    text-align: center;
    vertical-align: middle;
    border-bottom: 1px solid #e5e7eb;
    overflow-wrap: break-word;
    word-break: break-word;
  }

  #tbldetail-farm-receipt th:nth-child(1),
  #tbldetail-farm-receipt td:nth-child(1) {
    width: 20%;
  }

  #tbldetail-farm-receipt th:nth-child(2),
  #tbldetail-farm-receipt td:nth-child(2) {
    width: 20%;
  }

  #tbldetail-farm-receipt th:nth-child(3),
  #tbldetail-farm-receipt td:nth-child(3) {
    width: 20%;
  }

  #tbldetail-farm-receipt th:nth-child(4),
  #tbldetail-farm-receipt td:nth-child(4) {
    width: 20%;
  }

  #tbldetail-farm-receipt th:nth-child(5),
  #tbldetail-farm-receipt td:nth-child(5) {
    width: 20%;
  }
</style>

<div class="card-modern">
  <div class="card-title">Detail Resep</div>

  <table class="detail-header">
    <tr>
      <td><b>No Antrian</b></td>
      <td>: <?= htmlspecialchars($row['TRXA_REGI_LIST']) ?></td>
    </tr>
    <tr>
      <td><b>Pasien</b></td>
      <td>: <?= htmlspecialchars($row['PATI_MAIN_TITL'] . ' ' . $row['PATI_MAIN_NAME']) ?></td>
    </tr>
    <tr>
      <td><b>Dokter</b></td>
      <td>: <?= htmlspecialchars($row['PASS_USER_NAME']) ?></td>
    </tr>
    <tr>
      <td><b>Pembayaran</b></td>
      <td>: <?= ($row['TRXA_REGI_PAYM'] == 'B' ? 'BPJS' : 'UMUM') ?></td>
    </tr>
  </table>

  <div class="table-container">
    <table id="tbldetail-farm-receipt">
      <thead>
        <tr>
          <th>Obat</th>
          <th>Qty</th>
          <th>Batch</th>
          <th>Jenis</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>

        <?php while ($d = $qdetail->fetch(PDO::FETCH_ASSOC)): ?>
          <?php
          $jenis = $d['JENIS'];
          if ($d['TRXA_PRSC_CONC'] == 'Y' && !empty($d['RACIK_NAMA'])) {
            $jenis .= ' (' . $d['RACIK_NAMA'] . ')';
          }
          ?>

          <tr>
            <td><?= htmlspecialchars($d['INVE_PART_NAME'] . ' ' . $d['TBLI_SPEC_NAME']) ?></td>
            <td><?= htmlspecialchars($d['TRXA_STOCK_QUTY'] . ' ' . $d['TBLI_UNIT_NAME']) ?></td>
            <td><?= htmlspecialchars($d['BTCH']) ?></td>
            <td><?= htmlspecialchars($jenis) ?></td>
            <td>
              <div class="form-grid"> <!-- Wrapper div untuk tombol -->
                <button type="button" class="btn-modern btn-edit" style="height: 38px;"
                  onclick="editobat('<?= htmlspecialchars($prsccode) ?>', '<?= htmlspecialchars($d['TRXA_STOCK_CODE']) ?>')">
                  Edit
                </button>
                <button type="button" class="btn-modern btn-delete" style="height: 38px;"
                  onclick="hapusobat('<?= htmlspecialchars($prsccode) ?>', '<?= htmlspecialchars($d['TRXA_STOCK_CODE']) ?>')">
                  Hapus
                </button>
              </div>
            </td>
          </tr>

        <?php endwhile; ?>

      </tbody>
    </table>
  </div>

  <div style="margin-top:20px; text-align:right; font-size: 14px;">
    <button class="btn-modern btn-save" type="button" onclick="siapkansemua('<?= htmlspecialchars($prsccode) ?>')">
      Siapkan Resep
    </button>
  </div>
</div>