<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use FPDF;
	use FPDF\Scripts\PDFDraw\PDFDrawTrait;
	use Fawno\FPDF\Traits\CMYKTrait;
	use PHPUnit\Framework\TestCase;

	class PDFDrawTraitTest extends TestCase {
		public function testPDFDrawTrait () {
			$pdf = new class extends FPDF {
				use CMYKTrait;
				use PDFDrawTrait;
			};

			$pdf->SetFont('arial', '', 10);
			$pdf->AddPage();

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

			$filename = __DIR__ . '/example' . basename(__CLASS__) . '.pdf';
			$pdf->Output('F', $filename);

			$this->assertFileExists($filename);

			if (is_file($filename)) {
				unlink($filename);
			}
		}
	}
