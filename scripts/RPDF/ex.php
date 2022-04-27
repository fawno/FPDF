<?php
	declare(strict_types=1);

	require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
	require __DIR__ . '/RPDFTrait.php';

	use FPDF\Scripts\RPDF\RPDFTrait;

	$pdf = new class extends FPDF {
		use RPDFTrait;
	};

	$pdf->AddPage();
	$pdf->SetFont('Arial', '' , 40);
	$pdf->TextWithRotation(50, 65, 'Hello', 45, -45);
	$pdf->SetFontSize(30);
	$pdf->TextWithDirection(110, 50, 'world!', 'L');
	$pdf->TextWithDirection(110, 50, 'world!', 'U');
	$pdf->TextWithDirection(110, 50, 'world!', 'R');
	$pdf->TextWithDirection(110, 50, 'world!', 'D');
	$pdf->Output('F', __DIR__ . '/example.pdf');
