<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use FPDF;
	use FPDF\Scripts\ESignaturePlaceholder\ESignaturePlaceholderTrait;
	use Fawno\FPDF\Tests\TestCase;

	class ESignaturePlaceholderTraitTest extends TestCase {
		public function testESignaturePlaceholderTrait () {
			$pdf = new class extends FPDF {
				use ESignaturePlaceholderTrait;
			};

			$pdf->AddSignatureField(20, 15, 120, 40);

			$this->assertFileCanBeCreated($pdf);

			$this->assertPdfIsOk($pdf);
		}
	}
