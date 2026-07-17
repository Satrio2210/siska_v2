<?php
include 'conf/config.php';
include 'inc/sanie.php';
include 'inc/lab_filter_rujukan.php';

if (isset($_GET['laboregi'])) {
	$laboregi = xss_clean($_GET['laboregi']);

	$periksaregicode = "SELECT COUNT(*) FROM trxalabo_detail_hasil
						WHERE TRXA_LABO_REGI=:regi
						AND HASIL_VIEW_STAT='Y'";
	$periksaregicode_di_query = $db->prepare($periksaregicode);
	$periksaregicode_di_query->execute(array(':regi' => $laboregi));
	$ketersediaan = $periksaregicode_di_query->fetchColumn();

	if ($ketersediaan == 0) {
		header("location: " . "TRXALABO05.php");
	} else {
		$head_laboratory_name = "Rudiana A.Md.Ak";

		$query_header = "SELECT TRXA_REGI_CODE, TRXA_PATI_CODE, TRXA_PATI_CODE AS PATI_CODE, TRXA_REGI_DATE AS REGI_DATE,
	                (SELECT PATI_MAIN_NAME FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_NAME,
	                (SELECT PATI_MAIN_BIRT FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_AGE,
	                (SELECT PATI_MAIN_GEND FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_GEND,
	                (SELECT PATI_MAIN_BIRT FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_BIRT,
	                (SELECT DATE_FORMAT(PATI_MAIN_BIRT,'%d/%m/%Y') FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS BIRT_DATE,
	                (SELECT PATI_MAIN_ADDR FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_ADDR,
	                (SELECT PATI_MAIN_DIST FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_DIST,
	                (SELECT PATI_MAIN_CITY FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_CITY,
	                (SELECT PATI_MAIN_PHNE FROM patimast WHERE PATI_MAST_CODE=TRXA_PATI_CODE) AS MAIN_PHNE,
	                TRXA_REGI_PAYM,
	                (SELECT TRXA_REGI_DOCT FROM trxaregi WHERE TRXA_PATI_CODE = PATI_CODE AND TRXA_REGI_DATE = REGI_DATE
            			 ORDER BY TRXA_ENTR_DATE, TRXA_ENTR_TIME LIMIT 1) AS REGI_DOCT,
            			 (SELECT PASS_USER_NAME FROM passiden WHERE PASS_USER_IDEN = REGI_DOCT) AS DOCT_NAME,
	                (SELECT TRXA_REGI_POLI FROM trxaregi WHERE TRXA_PATI_CODE = PATI_CODE AND TRXA_REGI_DATE = REGI_DATE
            			 ORDER BY TRXA_ENTR_DATE, TRXA_ENTR_TIME LIMIT 1) AS REGI_POLI
	                FROM trxaregi
	                WHERE TRXA_REGI_CODE = :regi AND TRXA_VIEW_STAT = 'Y'";

		$qheader = $db->prepare($query_header);
		$qheader->execute(array(':regi' => $laboregi));
		$row_header = $qheader->fetch(PDO::FETCH_ASSOC);

		if (!$row_header) {
			header("location: " . "TRXALABO05.php");
			exit;
		}

		$admission_no = $row_header['TRXA_REGI_CODE'];
		$check_date = $row_header['REGI_DATE'];
		if ($row_header['MAIN_GEND'] == 'M') {
			$gender = "Laki-laki";
		} else if ($row_header['MAIN_GEND'] == 'F') {
			$gender = "Perempuan";
		} else {
			$gender = "-";
		}

		$id_patient = $row_header['TRXA_PATI_CODE'];
		$raw_birt_date = $row_header['MAIN_BIRT'];

		$tanggal = new DateTime($raw_birt_date);
		$today = new DateTime('today');
		$y = $today->diff($tanggal)->y;
		$m = $today->diff($tanggal)->m;
		$shortage = '' . $y . ' tahun ' . $m . ' bulan';
		$pati_umur = (int)$y;
		$pati_gend = strtoupper(trim((string)$row_header['MAIN_GEND']));

		$pati_name = $row_header['MAIN_NAME'];
		$street_address = $row_header['MAIN_ADDR'];
		$dist_address = $row_header['MAIN_DIST'];
		$city_address = $row_header['MAIN_CITY'];
		$doct_name = $row_header['DOCT_NAME'];
		$poli_assign = $row_header['REGI_POLI'];

		require('pdf/fpdf.php');
		$pdf = new FPDF('p', 'mm', 'A4');
		$pdf->SetAutoPageBreak(true);
		$pdf->AddPage();
		$pdf->Ln(5);
		$pdf->SetFont('Arial', 'B', 18);
		if (file_exists('assets/img/logo.png')) {
			$pdf->Image('assets/img/logo.png', 10, 5, 20);
		}
		if (file_exists('assets/img/qr-code.png')) {
			$pdf->Image('assets/img/qr-code.png', 175, 5, 20);
		}
		$pdf->Ln(5);

		$pdf->Cell(190, 8, 'HASIL PEMERIKSAAN LABORATORIUM', 0, 1, 'C');
		$pdf->Ln(2);
		$pdf->SetFont('Arial', '', 8);
		$pdf->Ln(5);

		$pdf->Cell(30, 5, 'Nomor.', 0, 0, 'L');
		$pdf->Cell(90, 5, ': ' . $admission_no . '/' . $id_patient . ' ', 0, 0, 'L');
		$pdf->Cell(30, 5, 'Dokter', 0, 0, 'L');
		if ($poli_assign == $code_lab_room) {
			$pdf->Cell(35, 5, ': APS ', 0, 1, 'L');
		} else {
			$pdf->Cell(35, 5, ': ' . $doct_name . ' ', 0, 1, 'L');
		}

		$pdf->Cell(30, 5, 'Nama / Jns kelamin', 0, 0, 'L');
		$pdf->Cell(90, 5, ': ' . $pati_name . ' / ' . $gender . '', 0, 0, 'L');
		$pdf->Cell(30, 5, 'Tanggal', 0, 0, 'L');
		$view_check_date = function_exists('formatTanggal') ? formatTanggal($check_date) : $check_date;
		$pdf->Cell(35, 5, ': ' . $view_check_date . ' ', 0, 1, 'L');

		$pdf->Cell(30, 5, 'Tgl.Lahir/Umur', 0, 0, 'L');
		$view_birt_date = function_exists('formatTanggal') ? formatTanggal($raw_birt_date) : $raw_birt_date;
		$pdf->Cell(90, 5, ': ' . $view_birt_date . ' / ' . $shortage . ' ', 0, 1, 'L');

		$pdf->Cell(30, 5, 'Alamat', 0, 0, 'L');
		$pdf->Cell(90, 5, ': ' . $street_address . ' ' . $dist_address . ' ', 0, 1, 'L');
		$pdf->Cell(30, 5, ' ', 0, 0, 'L');
		$pdf->Cell(35, 5, ': ' . $city_address . ' ', 0, 1, 'L');

		$pdf->Ln(5);
		$pdf->SetFont('Arial', 'B', 10);

		if ($gender == 'Laki-laki') {
			$pdf->setFillColor(153, 204, 255);
		} else if ($gender == 'Perempuan') {
			$pdf->setFillColor(255, 204, 229);
		} else {
			$pdf->setFillColor(230, 230, 230);
		}

		$pdf->Cell(50, 15, 'JENIS PEMERIKSAAN', 'TLR', 0, 'C', 1);
		$pdf->Cell(50, 15, 'HASIL', 'TLR', 0, 'C', 1);
		$pdf->Cell(20, 15, 'SATUAN', 'TLR', 0, 'C', 1);
		$pdf->Cell(35, 15, 'NILAI NORMAL', 'TLR', 0, 'C', 1);
		$pdf->Cell(35, 15, 'KETERANGAN', 'TLR', 1, 'C', 1);

		$query_group = "SELECT h.TRXA_MEDI_CODE AS MEDI_CODE,
		                       COALESCE(m.TBLF_MEDI_NAME, h.TRXA_MEDI_CODE, h.TEMP_CODE, 'HASIL LAB') AS MEDI_NAME
		                FROM trxalabo_detail_hasil h
		                LEFT JOIN tblfmedi m ON m.TBLF_MEDI_CODE = h.TRXA_MEDI_CODE
		                WHERE h.TRXA_LABO_REGI = :regi AND h.HASIL_VIEW_STAT = 'Y'
		                GROUP BY h.TRXA_MEDI_CODE, MEDI_NAME
		                ORDER BY MEDI_NAME";
		$q_body = $db->prepare($query_group);
		$q_body->execute(array(':regi' => $laboregi));

		while ($row_body = $q_body->fetch(PDO::FETCH_ASSOC)) {
			$outmedicode = $row_body['MEDI_CODE'];
			$outmediname = strtoupper($row_body['MEDI_NAME']);
			$no = 0;

			$pdf->setFillColor(230, 230, 230);
			$pdf->SetFont('Arial', 'B', 8);
			$pdf->Cell(190, 8, '' . $outmediname . '', 'TBLR', 1, 'C', 1);
			$pdf->SetFont('Arial', '', 8);

			$query_detail = "SELECT ITEM_NAME, ITEM_HASIL, ITEM_SATUAN, ITEM_RUJUKAN, ITEM_NOTE
			                 FROM trxalabo_detail_hasil
			                 WHERE TRXA_LABO_REGI = :regi
			                   AND HASIL_VIEW_STAT = 'Y'
			                   AND (TRXA_MEDI_CODE = :medi OR (:medi IS NULL AND TRXA_MEDI_CODE IS NULL))
			                 ORDER BY ITEM_URUT, HASIL_ID";
			$q_detail = $db->prepare($query_detail);
			$q_detail->execute(array(':regi' => $laboregi, ':medi' => $outmedicode));

			while ($row_detail = $q_detail->fetch(PDO::FETCH_ASSOC)) {
				$outsizename = $row_detail['ITEM_NAME'];
				$outrujukan = filter_lab_rujukan($row_detail['ITEM_RUJUKAN'], $pati_umur, $pati_gend);
				$outresult = format_lab_hasil_flag($row_detail['ITEM_HASIL'], $outrujukan);
				$outunitname = $row_detail['ITEM_SATUAN'];
				$outlabonote = $row_detail['ITEM_NOTE'];
				if ($outrujukan !== null && $outrujukan !== '') {
					$outrujukan = str_replace(array("\r\n", "\n", "\r"), ' / ', $outrujukan);
					if (strlen($outrujukan) > 40) {
						$outrujukan = substr($outrujukan, 0, 37) . '...';
					}
				}

				$no++;
				$fill = ($no % 2 == 0);
				if ($fill) {
					$pdf->setFillColor(245, 245, 245);
				}

				$pdf->Cell(50, 5, '    ' . $outsizename, 'LR', 0, 'L', $fill);
				$pdf->Cell(50, 5, '' . $outresult, 'LR', 0, 'C', $fill);
				$pdf->Cell(20, 5, '' . $outunitname, 'LR', 0, 'C', $fill);
				$pdf->Cell(35, 5, '' . $outrujukan, 'LR', 0, 'C', $fill);
				$pdf->Cell(35, 5, '' . $outlabonote, 'LR', 1, 'L', $fill);
			}
		}

		$pdf->Cell(50, 5, '  ', 'T', 0, 'L');
		$pdf->Cell(50, 5, '  ', 'T', 0, 'L');
		$pdf->Cell(20, 5, '  ', 'T', 0, 'L');
		$pdf->Cell(35, 5, '  ', 'T', 0, 'L');
		$pdf->Cell(35, 5, '  ', 'T', 1, 'L');
		$pdf->Ln(5);

		$query_footer = "SELECT DATE_FORMAT(HASIL_ENTR_DATE,'%d/%m/%Y') AS ENTR_DATE,
	    				 TIME_FORMAT(HASIL_ENTR_TIME,'%h:%i') AS ENTR_TIME,
	    				 HASIL_ENTR_USER,
	    				 (SELECT PASS_USER_NAME FROM passiden WHERE PASS_USER_IDEN = HASIL_ENTR_USER) AS ENTR_USER
	                	 FROM trxalabo_detail_hasil
	                	 WHERE TRXA_LABO_REGI = :regi AND HASIL_VIEW_STAT = 'Y' LIMIT 1";
		$qfooter = $db->prepare($query_footer);
		$qfooter->execute(array(':regi' => $laboregi));
		$row_footer = $qfooter->fetch(PDO::FETCH_ASSOC);

		$entr_date = $row_footer ? $row_footer['ENTR_DATE'] : '';
		$entr_time = $row_footer ? $row_footer['ENTR_TIME'] : '';
		$entr_user = $row_footer ? $row_footer['ENTR_USER'] : '';

		$query_print = "SELECT DATE_FORMAT(TRXA_UPDT_DATE,'%d/%m/%Y') AS PRINT_DATE,
	    				 TIME_FORMAT(TRXA_UPDT_TIME,'%h:%i') AS PRINT_TIME
	                	 FROM trxaregi
	                	 WHERE TRXA_REGI_CODE = :regi AND TRXA_VIEW_STAT = 'Y' LIMIT 1";
		$qprint = $db->prepare($query_print);
		$qprint->execute(array(':regi' => $laboregi));
		$row_print = $qprint->fetch(PDO::FETCH_ASSOC);
		$print_date = $row_print ? $row_print['PRINT_DATE'] : date('d/m/Y');
		$print_time = $row_print ? $row_print['PRINT_TIME'] : date('H:i');

		$pdf->Cell(80, 5, 'Waktu pengambilan Specimen : ' . $entr_date . ' ' . $entr_time . '', 0, 0, 'L');
		$pdf->Cell(110, 5, ' ', 0, 1, 'L');
		$pdf->Cell(80, 5, 'Waktu penyerahan hasil : ' . $print_date . ' ' . $print_time . '', 0, 0, 'L');
		$pdf->Cell(110, 5, ' ', 0, 1, 'L');
		$pdf->Ln(5);

		$pdf->Cell(63, 5, ' Pelaksana ', 0, 0, 'C');
		$pdf->Cell(64, 5, ' ', 0, 0, 'L');
		$pdf->Cell(63, 5, ' Pj. Laboratorium ', 0, 1, 'C');
		$pdf->Ln(10);
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->Cell(63, 5, ' ' . $entr_user . ' ', 0, 0, 'C');
		$pdf->Cell(64, 5, ' ', 0, 0, 'L');
		$pdf->Cell(63, 5, ' ' . $head_laboratory_name . ' ', 0, 1, 'C');
		$pdf->SetFont('Arial', '', 8);

		$pdf->Output('I', 'LABS-' . $laboregi . '.pdf');
	}
} else {
	header("location: " . "TRXALABO05.php");
}
?>
