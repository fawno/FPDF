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
	}
