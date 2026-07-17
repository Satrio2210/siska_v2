<?php
session_start();
include "conf/config.php";
include "inc/sanie.php";

if (isset($_POST['q']) && ($_POST['q'] != '')) {
    $rawdata = xss_clean($_POST['q']);
    list($regicode, $paticode) = explode("|", $rawdata);

    $view = "SELECT 
                t.TRXA_REGI_CODE, 
                t.TRXA_PATI_CODE, 
                t.TRXA_REGI_DATE, 
                t.TRXA_ENTR_DATE,
                t.TRXA_REGI_PAYM,
                t.TRXA_REGI_DOCT,
                t.TRXA_REGI_POLI,
                p.PATI_MAIN_NAME,
                p.PATI_MAIN_GEND,
                p.PATI_MAIN_BIRT,
                u.PASS_USER_NAME AS REGI_DOCT_NAME,
                pl.TBLA_POLI_NAME AS REGI_POLI_NAME
            FROM trxaregi t
            LEFT JOIN patimast p ON p.PATI_MAST_CODE = t.TRXA_PATI_CODE
            LEFT JOIN passiden u ON u.PASS_USER_IDEN = t.TRXA_REGI_DOCT
            LEFT JOIN tblapoli pl ON pl.TBLA_POLI_CODE = t.TRXA_REGI_POLI
            WHERE t.TRXA_REGI_CODE = '$regicode' 
              AND t.TRXA_PATI_CODE = '$paticode' 
              AND t.TRXA_VIEW_STAT = 'Y'";

    $qview = $db->query($view) or die("Gagal Ambil Data Admisi!!");
    $rview = $qview->fetch(PDO::FETCH_ASSOC);

    $regicode = "$rview[TRXA_REGI_CODE]";
    $paticode = "$rview[TRXA_PATI_CODE]";
    $regidoct = "$rview[TRXA_REGI_DOCT]";
    $regipoli = "$rview[TRXA_REGI_POLI]";
    $regipaym = "$rview[TRXA_REGI_PAYM]";

    $regidate = date("d-m-Y", strtotime($rview['TRXA_ENTR_DATE']));
    $patiname = "$rview[PATI_MAIN_NAME]";
    $maingend = "$rview[PATI_MAIN_GEND]";
    $mainbirt = date("d-m-Y", strtotime($rview['PATI_MAIN_BIRT']));
    $regidoctname = "$rview[REGI_DOCT_NAME]";
    $regipoliname = "$rview[REGI_POLI_NAME]";

    $xregipaym = "$rview[TRXA_REGI_PAYM]";
    if ($xregipaym == 'U') {
        $regipaym_name = 'Umum';
    } else if ($xregipaym == 'B') {
        $regipaym_name = 'BPJS';
    } else if ($xregipaym == 'A') {
        $regipaym_name = 'Asuransi';
    } else if ($xregipaym == 'P') {
        $regipaym_name = 'Perusahaan';
    } else if ($xregipaym == 'H') {
        $regipaym_name = 'Halodoc';
    } else {
        $regipaym_name = 'Kosong';
    }

    $maingend_name = '';
    if ($maingend == 'M') {
        $maingend_name = 'Laki-laki';
    } else if ($maingend == 'F') {
        $maingend_name = 'Perempuan';
    } else {
        $maingend_name = 'No gender';
    }

    // Total layanan (J) + tindakan (O/N) yang masih belumbayar
    $query_layanan = "SELECT COALESCE(SUM(a.TRXA_MEDI_RATE * a.TRXA_TRET_QUTY), 0) AS TOTA
                      FROM trxatret a
                      LEFT JOIN tblfmedi b ON a.TRXA_MEDI_CODE = b.TBLF_MEDI_CODE
                      WHERE a.TRXA_TRET_CODE = '$regicode'
                        AND b.TBLF_MEDI_TYPE = 'J'
                        AND a.TRXA_TRET_STAT = 'I'
                        AND a.TRXA_VIEW_STAT = 'Y'";
    $total_layanan = (float)$db->query($query_layanan)->fetchColumn();

    $query_tindakan = "SELECT COALESCE(SUM(a.TRXA_MEDI_RATE * a.TRXA_TRET_QUTY), 0) AS TOTA
                       FROM trxatret a
                       LEFT JOIN tblfmedi b ON a.TRXA_MEDI_CODE = b.TBLF_MEDI_CODE
                       WHERE a.TRXA_TRET_CODE = '$regicode'
                         AND b.TBLF_MEDI_TYPE IN ('O','N')
                         AND a.TRXA_TRET_STAT = 'I'
                         AND a.TRXA_VIEW_STAT = 'Y'";
    $total_tindakan = (float)$db->query($query_tindakan)->fetchColumn();

    // Semua tret (untuk non-BPJS)
    $query_tret = "SELECT COALESCE(SUM(TRXA_MEDI_RATE * TRXA_TRET_QUTY), 0) AS TOTA_TRET 
                    FROM trxatret 
                    WHERE TRXA_TRET_CODE = '$regicode'
                    AND TRXA_TRET_STAT = 'I'
                    AND TRXA_VIEW_STAT = 'Y'";
    $total_tret = (float)$db->query($query_tret)->fetchColumn();

    $query_csbl = "SELECT COALESCE(SUM(TRXA_STOCK_PRIC * TRXA_STOCK_QUTY), 0) AS TOTA_CSBL 
                   FROM trxacsbl 
                   WHERE TRXA_CSBL_CODE = '$regicode'
                   AND TRXA_CSBL_STAT = 'I'
                   AND TRXA_VIEW_STAT = 'Y'";
    $total_csbl = (float)$db->query($query_csbl)->fetchColumn();

    $query_prsc = "SELECT TRXA_STOCK_PRIC, TRXA_STOCK_QUTY, TRXA_PRSC_CONC, TRXA_RACIK_ID,
                  (SELECT TRXA_REGI_PAYM FROM trxaregi WHERE TRXA_REGI_CODE=TRXA_PRSC_CODE) AS PAYM_TYPE
                   FROM trxaprsc 
                   WHERE TRXA_PRSC_CODE = '$regicode'
                   AND TRXA_PRSC_STAT = 'I'
                   AND TRXA_VIEW_STAT = 'Y'";
    $qprsc = $db->query($query_prsc) or die("Gagal Ambil data Resep");

    $total_prsc = 0;
    $racik_totals = [];
    $paym_type = $regipaym;

    while ($rprsc = $qprsc->fetch(PDO::FETCH_ASSOC)) {
        if ($paym_type === null || $paym_type === '') {
            $paym_type = $rprsc['PAYM_TYPE'];
        }

        $stockpric_bulat = pembulatan((int) round($rprsc['TRXA_STOCK_PRIC']));
        // BPJS: obat tidak dihitung
        if ($regipaym === 'B' || $paym_type === 'B') {
            $stockpric_bulat = 0;
        }

        $tott = $stockpric_bulat * $rprsc['TRXA_STOCK_QUTY'];
        $totapric = pembulatan($tott);
        if ($regipaym === 'B' || $paym_type === 'B') {
            $totapric = 0;
        }

        $is_racikan = ($rprsc['TRXA_PRSC_CONC'] === 'Y' && !empty($rprsc['TRXA_RACIK_ID']) && $rprsc['TRXA_RACIK_ID'] > 0);
        if ($is_racikan) {
            $rid = $rprsc['TRXA_RACIK_ID'];
            if (!isset($racik_totals[$rid])) {
                $racik_totals[$rid] = 0;
            }
            $racik_totals[$rid] += $totapric;
        } else {
            $total_prsc += $totapric;
        }
    }

    foreach ($racik_totals as $rid => $comp_total) {
        if ($regipaym === 'B' || $paym_type === 'B') {
            $total_prsc += 0;
        } else {
            $total_prsc += pembulatan($comp_total + 30000);
        }
    }

    // BPJS: BHP tidak dihitung
    if ($regipaym === 'B') {
        $total_csbl = 0;
    }

    $query_outs = "SELECT COALESCE(SUM(TRXA_PAYM_AMNT), 0) AS TOTA_PAYM 
                   FROM trxasale WHERE TRXA_REGI_CODE = '$regicode'
                   AND TRXA_VIEW_STAT = 'Y'";
    $total_outs = (float)$db->query($query_outs)->fetchColumn();

    $q_regi = "SELECT TRXA_REGI_FEE, TRXA_REGI_PAYM FROM trxaregi WHERE TRXA_REGI_CODE='$regicode' LIMIT 1";
    $data_regi = $db->query($q_regi)->fetch(PDO::FETCH_ASSOC);

    $fee_admin_aktif = ($data_regi && $data_regi['TRXA_REGI_FEE'] == 'Y') ? true : false;
    $tipe_pembayaran = $data_regi ? $data_regi['TRXA_REGI_PAYM'] : '';

    $periksaracikan = "SELECT COUNT(*) FROM trxaprsc WHERE TRXA_PRSC_CODE='$regicode' 
                 AND TRXA_PRSC_CONC='Y'
                 AND TRXA_PRSC_STAT='I'
                 AND TRXA_VIEW_STAT='Y'";
    $ketersediaan_racikan = $db->query($periksaracikan)->fetchColumn();

    if ($ketersediaan_racikan == 0) {
        $periksaresep = "SELECT COUNT(*) FROM trxaprsc WHERE TRXA_PRSC_CODE='$regicode'
                 AND TRXA_PRSC_STAT='I'
                 AND TRXA_VIEW_STAT='Y'";
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

    // BPJS: hanya layanan + tindakan (nilai > 0) yang boleh ditagih
    if ($regipaym === 'B') {
        $billable_extra = $total_layanan + $total_tindakan;
        $xpaymtota = $billable_extra - $total_outs;
        // mode: CLOSE jika tidak ada tambahan berbayar, else BAYAR
        $sale_mode = ($billable_extra > 0) ? 'BAYAR' : 'CLOSE';
    } else {
        $subtotal1 = ($total_tret + $total_csbl);
        $subtotal2 = ($total_prsc + $subtotal1);
        $subtotal3 = ($subtotal2 + $total_admin);
        $xpaymtota = ($subtotal3 - $total_outs);
        $sale_mode = 'BAYAR';
    }

    $xharga = round($xpaymtota);
    $int = (int) $xharga;
    if ($int < 0) {
        $int = 0;
    }
    $paymtota = pembulatan($int);
    $viewpaymtota = number_format($paymtota, 0, '', '.');

    // pipe: 1-14 existing + 15 sale_mode
    echo "|$regicode|$paticode|$regidoct|$regipoli|$regipaym|$paymtota|$viewpaymtota|$regidate|$patiname|$maingend_name|$mainbirt|$regidoctname|$regipoliname|$regipaym_name|$sale_mode|";
}
?>