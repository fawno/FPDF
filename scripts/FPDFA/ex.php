<?php
	declare(strict_types=1);

	require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
	require dirname(dirname(__DIR__)) . '/src/Traits/FontsTrait.php';
	require __DIR__ . '/FPDFATrait.php';

	use Fawno\FPDF\Traits\FontsTrait;
	use FPDF\Scripts\FPDFA\FPDFATrait;

	$pdf = new class extends FPDF {
		use FontsTrait;
		use FPDFATrait;
	};

	$pdf->AddFont('DejaVuSansCondensed', '', __DIR__ . '/DejaVuSansCondensed.php');
	$pdf->SetFont('DejaVuSansCondensed', '', 16);
	$pdf->AddPage();
	$pdf->Write(10, 'This PDF is PDF/A-3b compliant.');

	$pdf->Output('F', __DIR__ . '/example.pdf');
