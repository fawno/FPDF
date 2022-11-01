<?php
	declare(strict_types=1);

	require dirname(dirname(__DIR__)) . '/fpdf/fpdf.php';
	require __DIR__ . '/VariableStream.php';
	require __DIR__ . '/PDFMemImageTrait.php';

	use FPDF\Scripts\PDFMemImage\PDFMemImageTrait;

	$pdf = new class extends FPDF {
		use PDFMemImageTrait;
	};


	$pdf->AddPage();

	// Load an image into a variable
	$logo = file_get_contents(__DIR__ . '/logo.jpg');
	// Output it
	$pdf->MemImage($logo, 50, 30);

	// Base64 Image
	$pdf->MemImage(base64_encode($logo), 85, 30);

	// Create a GD graphics
	$im = imagecreate(200, 150);
	$bgcolor = imagecolorallocate($im, 255, 255, 255);
	$bordercolor = imagecolorallocate($im, 0, 0, 0);
	$color1 = imagecolorallocate($im, 255, 0, 0);
	$color2 = imagecolorallocate($im, 0, 255, 0);
	$color3 = imagecolorallocate($im, 0, 0, 255);
	imagefilledrectangle($im, 0, 0, 199, 149, $bgcolor);
	imagerectangle($im, 0, 0, 199, 149, $bordercolor);
	imagefilledrectangle($im, 30, 100, 60, 148, $color1);
	imagefilledrectangle($im, 80, 80, 110, 148, $color2);
	imagefilledrectangle($im, 130, 40, 160, 148, $color3);
	// Output it
	$pdf->GDImage($im, 120, 25, 40);
	imagedestroy($im);

	$pdf->Output('F', __DIR__ . '/example.pdf');
