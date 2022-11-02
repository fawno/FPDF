<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use FPDF;
	use Fawno\FPDF\Traits\PDFMacroableTrait;
	use PHPUnit\Framework\TestCase;

	class PDFMacroable extends FPDF {
		use PDFMacroableTrait;
	};

	class PDFMacroableTraitTest extends TestCase {
		public function testPDFMacroableTrait () {
			PDFMacroable::macro('SetDash', function($black = null, $white = null) {
				if ($black !== null) {
					$s = sprintf('[%.3F %.3F] 0 d', $black * $this->k, $white * $this->k);
				} else {
					$s = '[] 0 d';
				}

				$this->_out($s);
			});

			$pdf = new PDFMacroable();

			$pdf->AddPage();
			$pdf->SetLineWidth(0.1);
			$pdf->SetDash(5, 5); //5mm on, 5mm off
			$pdf->Line(20, 20, 190, 20);
			$pdf->SetLineWidth(0.5);
			$pdf->Line(20, 25, 190, 25);
			$pdf->SetLineWidth(0.8);
			$pdf->SetDash(4,2); //4mm on, 2mm off
			$pdf->Rect(20, 30, 170, 20);
			$pdf->SetDash(); //restores no dash
			$pdf->Line(20, 55, 190, 55);

			$filename = __DIR__ . '/example' . basename(__CLASS__) . '.pdf';
			$pdf->Output('F', $filename);

			$this->assertFileExists($filename);

			if (is_file($filename)) {
				unlink($filename);
			}
		}
	}
