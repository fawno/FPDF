<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

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

			//$expected = file_get_contents(dirname(dirname(__DIR__)) . '/scripts/PDFProtection/ex.pdf');
			//$expected = file_get_contents(dirname(__DIR__) . '/examples/example' . basename(__CLASS__) . '.pdf');
			//$this->assertPdfAreEquals($expected, $pdf->Output('S'));
		}
	}
