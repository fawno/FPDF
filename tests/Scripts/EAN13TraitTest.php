<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use FPDF;
	use Fawno\FPDF\Tests\TestCase;
	use Fawno\FPDF\Traits\BasicFunctionsTrait;
	use Fawno\FPDF\Traits\EAN13Trait;
	use FPDF\Scripts\PDFTransform\PDFTransformTrait;

	class EAN13TraitTest extends TestCase {
		public function testEAN13Trait () {
			$pdf = new class extends FPDF {
				use PDFTransformTrait;
				use BasicFunctionsTrait;
				use EAN13Trait;
			};

			$pdf->AddPage();
			$pdf->BarcodeEAN13(80, 40, '123456789012');

			$this->assertFileCanBeCreated($pdf);

			$this->assertPdfIsOk($pdf);
		}
	}
