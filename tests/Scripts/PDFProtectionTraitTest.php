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

			$pdf->SetProtection(['print'], 'admin', 'admin');
			$pdf->AddPage();
			$pdf->SetFont('Arial');
			$pdf->Write(10, 'You can print me but not copy my text.');

			$this->assertFileCanBeCreated($pdf);

			if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
				$this->markTestIncomplete('Encryption support is incomplete in ddn\sapp\PDFDoc');
			}

			$this->assertPdfIsOk($pdf);
		}
	}
