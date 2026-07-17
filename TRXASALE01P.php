<?php
include 'conf/config.php';
include 'inc/sanie.php';

if (isset($_POST['hidregicode'])) {
	$regicode = $_POST['hidregicode'];

	$periksaregicode = "SELECT COUNT(*) FROM trxasale WHERE TRXA_REGI_CODE='$regicode' AND TRXA_VIEW_STAT='Y'";
	$periksaregicode_di_query = $db->query($periksaregicode) or die("Cek Fail");
	$ketersediaan = $periksaregicode_di_query->fetchColumn();

	if ($ketersediaan == 0) {
		header("location: " . "TRXASALE01.php");
	} else {
		$no = 0;
		$sub_total = 0;

		$query_header = "SELECT TRXA_REGI_CODE AS REGI_CODE, TRXA_PATI_CODE AS PATI_CODE, 
					(SELECT TRXA_SALE_CODE FROM trxasale WHERE TRXA_REGI_CODE=REGI_CODE AND TRXA_VIEW_STAT='Y' ORDER BY TRXA_ENTR_DATE DESC, TRXA_ENTR_TIME DESC LIMIT 1) AS INVOICE_NO,
					(SELECT PATI_MAIN_NAME FROM patimast WHERE PATI_MAST_CODE=PATI_CODE) AS NAME,
					(SELECT CONCAT(TRXA_ENTR_DATE,' ',TRXA_ENTR_TIME) FROM trxasale WHERE TRXA_REGI_CODE=REGI_CODE AND TRXA_VIEW_STAT='Y' ORDER BY TRXA_ENTR_DATE DESC, TRXA_ENTR_TIME DESC LIMIT 1) AS INVOICE_DATE,
					(SELECT PATI_MAIN_ADDR FROM patimast WHERE PATI_MAST_CODE=PATI_CODE) AS ADDRESS,
					CONCAT(TRXA_ENTR_DATE,' ',TRXA_ENTR_TIME) AS ADMISSION_DATE, 
					TRXA_REGI_PAYM AS PATIENT_TYPE, 
					CONCAT(TRXA_UPDT_DATE,' ',TRXA_UPDT_TIME) AS DISCHARGE_DATE, 
					TRXA_REGI_DOCT, (SELECT PASS_USER_NAME FROM passiden WHERE PASS_USER_IDEN=TRXA_REGI_DOCT) AS PRIMARY_DOCTOR 
          			FROM trxaregi 
          WHERE TRXA_REGI_CODE = '$regicode'";

		$qheader = $db->query($query_header) or die("Gagal Ambil Kode Register!!");
		$row_header = $qheader->fetch(PDO::FETCH_ASSOC);

		$admission_no = $row_header['REGI_CODE'];
		$mr_no = $row_header['PATI_CODE'];
		$invoice_no = $row_header['INVOICE_NO'];
		$name = $row_header['NAME'];
		$invoice_date = $row_header['INVOICE_DATE'];
		$address = $row_header['ADDRESS'];
		$admission_date = $row_header['ADMISSION_DATE'];
		$discharge_date = $row_header['DISCHARGE_DATE'];
		$is_bpjs = ($row_header['PATIENT_TYPE'] == 'B');

		// BPJS + ada pembayaran tagihan tambahan → cetak Patient Type = Umum
		// dan hanya tampilkan layanan/tindakan (tanpa obat/BHP)
		$q_paid = $db->query("SELECT COALESCE(SUM(TRXA_PAYM_AMNT),0) FROM trxasale
		                      WHERE TRXA_REGI_CODE='$regicode' AND TRXA_VIEW_STAT='Y'");
		$paid_amnt = (float)$q_paid->fetchColumn();
		$bpjs_extra_invoice = ($is_bpjs && $paid_amnt > 0);

		if ($row_header['PATIENT_TYPE'] == 'U') {
			$patient_type = 'Umum';
		} else if ($row_header['PATIENT_TYPE'] == 'B') {
			$patient_type = $bpjs_extra_invoice ? 'Umum' : 'BPJS';
		} else if ($row_header['PATIENT_TYPE'] == 'A') {
			$patient_type = 'Asuransi';
		} else if ($row_header['PATIENT_TYPE'] == 'P') {
			$patient_type = 'Perusahaan';
		} else {
			$patient_type = 'No Type';
		}

		$primary_doctor = $row_header['PRIMARY_DOCTOR'];

		// Setelah bayar, item status = P → print pakai I/P
		$stat_filter = "IN ('I','P')";

		require('pdf/fpdf.php');
		$pdf = new FPDF('p', 'mm', 'A4');
		$pdf->SetAutoPageBreak(true);
		$pdf->AddPage();
		$pdf->Ln(5);
		$pdf->SetFont('Arial', 'B', 18);
		if (file_exists('assets/img/logo.png')) {
			$pdf->Image('assets/img/logo.png', 10, 5, 20);
		}
		$pdf->Ln(5);

		$pdf->Cell(190, 8, 'INVOICE', 0, 1, 'C');
		$pdf->Ln(2);
		$pdf->SetFont('Arial', '', 10);

		$pdf->Cell(30, 5, 'Admission No/MR', 0, 0, 'L');
		$pdf->Cell(90, 5, ': ' . $admission_no . '/' . $mr_no . '', 0, 0, 'L');
		$pdf->Cell(30, 5, 'Invoice No', 0, 0, 'L');
		$pdf->Cell(35, 5, ': ' . $invoice_no . '', 0, 1, 'L');

		$pdf->Cell(30, 5, 'Name', 0, 0, 'L');
		$pdf->Cell(90, 5, ': ' . $name . '', 0, 0, 'L');
		$pdf->Cell(30, 5, 'Invoice Date', 0, 0, 'L');
		$pdf->Cell(35, 5, ': ' . $invoice_date . '', 0, 1, 'L');

		$pdf->Cell(30, 5, 'Address', 0, 0, 'L');
		$pdf->Cell(90, 5, ': ' . $address . '', 0, 0, 'L');
		$pdf->Cell(30, 5, 'Admission Date', 0, 0, 'L');
		$pdf->Cell(35, 5, ': ' . $admission_date . '', 0, 1, 'L');

		$pdf->Cell(30, 5, 'Patient Type', 0, 0, 'L');
		$pdf->Cell(90, 5, ': ' . $patient_type . '', 0, 0, 'L');
		$pdf->Cell(30, 5, 'Discharge Date', 0, 0, 'L');
		$pdf->Cell(35, 5, ': ' . $discharge_date . '', 0, 1, 'L');

		$pdf->Cell(30, 5, 'Primary Doctor', 0, 0, 'L');
		$pdf->Cell(90, 5, ': ' . $primary_doctor . '', 0, 1, 'L');

		$pdf->SetFont('Arial', '', 9);
		$pdf->Ln(2);

		$pdf->Cell(8, 6, 'No', 'LTBR', 0, 'C');
		$pdf->Cell(50, 6, 'Name', 'LTB', 0, 'L');
		$pdf->Cell(30, 6, ' Description ', 'TBR', 0, 'L');
		$pdf->Cell(10, 6, ' Qty', 'LTBR', 0, 'R');
		$pdf->Cell(15, 6, 'UOM', 'LTBR', 0, 'L');
		$pdf->Cell(25, 6, 'Amount', 'LTBR', 0, 'R');
		$pdf->Cell(25, 6, 'Disc.', 'LTBR', 0, 'R');
		$pdf->Cell(25, 6, 'Patient', 'LTBR', 1, 'R');

		// ========== DRUGS (skip untuk BPJS extra invoice) ==========
		if (!$bpjs_extra_invoice) {
			$pdf->Cell(8, 8, ' ', 'LR', 0, 'C');
			$pdf->Cell(50, 8, 'DRUGS', 'L', 0, 'L');
			$pdf->Cell(30, 8, '  ', 'R', 0, 'L');
			$pdf->Cell(10, 8, ' ', 'LR', 0, 'R');
			$pdf->Cell(15, 8, ' ', 'LR', 0, 'L');
			$pdf->Cell(25, 8, ' ', 'LR', 0, 'R');
			$pdf->Cell(25, 8, ' ', 'LR', 0, 'R');
			$pdf->Cell(25, 8, ' ', 'LR', 1, 'R');

			$query_prsc = "SELECT TRXA_PRSC_CODE, TRXA_STOCK_CODE, 
              (SELECT INVE_PART_NAME FROM invemast WHERE INVE_MAST_CODE=TRXA_STOCK_CODE) AS STOCK_NAME, 
              (SELECT INVE_SALE_UNIT FROM invemast WHERE INVE_MAST_CODE=TRXA_STOCK_CODE) AS UNIT_CODE,
              (SELECT TBLI_UNIT_NAME FROM tbliunit WHERE TBLI_UNIT_CODE=UNIT_CODE) AS UNIT_NAME,
              TRXA_STOCK_PRIC, TRXA_STOCK_QUTY, (TRXA_STOCK_PRIC*TRXA_STOCK_QUTY) AS SUB_TOTAL_PRIC, 
              (SELECT TRXA_REGI_PAYM FROM trxaregi WHERE TRXA_REGI_CODE=TRXA_PRSC_CODE) AS PAYM_TYPE
              FROM trxaprsc WHERE TRXA_PRSC_CODE = '$regicode' AND TRXA_PRSC_STAT $stat_filter AND TRXA_VIEW_STAT='Y'";

			$qprsc = $db->query($query_prsc) or die("Gagal Ambil data obat!!");
			while ($row_prsc = $qprsc->fetch(PDO::FETCH_ASSOC)) {
				$drug_name = $row_prsc['STOCK_NAME'];
				$drug_qty = $row_prsc['TRXA_STOCK_QUTY'];
				$drug_unit = $row_prsc['UNIT_NAME'];
				$drug_amount = $row_prsc['SUB_TOTAL_PRIC'];

				if ($row_prsc['PAYM_TYPE'] === 'B' || $is_bpjs) {
					$drug_amount = 0;
				}

				$view_drug_amount = number_format($drug_amount, 0, '', '.');
				$no++;
				$sub_total = $sub_total + $drug_amount;

				$pdf->Cell(8, 6, '' . $no . '', 'LR', 0, 'C');
				$pdf->Cell(50, 6, '' . $drug_name . '', 'L', 0, 'L');
				$pdf->Cell(30, 6, '  ', 'R', 0, 'L');
				$pdf->Cell(10, 6, '' . $drug_qty . '', 'LR', 0, 'R');
				$pdf->Cell(15, 6, '' . $drug_unit . '', 'LR', 0, 'L');
				$pdf->Cell(25, 6, '' . $view_drug_amount . '', 'LR', 0, 'R');
				$pdf->Cell(25, 6, '0', 'LR', 0, 'R');
				$pdf->Cell(25, 6, '' . $view_drug_amount . '', 'LR', 1, 'R');
			}
		}

		// ========== TREATMENT (tindakan O/N) — BPJS extra: hanya amount > 0 ==========
		$query_action = "SELECT TRXA_TRET_CODE, TRXA_MEDI_CODE, 
              (SELECT TBLF_MEDI_NAME FROM tblfmedi WHERE TBLF_MEDI_CODE=TRXA_MEDI_CODE) AS MEDI_NAME, 
              TRXA_MEDI_RATE, TRXA_TRET_QUTY, (TRXA_MEDI_RATE*TRXA_TRET_QUTY) AS SUB_TOTAL, 
              (SELECT TRXA_REGI_PAYM FROM trxaregi WHERE TRXA_REGI_CODE=TRXA_TRET_CODE) AS PAYM_TYPE
              FROM trxatret WHERE (SELECT TBLF_MEDI_TYPE FROM tblfmedi WHERE TBLF_MEDI_CODE=TRXA_MEDI_CODE) IN ('O','N')  
              AND TRXA_TRET_CODE = '$regicode' AND TRXA_TRET_STAT $stat_filter AND TRXA_VIEW_STAT='Y'";

		$qaction = $db->query($query_action) or die("Gagal Ambil data tindakan!!");
		$actions_rows = $qaction->fetchAll(PDO::FETCH_ASSOC);
		$actions_print = array();
		foreach ($actions_rows as $row_action) {
			$action_amount = (float)$row_action['SUB_TOTAL'];
			// BPJS extra / selalu: item nilai 0 (mis. Konsul BPJS) tidak dicetak
			if ($bpjs_extra_invoice && $action_amount < 1) {
				continue;
			}
			$actions_print[] = $row_action;
		}
		if (count($actions_print) > 0) {
			$pdf->Cell(8, 8, ' ', 'LR', 0, 'C');
			$pdf->Cell(50, 8, 'TREATMENT', 'L', 0, 'L');
			$pdf->Cell(30, 8, '  ', 'R', 0, 'L');
			$pdf->Cell(10, 8, ' ', 'LR', 0, 'R');
			$pdf->Cell(15, 8, ' ', 'LR', 0, 'L');
			$pdf->Cell(25, 8, ' ', 'LR', 0, 'R');
			$pdf->Cell(25, 8, ' ', 'LR', 0, 'R');
			$pdf->Cell(25, 8, ' ', 'LR', 1, 'R');

			foreach ($actions_print as $row_action) {
				$action_amount = (float)$row_action['SUB_TOTAL'];
				$action_name = $row_action['MEDI_NAME'];
				$action_qty = $row_action['TRXA_TRET_QUTY'];
				$view_action_amount = number_format($action_amount, 0, '', '.');

				$no++;
				$sub_total = $sub_total + $action_amount;

				$pdf->Cell(8, 6, '' . $no . '', 'LR', 0, 'C');
				$pdf->Cell(50, 6, '' . $action_name . '', 'L', 0, 'L');
				$pdf->Cell(30, 6, ' ', 'R', 0, 'L');
				$pdf->Cell(10, 6, '' . $action_qty . '', 'LR', 0, 'R');
				$pdf->Cell(15, 6, ' ', 'LR', 0, 'L');
				$pdf->Cell(25, 6, '' . $view_action_amount . '', 'LR', 0, 'R');
				$pdf->Cell(25, 6, '0', 'LR', 0, 'R');
				$pdf->Cell(25, 6, '' . $view_action_amount . '', 'LR', 1, 'R');
			}
		}

		// ========== SERVICES (layanan J) — BPJS extra: hanya amount > 0 ==========
		$query_tret = "SELECT TRXA_TRET_CODE, TRXA_MEDI_CODE, 
              (SELECT TBLF_MEDI_NAME FROM tblfmedi WHERE TBLF_MEDI_CODE=TRXA_MEDI_CODE) AS MEDI_NAME, 
              TRXA_MEDI_RATE, TRXA_TRET_QUTY, (TRXA_MEDI_RATE*TRXA_TRET_QUTY) AS SUB_TOTAL, 
              (SELECT TRXA_REGI_PAYM FROM trxaregi WHERE TRXA_REGI_CODE=TRXA_TRET_CODE) AS PAYM_TYPE
              FROM trxatret WHERE (SELECT TBLF_MEDI_TYPE FROM tblfmedi WHERE TBLF_MEDI_CODE=TRXA_MEDI_CODE) = 'J' 
              AND TRXA_TRET_CODE = '$regicode' AND TRXA_TRET_STAT $stat_filter AND TRXA_VIEW_STAT='Y'";

		$qtret = $db->query($query_tret) or die("Gagal Ambil data jasa pelayanan!!");
		$services_rows = $qtret->fetchAll(PDO::FETCH_ASSOC);
		$services_print = array();
		foreach ($services_rows as $row_tret) {
			$tret_amount = (float)$row_tret['SUB_TOTAL'];
			// ex: Konsul Dokter (BPJS) amount 0 → skip
			if ($bpjs_extra_invoice && $tret_amount < 1) {
				continue;
			}
			$services_print[] = $row_tret;
		}
		if (count($services_print) > 0) {
			$pdf->Cell(8, 8, ' ', 'LR', 0, 'C');
			$pdf->Cell(50, 8, 'SERVICES', 'L', 0, 'L');
			$pdf->Cell(30, 8, '  ', 'R', 0, 'L');
			$pdf->Cell(10, 8, ' ', 'LR', 0, 'R');
			$pdf->Cell(15, 8, ' ', 'LR', 0, 'L');
			$pdf->Cell(25, 8, ' ', 'LR', 0, 'R');
			$pdf->Cell(25, 8, ' ', 'LR', 0, 'R');
			$pdf->Cell(25, 8, ' ', 'LR', 1, 'R');

			foreach ($services_print as $row_tret) {
				$tret_amount = (float)$row_tret['SUB_TOTAL'];
				$tret_name = $row_tret['MEDI_NAME'];
				$tret_qty = $row_tret['TRXA_TRET_QUTY'];
				$view_tret_amount = number_format($tret_amount, 0, '', '.');

				$no++;
				$sub_total = $sub_total + $tret_amount;

				$pdf->Cell(8, 6, '' . $no . '', 'LR', 0, 'C');
				$pdf->Cell(50, 6, '' . $tret_name . '', 'L', 0, 'L');
				$pdf->Cell(30, 6, ' ', 'R', 0, 'L');
				$pdf->Cell(10, 6, '' . $tret_qty . '', 'LR', 0, 'R');
				$pdf->Cell(15, 6, ' ', 'LR', 0, 'L');
				$pdf->Cell(25, 6, '' . $view_tret_amount . '', 'LR', 0, 'R');
				$pdf->Cell(25, 6, '0', 'LR', 0, 'R');
				$pdf->Cell(25, 6, '' . $view_tret_amount . '', 'LR', 1, 'R');
			}
		}

		// ========== CONSUMABLE / BHP (skip untuk BPJS extra invoice) ==========
		if (!$bpjs_extra_invoice) {
			$pdf->Cell(8, 8, ' ', 'LR', 0, 'C');
			$pdf->Cell(50, 8, 'CONSUMABLE', 'L', 0, 'L');
			$pdf->Cell(30, 8, '  ', 'R', 0, 'L');
			$pdf->Cell(10, 8, ' ', 'LR', 0, 'R');
			$pdf->Cell(15, 8, ' ', 'LR', 0, 'L');
			$pdf->Cell(25, 8, ' ', 'LR', 0, 'R');
			$pdf->Cell(25, 8, ' ', 'LR', 0, 'R');
			$pdf->Cell(25, 8, ' ', 'LR', 1, 'R');

			$query_csbl = "SELECT TRXA_CSBL_CODE, TRXA_STOCK_CODE, 
              (SELECT INVE_PART_NAME FROM invemast WHERE INVE_MAST_CODE=TRXA_STOCK_CODE) AS STOCK_NAME, 
              TRXA_STOCK_PRIC, TRXA_STOCK_QUTY, (TRXA_STOCK_PRIC*TRXA_STOCK_QUTY) AS SUB_TOTAL_PRIC, 
              (SELECT TRXA_REGI_PAYM FROM trxaregi WHERE TRXA_REGI_CODE=TRXA_CSBL_CODE) AS PAYM_TYPE
              FROM trxacsbl WHERE TRXA_CSBL_CODE = '$regicode' AND TRXA_CSBL_STAT $stat_filter AND TRXA_VIEW_STAT='Y'";

			$qcsbl = $db->query($query_csbl) or die("Gagal Ambil data BHP!!");
			while ($row_csbl = $qcsbl->fetch(PDO::FETCH_ASSOC)) {
				$csbl_name = $row_csbl['STOCK_NAME'];
				$csbl_qty = $row_csbl['TRXA_STOCK_QUTY'];
				$csbl_amount = $row_csbl['SUB_TOTAL_PRIC'];
				if ($row_csbl['PAYM_TYPE'] === 'B' || $is_bpjs) {
					$csbl_amount = 0;
				}
				$view_csbl_amount = number_format($csbl_amount, 0, '', '.');

				$no++;
				$sub_total = $sub_total + $csbl_amount;

				$pdf->Cell(8, 6, '' . $no . '', 'LR', 0, 'C');
				$pdf->Cell(50, 6, '' . $csbl_name . '', 'L', 0, 'L');
				$pdf->Cell(30, 6, ' ', 'R', 0, 'L');
				$pdf->Cell(10, 6, '' . $csbl_qty . '', 'LR', 0, 'R');
				$pdf->Cell(15, 6, ' ', 'LR', 0, 'L');
				$pdf->Cell(25, 6, '' . $view_csbl_amount . '', 'LR', 0, 'R');
				$pdf->Cell(25, 6, '0', 'LR', 0, 'R');
				$pdf->Cell(25, 6, '' . $view_csbl_amount . '', 'LR', 1, 'R');
			}
		}

		$pdf->Cell(113, 6, 'SUB TOTAL :', 'T', 0, 'R');
		$view_sub_total = number_format($sub_total, 0, '', '.');
		$pdf->Cell(25, 6, '' . $view_sub_total . '', 'T', 0, 'R');
		$pdf->Cell(25, 6, '0', 'T', 0, 'R');
		$pdf->Cell(25, 6, '' . $view_sub_total . '', 'T', 1, 'R');

		$pdf->Cell(113, 6, 'DISCOUNT :', 0, 0, 'R');
		$pdf->Cell(25, 6, '', 0, 0, 'R');
		$pdf->Cell(25, 6, '', 0, 0, 'R');
		$pdf->Cell(25, 6, '0', 0, 1, 'R');

		$pdf->Cell(113, 6, 'ADMIN CHARGE :', 0, 0, 'R');
		$pdf->Cell(25, 6, '', 0, 0, 'R');
		$pdf->Cell(25, 6, '', 0, 0, 'R');

		// Admin: BPJS extra invoice = 0
		if ($bpjs_extra_invoice || $is_bpjs) {
			$total_admin = 0;
		} else {
			$periksaracikan = "SELECT COUNT(*) FROM trxaprsc WHERE TRXA_PRSC_CODE='$regicode' 
                     AND TRXA_PRSC_CONC='Y'
                     AND TRXA_PRSC_STAT $stat_filter
                     AND TRXA_VIEW_STAT='Y'";
			$ketersediaan_racikan = $db->query($periksaracikan)->fetchColumn();

			if ($ketersediaan_racikan == 0) {
				$periksaresep = "SELECT COUNT(*) FROM trxaprsc WHERE TRXA_PRSC_CODE='$regicode'
                     AND TRXA_PRSC_STAT $stat_filter
                     AND TRXA_VIEW_STAT='Y'";
				$ketersediaan_resep = $db->query($periksaresep)->fetchColumn();

				if ($ketersediaan_resep == 0) {
					$periksabiayaadmin = "SELECT COUNT(*) FROM trxaregi WHERE TRXA_REGI_CODE='$regicode' AND TRXA_REGI_FEE IN ('Y','P')";
					$ketersediaan_biayaadmin = $db->query($periksabiayaadmin)->fetchColumn();
					$total_admin = ($ketersediaan_biayaadmin == 0) ? 0 : $fee_admin;
				} else {
					$total_admin = ($fee_admin + $fee_resep);
				}
			} else {
				$total_admin = ($fee_admin + ($fee_resep + $fee_racikan));
			}
		}

		$view_fee_admin = number_format($total_admin, 0, '', '.');
		$pdf->Cell(25, 6, '' . $view_fee_admin . '', 0, 1, 'R');

		$pdf->Cell(113, 6, 'TOTAL :', 0, 0, 'R');
		$pdf->Cell(25, 6, '', 0, 0, 'R');
		$pdf->Cell(25, 6, '', 0, 0, 'R');
		$sub_total = $sub_total + $total_admin;
		$view_sub_total = number_format($sub_total, 0, '', '.');
		$pdf->Cell(25, 6, '' . $view_sub_total . '', 0, 1, 'R');

		$query_payment = "SELECT SUM(TRXA_PAYM_AMNT) AS PAYMENT_AMOUNT FROM trxasale 
                    WHERE TRXA_REGI_CODE='$regicode'  AND TRXA_VIEW_STAT='Y'";
		$qpayment = $db->query($query_payment) or die("Gagal Ambil data Pembayaran!!");
		$row_payment = $qpayment->fetch(PDO::FETCH_ASSOC);
		$payment = $row_payment['PAYMENT_AMOUNT'];
		$balance = $sub_total - $payment;

		$pdf->Cell(113, 6, 'PAYMENT :', 0, 0, 'R');
		$pdf->Cell(25, 6, '', 0, 0, 'R');
		$pdf->Cell(25, 6, '', 0, 0, 'R');
		$view_payment = number_format($payment, 0, '', '.');
		$pdf->Cell(25, 6, '' . $view_payment . '', 0, 1, 'R');

		$pdf->Cell(113, 6, 'BALANCE :', 0, 0, 'R');
		$pdf->Cell(25, 6, '', 0, 0, 'R');
		$pdf->Cell(25, 6, '', 0, 0, 'R');
		$view_balance = number_format($balance, 0, '', '.');
		$pdf->Cell(25, 6, '' . $view_balance . '', 0, 1, 'R');

		$pdf->Cell(60, 5, 'IN WORDS PATIENT :', 0, 0, 'L');
		$pdf->SetFont('Arial', '', 8);
		$words_patient = terbilang($payment);
		$pdf->Cell(70, 5, '' . $words_patient . ' Rupiah', 0, 1, 'R');

		$pdf->Ln(4);
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(60, 5, 'PATIENT RECEIPT / KUITANSI :', 0, 1, 'L');
		$pdf->SetFont('Arial', '', 8);

		$pdf->Cell(18, 6, 'Type', 'LTBR', 0, 'L');
		$pdf->Cell(18, 6, 'Date', 'LTBR', 0, 'L');
		$pdf->Cell(22, 6, 'Payment Mode', 'LTBR', 0, 'L');
		$pdf->Cell(25, 6, 'Account No.', 'LTBR', 0, 'L');
		$pdf->Cell(25, 6, 'Account Name', 'LTBR', 0, 'L');
		$pdf->Cell(30, 6, 'Description', 'LTBR', 0, 'L');
		$pdf->Cell(25, 6, 'Cashier', 'LTBR', 0, 'R');
		$pdf->Cell(25, 6, 'Patient', 'LTBR', 1, 'R');

		$query_footer = "SELECT TRXA_PAYM_MODE, TRXA_ENTR_DATE, TRXA_ENTR_USER, 
                  (SELECT PASS_USER_NAME FROM passiden WHERE PASS_USER_IDEN=TRXA_ENTR_USER) AS CASHIER 
                  FROM trxasale 
                  WHERE TRXA_SALE_CODE='$invoice_no' AND TRXA_VIEW_STAT='Y'";
		$qfooter = $db->query($query_footer) or die("Gagal Ambil data Footer!!");
		$row_footer = $qfooter->fetch(PDO::FETCH_ASSOC);

		$paymmode = '';
		if ($row_footer && $row_footer['TRXA_PAYM_MODE'] == 'TUN') {
			$paymmode = 'Cash';
		} else if ($row_footer && $row_footer['TRXA_PAYM_MODE'] == 'BCA') {
			$paymmode = 'Debit BCA';
		} else if ($row_footer && $row_footer['TRXA_PAYM_MODE'] == 'MAN') {
			$paymmode = 'Debit Mandiri';
		} else if ($row_footer && $row_footer['TRXA_PAYM_MODE'] == 'BNI') {
			$paymmode = 'Debit BNI';
		} else if ($row_footer && $row_footer['TRXA_PAYM_MODE'] == 'BCM') {
			$paymmode = 'Transfer BCA';
		} else if ($row_footer && $row_footer['TRXA_PAYM_MODE'] == 'LIN') {
			$paymmode = 'Transfer LinkAja';
		} else if ($row_footer) {
			$paymmode = $row_footer['TRXA_PAYM_MODE'];
		}

		$paymdate = $row_footer ? date("d/m/Y", strtotime($row_footer['TRXA_ENTR_DATE'])) : '';
		$cashier = $row_footer ? $row_footer['CASHIER'] : '';

		$pdf->Cell(18, 8, 'Payment', 'LTBR', 0, 'L');
		$pdf->Cell(18, 8, '' . $paymdate . '', 'LTBR', 0, 'L');
		$pdf->Cell(22, 8, '' . $paymmode . '', 'LTBR', 0, 'L');
		$pdf->Cell(25, 8, ' ', 'LTBR', 0, 'R');
		$pdf->Cell(25, 8, ' ', 'LTBR', 0, 'L');
		$pdf->Cell(30, 8, ' ', 'LTBR', 0, 'R');
		$pdf->Cell(25, 8, '' . $cashier . '', 'LTBR', 0, 'R');
		$pdf->Cell(25, 8, '' . $view_payment . '', 'LTBR', 1, 'R');

		$pdf->Cell(163, 8, 'TOTAL :', 0, 0, 'R');
		$pdf->Cell(25, 8, '' . $view_payment . '', 0, 1, 'R');

		$pdf->Ln(2);
		$pdf->Cell(150, 8, ' ', 0, 0, 'R');
		$pdf->Cell(30, 8, 'CASHIER', 0, 0, 'C');
		$pdf->Ln(15);
		$pdf->Cell(150, 8, ' ', 0, 0, 'R');
		$pdf->Cell(30, 8, '' . $cashier . '', 0, 0, 'C');

		$pdf->Output('I', 'KWITANSI-' . $invoice_no . '.pdf');
	}
} else {
	header("location: " . "TRXASALE01.php");
}
?>
