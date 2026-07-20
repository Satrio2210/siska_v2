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
                t.TRXA_REGI_FEE,
                t.TRXA_REGI_STAT,
                p.PATI_MAIN_NAME,
                p.PATI_MAIN_GEND,
                p.PATI_MAIN_BIRT,
                u.PASS_USER_NAME AS REGI_DOCT_NAME,
                pl.TBLA_POLI_NAME AS REGI_POLI_NAME
            FROM trxaregi t
            LEFT JOIN patimast p ON p.PATI_MAST_CODE = t.TRXA_PATI_CODE
            LEFT JOIN passiden u ON u.PASS_USER_IDEN = t.TRXA_REGI_DOCT
            LEFT JOIN tblapoli pl ON pl.TBLA_POLI_CODE = t.TRXA_REGI_POLI
            WHERE t.TRXA_REGI_CODE = :regi
              AND t.TRXA_PATI_CODE = :pati
              AND t.TRXA_VIEW_STAT = 'Y'";

    $stview = $db->prepare($view);
    $stview->execute([':regi' => $regicode, ':pati' => $paticode]);
    $rview = $stview->fetch(PDO::FETCH_ASSOC);

    if (!$rview) {
        echo "|0||||||0||0|||||||||";
        exit;
    }

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

    $stSum = $db->prepare("
        SELECT
            COALESCE(SUM(CASE WHEN b.TBLF_MEDI_TYPE = 'J'  THEN a.TRXA_MEDI_RATE * a.TRXA_TRET_QUTY ELSE 0 END), 0) AS TOTAL_LAYANAN,
            COALESCE(SUM(CASE WHEN b.TBLF_MEDI_TYPE IN ('O','N') THEN a.TRXA_MEDI_RATE * a.TRXA_TRET_QUTY ELSE 0 END), 0) AS TOTAL_TINDAKAN,
            COALESCE(SUM(a.TRXA_MEDI_RATE * a.TRXA_TRET_QUTY), 0) AS TOTAL_TRET
        FROM trxatret a
        LEFT JOIN tblfmedi b ON a.TRXA_MEDI_CODE = b.TBLF_MEDI_CODE
        WHERE a.TRXA_TRET_CODE = :regi
          AND a.TRXA_TRET_STAT = 'I'
          AND a.TRXA_VIEW_STAT = 'Y'
    ");
    $stSum->execute([':regi' => $regicode]);
    $rowSum = $stSum->fetch(PDO::FETCH_ASSOC);

    $total_layanan = (float) ($rowSum['TOTAL_LAYANAN'] ?? 0);
    $total_tindakan = (float) ($rowSum['TOTAL_TINDAKAN'] ?? 0);
    $total_tret = (float) ($rowSum['TOTAL_TRET'] ?? 0);

    $stCsbl = $db->prepare("
        SELECT COALESCE(SUM(TRXA_STOCK_PRIC * TRXA_STOCK_QUTY), 0) AS TOTA_CSBL
        FROM trxacsbl
        WHERE TRXA_CSBL_CODE = :regi
          AND TRXA_CSBL_STAT = 'I'
          AND TRXA_VIEW_STAT = 'Y'
    ");
    $stCsbl->execute([':regi' => $regicode]);
    $total_csbl = (float) $stCsbl->fetchColumn();

    $stPrsc = $db->prepare("
        SELECT
            a.TRXA_STOCK_PRIC,
            a.TRXA_STOCK_QUTY,
            a.TRXA_PRSC_CONC,
            a.TRXA_RACIK_ID,
            c.TRXA_REGI_PAYM AS PAYM_TYPE
        FROM trxaprsc a
        LEFT JOIN trxaregi c ON c.TRXA_REGI_CODE = a.TRXA_PRSC_CODE
                              AND c.TRXA_VIEW_STAT = 'Y'
        WHERE a.TRXA_PRSC_CODE = :regi
          AND a.TRXA_PRSC_STAT = 'I'
          AND a.TRXA_VIEW_STAT = 'Y'
    ");
    $stPrsc->execute([':regi' => $regicode]);

    $total_prsc = 0;
    $racik_totals = [];
    $paym_type = $regipaym;
    $racik_ids = [];

    while ($rprsc = $stPrsc->fetch(PDO::FETCH_ASSOC)) {
        if (($paym_type === null || $paym_type === '') && !empty($rprsc['PAYM_TYPE'])) {
            $paym_type = $rprsc['PAYM_TYPE'];
        }

        $is_bpjs = ($regipaym === 'B' || ($paym_type !== null && $paym_type === 'B'));
        $stockpric_bulat = $is_bpjs ? 0 : pembulatan((int) round($rprsc['TRXA_STOCK_PRIC']));

        $tott = $stockpric_bulat * $rprsc['TRXA_STOCK_QUTY'];
        $totapric = $is_bpjs ? 0 : pembulatan($tott);

        $is_racikan = ($rprsc['TRXA_PRSC_CONC'] === 'Y' && !empty($rprsc['TRXA_RACIK_ID']) && $rprsc['TRXA_RACIK_ID'] > 0);
        if ($is_racikan) {
            $rid = $rprsc['TRXA_RACIK_ID'];
            $racik_ids[$rid] = true;
            if (!isset($racik_totals[$rid])) {
                $racik_totals[$rid] = 0;
            }
            $racik_totals[$rid] += $totapric;
        } else {
            $total_prsc += $totapric;
        }
    }

    foreach (array_keys($racik_totals) as $rid) {
        $is_bpjs = ($regipaym === 'B' || ($paym_type !== null && $paym_type === 'B'));
        if ($is_bpjs) {
            $total_prsc += 0;
        } else {
            $total_prsc += pembulatan($racik_totals[$rid] + 30000);
        }
    }

    if ($regipaym === 'B') {
        $total_csbl = 0;
    }

    $stOuts = $db->prepare("
        SELECT COALESCE(SUM(TRXA_PAYM_AMNT), 0)
        FROM trxasale
        WHERE TRXA_REGI_CODE = :regi
          AND TRXA_VIEW_STAT = 'Y'
    ");
    $stOuts->execute([':regi' => $regicode]);
    $total_outs = (float) $stOuts->fetchColumn();

    $fee_admin_aktif = ($rview['TRXA_REGI_FEE'] == 'Y');
    $tipe_pembayaran = $rview['TRXA_REGI_PAYM'];

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

    if ($regipaym === 'B') {
        $billable_extra = $total_layanan + $total_tindakan;
        $xpaymtota = $billable_extra - $total_outs;
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

    echo "|$regicode|$paticode|$regidoct|$regipoli|$regipaym|$paymtota|$viewpaymtota|$regidate|$patiname|$maingend_name|$mainbirt|$regidoctname|$regipoliname|$regipaym_name|$sale_mode|";
}
