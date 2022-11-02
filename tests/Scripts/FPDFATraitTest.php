<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use FPDF;
	use FPDF\Scripts\FPDFA\FPDFATrait;
	use PHPUnit\Framework\TestCase;

	define('FPDF_FONTPATH', dirname(__DIR__) . '/Fonts/');

	class FPDFATraitTest extends TestCase {
		public function testFPDFATrait () {
			$pdf = new class extends FPDF {
				use FPDFATrait;
			};

			$pdf->AddFont('DejaVuSansCondensed', '', 'DejaVuSansCondensed.php');
			$pdf->SetFont('DejaVuSansCondensed', '', 16);
			$pdf->AddPage();
			$pdf->Write(10, 'This PDF is PDF/A-3b compliant.');

			$filename = __DIR__ . '/example' . basename(__CLASS__) . '.pdf';
			$pdf->Output('F', $filename);

			$this->assertFileExists($filename);

			if (is_file($filename)) {
				unlink($filename);
			}
		}
	}
