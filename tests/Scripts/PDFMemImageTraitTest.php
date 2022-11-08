<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use FPDF;
	use FPDF\Scripts\PDFMemImage\PDFMemImageTrait;
	use Fawno\FPDF\Tests\TestCase;

	class PDFMemImageTraitTest extends TestCase {
		public function testPDFMemImageTrait () {
			$pdf = new class extends FPDF {
				use PDFMemImageTrait;
			};

			$pdf->AddPage();

			$logo = file_get_contents(dirname(dirname(__DIR__)) . '/scripts/PDFMemImage/logo.jpg');
			$pdf->MemImage($logo, 50, 30);
			$pdf->MemImage(base64_encode($logo), 85, 30);
			$im = imagecreate(200, 150);
			$bgcolor = imagecolorallocate($im, 255, 255, 255);
			$bordercolor = imagecolorallocate($im, 0, 0, 0);
			$color1 = imagecolorallocate($im, 255, 0, 0);
			$color2 = imagecolorallocate($im, 0, 255, 0);
			$color3 = imagecolorallocate($im, 0, 0, 255);
			imagefilledrectangle($im, 0, 0, 199, 149, $bgcolor);
			imagerectangle($im, 0, 0, 199, 149, $bordercolor);
			imagefilledrectangle($im, 30, 100, 60, 148, $color1);
			imagefilledrectangle($im, 80, 80, 110, 148, $color2);
			imagefilledrectangle($im, 130, 40, 160, 148, $color3);
			$pdf->GDImage($im, 120, 25, 40);
			imagedestroy($im);

			$this->assertFileCanBeCreated($pdf);

			$this->assertPdfIsOk($pdf);
		}
	}
