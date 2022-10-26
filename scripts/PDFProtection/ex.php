<?php
	declare(strict_types=1);

	require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
	require __DIR__ . '/PDFProtectionTrait.php';

	use FPDF\Scripts\PDFProtection\PDFProtectionTrait;

	$pdf = new class extends FPDF {
		use PDFProtectionTrait;
	};

	$pdf->SetProtection(['print']);
	$pdf->AddPage();
	$pdf->SetFont('Arial');
	$pdf->Write(10, 'You can print me but not copy my text.');

	$pdf->Output('F', __DIR__ . '/example.pdf');
