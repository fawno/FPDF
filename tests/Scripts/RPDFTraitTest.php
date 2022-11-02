<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use FPDF;
	use FPDF\Scripts\RPDF\RPDFTrait;
	use Fawno\FPDF\Tests\TestCase;

	class RPDFTraitTest extends TestCase {
		public function testRPDFTrait () {
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

			$this->assertFileCanBeCreated($pdf);
		}
	}
