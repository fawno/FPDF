<?php
	declare(strict_types=1);

	require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
	require __DIR__ . '/QRcodeTrait.php';
    require __DIR__ . '/QRcode.php';

	use FPDF\Scripts\QRcode\QRcodeTrait;

	$pdf = new class extends FPDF {
		use QRcodeTrait;
	};

	$pdf->AddPage();

	$pdf->QRcode(5, 5, 50, 'Generated QR Code Data');

	$pdf->Output('F', __DIR__ . '/example.pdf');
