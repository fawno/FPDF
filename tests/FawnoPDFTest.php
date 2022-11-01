<?php
	declare(strict_types=1);

	use Fawno\FPDF\FawnoFPDF;
	use PHPUnit\Framework\TestCase;
	use setasign\Fpdi\Fpdi;

	class strftimeTest extends TestCase {
		public function setUp () : void {
		}

		public function testFPDF () {
			$pdf = new FawnoFPDF();
			$this->assertInstanceOf(\FPDF::class, $pdf);

			if (class_exists('setasign\Fpdi\Fpdi')) {
				$this->assertInstanceOf(Fpdi::class, $pdf);
			}
		}

		public function testMultipleFawnoInstances () {
			$pdf1 = new FawnoFPDF();
			$pdf2 = new FawnoFPDF();
			$pdf3 = new \FPDF();
			if (class_exists('setasign\Fpdi\Fpdi')) {
				$pdf4 = new \setasign\Fpdi\Fpdi();
			}

			$this->assertEquals(get_class($pdf1), get_class($pdf2));
			$this->assertNotEquals(get_class($pdf1), get_class($pdf3));
		}

		public function testScripts () {
			include('test.php');
			$this->assertFileExists(dirname(__DIR__) . '/tests/example.pdf');
		}
	}
