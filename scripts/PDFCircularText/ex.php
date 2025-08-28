<?php
	declare(strict_types=1);

	require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
	require dirname(__DIR__) . '/PDFTransform/PDFTransformTrait.php';
	require __DIR__ . '/PDFCircularTextTrait.php';

	use FPDF\Scripts\PDFTransform\PDFTransformTrait;
	use FPDF\Scripts\PDFCircularText\PDFCircularTextTrait;

	$pdf = new class extends FPDF {
		use PDFTransformTrait;
		use PDFCircularTextTrait;
	};

	$pdf->AddPage();
	$pdf->SetFont('Arial', '', 32);

	$text='Circular Text';
	$pdf->CircularText(105, 50, 30, $text, 'top');
	$pdf->CircularText(105, 50, 30, $text, 'bottom');

	$pdf->Output('F', __DIR__ . '/example.pdf');
