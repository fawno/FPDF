<?php
	declare(strict_types=1);

	require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
	require __DIR__ . '/PDFRotateTrait.php';

	use FPDF\Scripts\PDFRotate\PDFRotateTrait;

	$pdf = new class extends FPDF {
		use PDFRotateTrait;
	};

	$pdf->AddPage();
	$pdf->SetFont('Arial', '', 20);
	$pdf->RotatedImage(__DIR__ . '/circle.png', 85, 60, 40, 16, 45);
	$pdf->RotatedText(100, 60, 'Hello!', 45);

	$pdf->Output('F', __DIR__ . '/example.pdf');
