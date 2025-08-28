<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use FPDF;
	use FPDF\Scripts\PDFTransform\PDFTransformTrait;
	use FPDF\Scripts\PDFCircularText\PDFCircularTextTrait;
	use Fawno\FPDF\Tests\TestCase;

	class PDFCircularTextTraitTest extends TestCase {
		public function testPDFCircularTextTrait () {
			$pdf = new class extends FPDF {
				use PDFTransformTrait;
				use PDFCircularTextTrait;
			};

			$pdf->AddPage();
			$pdf->SetFont('Arial', '', 32);

			$text='Circular Text';
			$pdf->CircularText(105, 50, 30, $text, 'top');
			$pdf->CircularText(105, 50, 30, $text, 'bottom');

			$this->assertFileCanBeCreated($pdf);

			$this->assertPdfIsOk($pdf);
		}
	}
