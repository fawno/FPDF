<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use FPDF;
	use FPDF\Scripts\PDFProtection\PDFProtectionTrait;
	use PHPUnit\Framework\TestCase;

	class PDFProtectionTraitTest extends TestCase {
		public function testPDFProtectionTrait () {
			$pdf = new class extends FPDF {
				use PDFProtectionTrait;
			};

			$pdf->SetProtection(['print']);
			$pdf->AddPage();
			$pdf->SetFont('Arial');
			$pdf->Write(10, 'You can print me but not copy my text.');

			$filename = __DIR__ . '/example' . basename(__CLASS__) . '.pdf';
			$pdf->Output('F', $filename);

			$this->assertFileExists($filename);

			if (is_file($filename)) {
				unlink($filename);
			}
		}
	}
