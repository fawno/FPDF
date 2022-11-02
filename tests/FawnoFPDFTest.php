<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests;

	use Fawno\FPDF\FawnoFPDF;
	use Fawno\FPDF\PDFWrapper;
	use FPDF;
	use setasign\Fpdi\Fpdi;
	use PHPUnit\Framework\TestCase;

	class FawnoFPDFTest extends TestCase {
		public function testFPDF () {
			$pdf = new FawnoFPDF();
			$this->assertInstanceOf(FPDF::class, $pdf);
		}

		public function testPDFWrapper () {
			$pdf = new FawnoFPDF();
			$this->assertInstanceOf(PDFWrapper::class, $pdf);
		}

		public function testFpdi () {
			if (!class_exists(Fpdi::class)) {
				$this->markTestSkipped(sprintf('Class "%s" is required.', Fpdi::class));
				return;
			}

			$pdf = new FawnoFPDF();
			$this->assertInstanceOf(Fpdi::class, $pdf);
		}

		public function testMultipleInstances () {
			$pdf1 = new FawnoFPDF();
			$pdf2 = new FawnoFPDF();
			$pdf3 = new FPDF();

			if (class_exists(Fpdi::class)) {
				$pdf4 = new Fpdi();
			} else {
				$pdf4 = new FPDF();
			}

			$this->assertEquals(get_class($pdf1), get_class($pdf2));
			$this->assertNotEquals(get_class($pdf1), get_class($pdf3));
			$this->assertNotEquals(get_class($pdf1), get_class($pdf4));
		}

		public function testFawnoFPDF () {
			include __DIR__ . '/test.php';

			$filename = __DIR__ . '/example.pdf';

			$this->assertFileExists($filename);

			if (is_file($filename)) {
				unlink($filename);
			}
		}
	}
