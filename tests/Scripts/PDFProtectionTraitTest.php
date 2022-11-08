<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use ddn\sapp\PDFDoc;
	use FPDF;
	use FPDF\Scripts\PDFProtection\PDFProtectionTrait;
	use Fawno\FPDF\Tests\TestCase;

	class PDFProtectionTraitTest extends TestCase {
		public function testPDFProtectionTrait () {
			$pdf = new class extends FPDF {
				use PDFProtectionTrait;
			};

			$pdf->SetProtection(['print']);
			$pdf->AddPage();
			$pdf->SetFont('Arial');
			$pdf->Write(10, 'You can print me but not copy my text.');

			$this->assertFileCanBeCreated($pdf);

			$this->expectError();
			$this->expectErrorMessageMatches('~Uninitialized string offset:? -1~');

			$this->assertPdfIsOk($pdf);
		}
	}
