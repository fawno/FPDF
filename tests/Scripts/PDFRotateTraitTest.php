<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use FPDF;
	use FPDF\Scripts\PDFRotate\PDFRotateTrait;
	use PHPUnit\Framework\TestCase;

	class PDFRotateTraitTest extends TestCase {
		public function testPDFRotateTrait () {
			$pdf = new class extends FPDF {
				use PDFRotateTrait;
			};

			$pdf->AddPage();
			$pdf->SetFont('Arial','',20);
			$pdf->RotatedImage(dirname(dirname(__DIR__)) . '/scripts/PDFRotate/circle.png', 85, 60, 40, 16, 45);
			$pdf->RotatedText(100,60,'Hello!',45);

			$filename = __DIR__ . '/example' . basename(__CLASS__) . '.pdf';
			$pdf->Output('F', $filename);

			$this->assertFileExists($filename);

			if (is_file($filename)) {
				unlink($filename);
			}
		}
	}
