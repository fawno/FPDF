<?php
	declare(strict_types=1);

	require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
	require __DIR__ . '/ESignaturePlaceholderTrait.php';

	use FPDF\Scripts\ESignaturePlaceholder\ESignaturePlaceholderTrait;

	$pdf = new class extends FPDF {
		use ESignaturePlaceholderTrait;
	};

	$pdf->AddPage();
	$pdf->SetFont('Arial', '', 12);
	$pdf->Text(20, 10, 'First Sign Here:');
	// add placeholder with name `SignPlaceholder1` on page 1
	$pdf->AddSignatureField(20, 15, 120, 40, 'SignPlaceholder1');

	$pdf->AddPage();
	$pdf->SetFont('Arial', '', 12);
	$pdf->Text(20, 10, 'Second Sign Here:');
	// add placeholder with default name on page 2
	$pdf->AddSignatureField(20, 15, 120, 40);
