<?php
  declare(strict_types=1);

	if (!class_exists('Fawno\FPDF\FawnoFPDF')) {
		require dirname(__DIR__) . '/vendor/autoload.php';
	}

	use Fawno\FPDF\FawnoFPDF;

	FawnoFPDF::macro('SetDash', function($black = null, $white = null) {
		if ($black !== null) {
			$s = sprintf('[%.3F %.3F] 0 d', $black * $this->k, $white * $this->k);
		} else {
			$s = '[] 0 d';
		}

		$this->_out($s);
	});

	$pdf = new FawnoFPDF();

	// PDFProtection
	$pdf->SetProtection(['print'], 'admin', 'admin');

	// PDFBookmarkTrait;
	$pdf->SetFont('Arial', '', 15);
	// Page 1
	$pdf->AddPage();
	$pdf->Bookmark('Page 1', false);
	$pdf->Bookmark('Paragraph 1', false, 1, -1);
	$pdf->Cell(0, 6, 'Paragraph 1');
	$pdf->Ln(50);
	$pdf->Bookmark('Paragraph 2', false, 1, -1);
	$pdf->Cell(0, 6, 'Paragraph 2');
	// Page 2
	$pdf->AddPage();
	$pdf->Bookmark('Page 2', false);
	$pdf->Bookmark('Paragraph 3', false, 1, -1);
	$pdf->Cell(0, 6, 'Paragraph 3');

	//PDFMacroable
	$pdf->AddPage();
	$pdf->Bookmark('PDFMacroable', false);
	$pdf->SetLineWidth(0.1);
	$pdf->SetDash(5,5); //5mm on, 5mm off
	$pdf->Line(20,20,190,20);
	$pdf->SetLineWidth(0.5);
	$pdf->Line(20,25,190,25);
	$pdf->SetLineWidth(0.8);
	$pdf->SetDash(4,2); //4mm on, 2mm off
	$pdf->Rect(20,30,170,20);
	$pdf->SetDash(); //restores no dash
	$pdf->Line(20,55,190,55);

	//PDFMultiCellsTableTrait
	$pdf->AddPage();
	$pdf->Bookmark('PDFMultiCellsTable', false);
	$pdf->SetFont('Arial', '', 14);
	//Table with 20 rows and 4 columns
	$filename = dirname(__DIR__) . '/scripts/PDFMultiCellsTable/table.json';
	$table = json_decode(file_get_contents($filename), true);
	$pdf->SetWidths([30, 50, 30, 40]);
	foreach ($table as $row) {
		$pdf->Row($row);
	}

	//PDFRotateTrait
	$pdf->AddPage();
	$pdf->Bookmark('PDFRotate', false);
	$pdf->SetFont('Arial','',20);
	$pdf->RotatedImage(dirname(__DIR__) . '/scripts/PDFRotate/circle.png',85,60,40,16,45);
	$pdf->RotatedText(100,60,'Hello!',45);

	// CMYKTrait
	$pdf->AddPage();
	$pdf->Bookmark('CMYK', false);
	$pdf->SetFont('Arial', 'B', 20);
	$pdf->SetLineWidth(1);

	$pdf->SetDrawColor(50, 0, 0, 0);
	$pdf->SetFillColor(100, 0, 0, 0);
	$pdf->SetTextColor(100, 0, 0, 0);
	$pdf->Rect(10, 10, 20, 20, 'DF');
	$pdf->Text(10, 40, 'Cyan');

	$pdf->SetDrawColor(0, 50, 0, 0);
	$pdf->SetFillColor(0, 100, 0, 0);
	$pdf->SetTextColor(0, 100, 0, 0);
	$pdf->Rect(40, 10, 20, 20, 'DF');
	$pdf->Text(40, 40, 'Magenta');

	$pdf->SetDrawColor(0, 0, 50, 0);
	$pdf->SetFillColor(0, 0, 100, 0);
	$pdf->SetTextColor(0, 0, 100, 0);
	$pdf->Rect(70, 10, 20, 20, 'DF');
	$pdf->Text(70, 40, 'Yellow');

	$pdf->SetDrawColor(0, 0, 0, 50);
	$pdf->SetFillColor(0, 0, 0, 100);
	$pdf->SetTextColor(0, 0, 0, 100);
	$pdf->Rect(100, 10, 20, 20, 'DF');
	$pdf->Text(100, 40, 'Black');

	$pdf->SetDrawColor(128, 0, 0);
	$pdf->SetFillColor(255, 0, 0);
	$pdf->SetTextColor(255, 0, 0);
	$pdf->Rect(10, 50, 20, 20, 'DF');
	$pdf->Text(10, 80, 'Red');

	$pdf->SetDrawColor(0, 127, 0);
	$pdf->SetFillColor(0, 255, 0);
	$pdf->SetTextColor(0, 255, 0);
	$pdf->Rect(40, 50, 20, 20, 'DF');
	$pdf->Text(40, 80, 'Green');

	$pdf->SetDrawColor(0, 0, 127);
	$pdf->SetFillColor(0, 0, 255);
	$pdf->SetTextColor(0, 0, 255);
	$pdf->Rect(70, 50, 20, 20, 'DF');
	$pdf->Text(70, 80, 'Blue');

	$pdf->SetDrawColor(50);
	$pdf->SetFillColor(0);
	$pdf->SetTextColor(0);
	$pdf->Rect(10, 90, 20, 20, 'DF');
	$pdf->Text(10, 120, 'Gray');

	//RPDFTrait
	$pdf->AddPage();
	$pdf->Bookmark('RPDF', false);
	$pdf->SetFont('Arial', '' , 40);
	$pdf->TextWithRotation(50, 65, 'Hello', 45, -45);
	$pdf->SetFontSize(30);
	$pdf->TextWithDirection(110, 50, 'world!', 'L');
	$pdf->TextWithDirection(110, 50, 'world!', 'U');
	$pdf->TextWithDirection(110, 50, 'world!', 'R');
	$pdf->TextWithDirection(110, 50, 'world!', 'D');

	//PDFMemImageTrait
	$pdf->AddPage();
	$pdf->Bookmark('PDFMemImage', false);

	$logo = file_get_contents(dirname(__DIR__) . '/scripts/PDFMemImage/logo.jpg');
	$pdf->MemImage($logo, 50, 30);
	$pdf->MemImage(base64_encode($logo), 85, 30);
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
	$pdf->GDImage($im, 120, 25, 40);
	imagedestroy($im);

	//PDFDrawTrait
	$pdf->SetFont('arial', '', 10);
	$pdf->AddPage();
	$pdf->Bookmark('PDFDraw', false);

	$style1 = ['width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => '10,20,5,10', 'phase' => 10, 'color' => [255, 0, 0]];
	$style2 = ['width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [255, 0, 0]];
	$style3 = ['width' => 1, 'cap' => 'round', 'join' => 'round', 'dash' => '2,10', 'color' => [255, 0, 0]];
	$style4 = [
		'L' => 0,
		'T' => ['width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => '20,10', 'phase' => 10, 'color' => [100, 100, 255]],
		'R' => ['width' => 0.50, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => [50, 50, 127]],
		'B' => ['width' => 0.75, 'cap' => 'square', 'join' => 'miter', 'dash' => '30,10,5,10'],
	];
	$style5 = ['width' => 0.25, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]];
	$style6 = ['width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => '10,10', 'color' => [0, 255, 0]];
	$style7 = ['width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [200, 200, 0]];

	// Line
	$pdf->Text(5, 7, 'Line examples');
	$pdf->Line(5, 10, 80, 30, $style1);
	$pdf->Line(5, 10, 5, 30, $style2);
	$pdf->Line(5, 10, 80, 10, $style3);

	// Rect
	$pdf->Text(100, 7, 'Rectangle examples');
	$pdf->Rect(100, 10, 40, 20, 'DF', $style4, [220, 220, 200]);
	$pdf->Rect(145, 10, 40, 20, 'D', ['all' => $style3]);

	// Curve
	$pdf->Text(5, 37, 'Curve examples');
	$pdf->Curve(5, 40, 30, 55, 70, 45, 60, 75, null, $style6);
	$pdf->Curve(80, 40, 70, 75, 150, 45, 100, 75, 'F', $style6);
	$pdf->Curve(140, 40, 150, 55, 180, 45, 200, 75, 'DF', $style6, [200, 220, 200]);

	// Circle and ellipse
	$pdf->Text(5, 82, 'Circle and ellipse examples');
	$pdf->SetLineStyle($style5);
	$pdf->Circle(25,105,20);
	$pdf->Circle(25,105,10, 90, 180, null, $style6);
	$pdf->Circle(25,105,10, 270, 360, 'F');
	$pdf->Circle(25,105,10, 270, 360, 'C', $style6);

	$pdf->SetLineStyle($style5);
	$pdf->Ellipse(100,105,40,20);
	$pdf->Ellipse(100,105,20,10, 0, 90, 180, null, $style6);
	$pdf->Ellipse(100,105,20,10, 0, 270, 360, 'DF', $style6);

	$pdf->SetLineStyle($style5);
	$pdf->Ellipse(175,105,30,15, 45);
	$pdf->Ellipse(175,105,15,7.50, 45, 90, 180, null, $style6);
	$pdf->Ellipse(175,105,15,7.50, 45, 270, 360, 'F', $style6, [220, 200, 200]);

	// Polygon
	$pdf->Text(5, 132, 'Polygon examples');
	$pdf->SetLineStyle(['width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]]);
	$pdf->Polygon([5,135,45,135,15,165]);
	$pdf->Polygon([60,135,80,135,80,155,70,165,50,155], 'DF', [$style6, $style7, $style7, 0, $style6]);
	$pdf->Polygon([120,135,140,135,150,155,110,155], 'D', [$style6, 0, $style7, $style6]);
	$pdf->Polygon([160,135,190,155,170,155,200,160,160,165], 'DF', ['all' => $style6], [220, 220, 220]);

	// Regular polygon
	$pdf->Text(5, 172, 'Regular polygon examples');
	$pdf->SetLineStyle($style5);
	$pdf->RegularPolygon(20, 190, 15, 6, 0, 1, 'F');
	$pdf->RegularPolygon(55, 190, 15, 6);
	$pdf->RegularPolygon(55, 190, 10, 6, 45, 0, 'DF', [$style6, 0, $style7, 0, $style7, $style7, $style6]);
	$pdf->RegularPolygon(90, 190, 15, 3, 0, 1, 'DF', ['all' => $style5], [200, 220, 200], 'F', [255, 200, 200]);
	$pdf->RegularPolygon(125, 190, 15, 4, 30, 1, null, ['all' => $style5], null, null, $style6);
	$pdf->RegularPolygon(160, 190, 15, 10);

	// Star polygon
	$pdf->Text(5, 212, 'Star polygon examples');
	$pdf->SetLineStyle($style5);
	$pdf->StarPolygon(20, 230, 15, 20, 3, 0, 1, 'F');
	$pdf->StarPolygon(55, 230, 15, 12, 5);
	$pdf->StarPolygon(55, 230, 7, 12, 5, 45, 0, 'DF', [$style6, 0, $style7, 0, $style7, $style7, $style6]);
	$pdf->StarPolygon(90, 230, 15, 20, 6, 0, 1, 'DF', ['all' => $style5], [220, 220, 200], 'F', [255, 200, 200]);
	$pdf->StarPolygon(125, 230, 15, 5, 2, 30, 1, null, ['all' => $style5], null, null, $style6);
	$pdf->StarPolygon(160, 230, 15, 10, 3);
	$pdf->StarPolygon(160, 230, 7, 50, 26);

	// Rounded rectangle
	$pdf->Text(5, 252, 'Rounded rectangle examples');
	$pdf->SetLineStyle(['width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => [0, 0, 0]]);
	$pdf->RoundedRect(5, 255, 40, 30, 3.50, '1111', 'DF');
	$pdf->RoundedRect(50, 255, 40, 30, 6.50, '1000');
	$pdf->RoundedRect(95, 255, 40, 30, 10.0, '1111', null, $style6);
	$pdf->RoundedRect(140, 255, 40, 30, 8.0, '0101', 'DF', $style6, [200, 200, 200]);

	$pdf->SetDrawColor();
	$pdf->SetFillColor();
	$pdf->SetTextColor();

	//PDFCode128Trait
	$pdf->AddPage();
	$pdf->Bookmark('PDFCode128', false);
	$pdf->SetFont('Arial', '', 10);
	//A set
	$code = 'CODE 128';
	$pdf->Code128(50, 20, $code, 80, 20);
	$pdf->SetXY(50, 45);
	$pdf->Write(5, 'A set: "' . $code . '"');

	//B set
	$code = 'Code 128';
	$pdf->Code128(50, 70, $code, 80, 20);
	$pdf->SetXY(50, 95);
	$pdf->Write(5, 'B set: "' . $code . '"');

	//C set
	$code = '12345678901234567890';
	$pdf->Code128(50, 120, $code, 110, 20);
	$pdf->SetXY(50, 145);
	$pdf->Write(5,'C set: "' . $code . '"');

	//A,C,B sets
	$code = 'ABCDEFG1234567890AbCdEf';
	$pdf->Code128(50, 170, $code, 125, 20);
	$pdf->SetXY(50, 195);
	$pdf->Write(5, 'ABC sets combined: "' . $code . '"');

	//Attachments
	$pdf->AddPage();
	$pdf->Bookmark('Attachments', false);
	$pdf->Attach(__DIR__ . '/../scripts/Attachments/attached.txt');
	$pdf->OpenAttachmentPane();
	$pdf->SetFont('Arial','',14);
	$pdf->Write(5,'This PDF contains an attached file.');

	$pdf->Output('F', __DIR__ . '/example.pdf');
