<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use FPDF;
	use Fawno\FPDF\Tests\TestCase;
	use FPDF\Scripts\QRcode\QRcodeTrait;

	class QRcodeTraitTest extends TestCase {
		public function testQRcodeTrait () {
			$pdf = new class extends FPDF {
				use QRcodeTrait;
			};

			$pdf->AddPage();
			$pdf->QRcode(5, 5, 50, 'Generated QR Code Data');

			$this->assertFileCanBeCreated($pdf);

			$this->assertPdfIsOk($pdf);
		}
	}
