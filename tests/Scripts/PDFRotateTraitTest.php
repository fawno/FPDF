<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use FPDF;
	use FPDF\Scripts\PDFRotate\PDFRotateTrait;
	use FPDF\Scripts\PDFTransform\PDFTransformTrait;
	use Fawno\FPDF\Tests\TestCase;

	class PDFRotateTraitTest extends TestCase {
		public function testPDFRotateTrait () {
			$pdf = new class extends FPDF {
				use PDFTransformTrait;
				use PDFRotateTrait;
			};

			$pdf->AddPage();
			$pdf->SetFont('Arial','',20);
			$pdf->RotatedImage(dirname(dirname(__DIR__)) . '/scripts/PDFRotate/circle.png', 85, 60, 40, 16, 45);
			$pdf->RotatedText(100,60,'Hello!',45);

			$this->assertFileCanBeCreated($pdf);

			$this->assertPdfIsOk($pdf);
		}
	}
