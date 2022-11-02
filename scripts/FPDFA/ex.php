<?php
	declare(strict_types=1);

	define('FPDF_FONTPATH', __DIR__);

	require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
	require __DIR__ . '/FPDFATrait.php';

	use FPDF\Scripts\FPDFA\FPDFATrait;

	$pdf = new class extends FPDF {
		use FPDFATrait;
	};

	$pdf->AddFont('DejaVuSansCondensed', '', 'DejaVuSansCondensed.php');
	$pdf->SetFont('DejaVuSansCondensed', '', 16);
	$pdf->AddPage();
	$pdf->Write(10, 'This PDF is PDF/A-3b compliant.');

	$pdf->Output('F', __DIR__ . '/example.pdf');
