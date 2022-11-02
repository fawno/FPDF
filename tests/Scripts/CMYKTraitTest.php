<?php
  declare(strict_types=1);

	namespace Fawno\FPDF\Tests\Scripts;

	use FPDF;
	use Fawno\FPDF\Traits\CMYKTrait;
	use PHPUnit\Framework\TestCase;

	class CMYKTraitTest extends TestCase {
		public function testCMYKTrait () {
			$pdf = new class extends FPDF {
				use CMYKTrait;
			};

			$pdf->AddPage();
			$pdf->SetFont('Arial', 'B', 20);
			$pdf->SetLineWidth(1);

			$pdf->SetDrawColor(50, 0, 0, 0);
			$pdf->SetFillColor(100, 0, 0, 0);
			$pdf->SetTextColor(100, 0, 0, 0);
			$pdf->Rect(10, 10, 20, 20, 'DF');
			$pdf->Text(10, 40, 'Cyan');

			$pdf->SetDrawColor(0, 50, 0, 0);
			$pdf->SetFillColor(0, 100, 0, 0);
			$pdf->SetTextColor(0, 100, 0, 0);
			$pdf->Rect(40, 10, 20, 20, 'DF');
			$pdf->Text(40, 40, 'Magenta');

			$pdf->SetDrawColor(0, 0, 50, 0);
			$pdf->SetFillColor(0, 0, 100, 0);
			$pdf->SetTextColor(0, 0, 100, 0);
			$pdf->Rect(70, 10, 20, 20, 'DF');
			$pdf->Text(70, 40, 'Yellow');

			$pdf->SetDrawColor(0, 0, 0, 50);
			$pdf->SetFillColor(0, 0, 0, 100);
			$pdf->SetTextColor(0, 0, 0, 100);
			$pdf->Rect(100, 10, 20, 20, 'DF');
			$pdf->Text(100, 40, 'Black');

			$pdf->SetDrawColor(128, 0, 0);
			$pdf->SetFillColor(255, 0, 0);
			$pdf->SetTextColor(255, 0, 0);
			$pdf->Rect(10, 50, 20, 20, 'DF');
			$pdf->Text(10, 80, 'Red');

			$pdf->SetDrawColor(0, 127, 0);
			$pdf->SetFillColor(0, 255, 0);
			$pdf->SetTextColor(0, 255, 0);
			$pdf->Rect(40, 50, 20, 20, 'DF');
			$pdf->Text(40, 80, 'Green');

			$pdf->SetDrawColor(0, 0, 127);
			$pdf->SetFillColor(0, 0, 255);
			$pdf->SetTextColor(0, 0, 255);
			$pdf->Rect(70, 50, 20, 20, 'DF');
			$pdf->Text(70, 80, 'Blue');

			$pdf->SetDrawColor(50);
			$pdf->SetFillColor(0);
			$pdf->SetTextColor(0);
			$pdf->Rect(10, 90, 20, 20, 'DF');
			$pdf->Text(10, 120, 'Gray');

			$filename = __DIR__ . '/example' . basename(__CLASS__) . '.pdf';
			$pdf->Output('F', $filename);

			$this->assertFileExists($filename);

			if (is_file($filename)) {
				unlink($filename);
			}
		}
	}
