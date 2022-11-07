<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use FPDF;
	use FPDF\Scripts\PDFRotate\PDFRotateTrait;
	use Fawno\FPDF\Tests\TestCase;

	class PDFRotateTraitTest extends TestCase {
		public function testPDFRotateTrait () {
			$pdf = new class extends FPDF {
				use PDFRotateTrait;
			};

			$pdf->AddPage();
			$pdf->SetFont('Arial','',20);
			$pdf->RotatedImage(dirname(dirname(__DIR__)) . '/scripts/PDFRotate/circle.png', 85, 60, 40, 16, 45);
			$pdf->RotatedText(100,60,'Hello!',45);

			$this->assertFileCanBeCreated($pdf);

			//$expected = file_get_contents(dirname(dirname(__DIR__)) . '/scripts/PDFRotate/ex.pdf');
			$expected = file_get_contents(dirname(__DIR__) . '/examples/example' . basename(__CLASS__) . '.pdf');
			$this->assertPdfAreEquals($expected, $pdf->Output('S'));
		}
	}
