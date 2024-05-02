<?php
	declare(strict_types=1);

	require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
	require __DIR__ . '/AttachmentsTrait.php';

	use FPDF\Scripts\Attachments\AttachmentsTrait;

	$pdf = new class extends FPDF {
		use AttachmentsTrait;
	};

	$pdf->Attach('attached.txt');
	$pdf->OpenAttachmentPane();
	$pdf->AddPage();
	$pdf->SetFont('Arial','',14);
	$pdf->Write(5,'This PDF contains an attached file.');

	$pdf->Output('F', __DIR__ . '/example.pdf');
