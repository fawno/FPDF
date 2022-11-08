<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use FPDF;
	use FPDF\Scripts\PDFCode128\PDFCode128Trait;
	use Fawno\FPDF\Tests\TestCase;

	class PDFCode128TraitTest extends TestCase {
		public function testPDFCode128Trait () {
			$pdf = new class extends FPDF {
				use PDFCode128Trait;
			};

			$pdf->AddPage();
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

			$this->assertFileCanBeCreated($pdf);

			$this->assertPdfIsOk($pdf);
		}
	}
